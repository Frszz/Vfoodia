<?php
    require __DIR__ . '/vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    date_default_timezone_set('Asia/Jakarta');
    session_start();

    $mode = $_ENV['APP_ENV'];

    function base_url () {
        global $mode;

        if ($mode == 'development') {
            return $_ENV['BASE_URL_DEV'];
        } else if ($mode == 'production') {
            return $_ENV['BASE_URL_PROD'];
        }
    }

    $googleAPIkey   = $_ENV['GOOGLE_API_KEY'];
    $heigitBASICkey = $_ENV['HEIGIT_BASIC_KEY'];
    $sourceMAP      = $_ENV['SOURCE_MAP'];

    $waAPIurl   = $_ENV['WA_API_URL'];

    $smtpHOST   = $_ENV['SMTP_HOST'];
    $smtpUSER   = $_ENV['SMTP_USER'];
    $smtpPASS   = $_ENV['SMTP_PASS'];
    $smtpPORT   = $_ENV['SMTP_PORT'];
    $smtpSENDER = $_ENV['SMTP_SENDER'];

    if ($mode == 'development') {
        $host = $_ENV['DB_HOST_DEV'];
        $user = $_ENV['DB_USER_DEV'];
        $pass = $_ENV['DB_PASS_DEV'];
        $db   = $_ENV['DB_NAME_DEV'];
        $port = $_ENV['DB_PORT_DEV'] ?? null;

        if (!empty($port) && $port != null) {
            $con = mysqli_connect($host, $user, $pass, $db, $port);
        } else {
            $con = mysqli_connect($host, $user, $pass, $db);
        }
    } else if ($mode == 'production') {
        $host = $_ENV['DB_HOST_PROD'];
        $user = $_ENV['DB_USER_PROD'];
        $pass = $_ENV['DB_PASS_PROD'];
        $db   = $_ENV['DB_NAME_PROD'];
        $port = $_ENV['DB_PORT_PROD'] ?? null;

        if (!empty($port) && $port != null) {
            $con = mysqli_connect($host, $user, $pass, $db, $port);
        } else {
            $con = mysqli_connect($host, $user, $pass, $db);
        }
    }

    mysqli_select_db($con, $db);

    function priceFormat ($a) {
        $b = number_format($a, 0, ',', '.');

        return $b;
    }

    function generateRandomString ($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    function indoDateFormat ($date) {
        if (empty($date)) {
            return null;
        }

        $timestamp = strtotime($date);
        if (!$timestamp) {
            return null;
        }

        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $timestamp = strtotime($date);
        $tgl = date('d', $timestamp);
        $bln = $bulan[(int)date('m', $timestamp)];
        $thn = date('Y', $timestamp);

        return "$tgl $bln $thn";
    }

    function sendWA ($wa_token, $target = [], $message = '') {
        global $waAPIurl;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $waAPIurl.'/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $target,
                'message' => $message,
                'typing' => false,
                'delay' => '2',
                'countryCode' => '62',
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: '.$wa_token
            ),
        ));
        $response = curl_exec($curl);
        echo "<script>console.log('Response: ".json_encode($response)."');</script>";
        if (curl_errno($curl)) {
            $error_message = curl_error($curl);
            echo "<script>console.log('Error: ".addslashes($error_message)."');</script>";
            curl_close($curl);
            return false;
        } else {
            curl_close($curl);
            return true;
        }

        return false;
    }

    function sendMail ($receiver, $subject, $message, $attachments = []) {
        global $smtpHOST;
        global $smtpUSER;
        global $smtpPASS;
        global $smtpPORT;
        global $smtpSENDER;

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $smtpHOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpUSER;
            $mail->Password   = $smtpPASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $smtpPORT;

            // Pengirim & Penerima
            $mail->setFrom($smtpUSER, $smtpSENDER);
            $mail->addAddress($receiver);

            // Konten Email
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            // Attachment
            foreach ($attachments as $attachment) {
                if (filter_var($attachment, FILTER_VALIDATE_URL)) {
                    $tempPath = tempnam(sys_get_temp_dir(), 'file');
                    file_put_contents($tempPath, file_get_contents($attachment));
                    $mail->addAttachment($tempPath, basename($attachment));
                } else if (strpos($attachment, 'data:') === 0) {
                    list($type, $data) = explode(';', $attachment);
                    list(, $data) = explode(',', $data);
                    $data = base64_decode($data);
                    $ext = explode('/', mime_content_type($attachment))[1];
                    $tempPath = tempnam(sys_get_temp_dir(), 'file') . ".$ext";
                    file_put_contents($tempPath, $data);
                    $mail->addAttachment($tempPath, "file.$ext");
                } else {
                    $mail->addAttachment($attachment);
                }
            }

            // Kirim Email
            $mail->send();
            echo "<script>console.log('Success');</script>";
            return true;
        } catch (Exception $e) {
            echo "<script>console.log('error: ".$e->getMessage()."');</script>";
            return false;
        }
    }

    function autoDirect ($action, $data = []) {
        $form = "<form id='autoSubmitForm' action='".htmlspecialchars($action)."' method='POST'>";
        foreach ($data as $name => $value) {
            $form .= "<input type='hidden' name='".htmlspecialchars($name)."' value='".htmlspecialchars($value)."'>";
        }
        $form .= "</form>
            <script>
                setTimeout(() => {
                    document.getElementById('autoSubmitForm').submit();
                }, 1000);
            </script>";
        echo $form;
    }

    function waTemplate ($c) {
        return "*---- VFOODIA ----*\n\n$c";
    }

    function mailTemplate ($c) {
        return "<b>---- VFOODIA ----</b><br><br>$c";
    }
?>