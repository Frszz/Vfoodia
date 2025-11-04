<?php
    require __DIR__ . '/../connect.php';
    require __DIR__ . '/../vendor/autoload.php';

    use Dompdf\Dompdf;
    use Dompdf\Options;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    function createPDF ($title, $content) {
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($content);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output PDF ke browser
        $dompdf->stream("$title.pdf", ["Attachment" => true]);
    }

    function createExcel ($title, $headers = [], $rows = [], $styleCallback = null) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if (!empty($headers)) {
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $col++;
            }
        }

        $startRow = empty($headers) ? 1 : 2;
        $rowIndex = $startRow;
        foreach ($rows as $row) {
            $col = 'A';
            foreach ($row as $cell) {
                $sheet->setCellValue($col . $rowIndex, $cell);
                $col++;
            }
            $rowIndex++;
        }

        if (is_callable($styleCallback)) {
            $styleCallback($sheet);
        }

        // Output Excel ke browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$title.xlsx\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    function templateInvoice ($content) {
        return '
            <html>
                <head>
                    <style>
                        body {
                            font-family: Helvetica, sans-serif;
                            font-size: 12px;
                            color: #333;
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 20px;
                        }
                        .header h2 {
                            margin: 0;
                        }
                        .info, .footer {
                            margin: 20px 0;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 10px;
                        }
                        table, th, td {
                            border: 1px solid #999;
                        }
                        th, td {
                            padding: 8px 10px;
                            text-align: left;
                        }
                        th {
                            background-color: #f0f0f0;
                        }
                        .total {
                            font-weight: bold;
                        }
                        .right {
                            text-align: right;
                        }
                    </style>
                </head>
                <body>'.$content.'</body>
            </html>
        ';
    }

    if (isset($_GET['type'])) {
        if ($_GET['type'] == 'sls' && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $qSLS = mysqli_query($con, "SELECT * FROM vw_sales WHERE id = $id LIMIT 1") or die (mysqli_error($con));
            $dataSLS = mysqli_fetch_array($qSLS);
            $content = templateInvoice('
                <div class="header">
                    <h2>FAKTUR PENJUALAN</h2>
                    <p>Kode Transaksi: <strong>#' . htmlspecialchars($dataSLS['code']) . '</strong></p>
                </div>
                <div class="info">
                    <table>
                        <tr>
                            <th>Nama Pelanggan</th>
                            <td>' . htmlspecialchars($dataSLS['customer_name']) . '</td>
                        </tr>
                        <tr>
                            <th>Periode</th>
                            <td>' . indoDateFormat($dataSLS['start_periode']) . ' - ' . indoDateFormat($dataSLS['end_periode']) . '</td>
                        </tr>
                    </table>
                </div>
                <div class="items">
                    <table>
                        <thead>
                            <tr>
                                <th>Deskripsi</th>
                                <th class="right">Qty/Box</th>
                                <th class="right">Total Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Penjualan Produk</td>
                                <td class="right">' . priceFormat($dataSLS['total_qty_box']) . '</td>
                                <td class="right">Rp' . priceFormat($dataSLS['total_price']) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="total right">Total</td>
                                <td class="total right">Rp' . priceFormat($dataSLS['total_price']) . '</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="footer">
                    <p style="text-align:right; margin-top:50px;">Tanda Tangan</p>
                </div>
            ');
            createPDF($dataSLS['code'], $content);
            exit;
        } else if ($_GET['type'] == 'sls_year_excel' && isset($_GET['start']) && isset($_GET['end'])) {
            $headers = ['Kode Penjualan', 'Kode Pelanggan', 'Nama Pelanggan', 'Waktu Transaksi', 'Periode', 'Total Qty/Box', 'Total Harga'];
            $rows = [];
            $styleMap = [];

            $start = (int)$_GET['start'];
            $end = (int)$_GET['end'];

            for ($year = $end; $year >= $start; $year--) {
                $startRow = count($rows) + 1;
                $rows[] = ["Tahun $year", '', '', '', ''];
                $styleMap[] = ['type' => 'year', 'row' => $startRow];

                $rows[] = $headers;
                $styleMap[] = ['row' => count($rows), 'type' => 'header'];

                $qSLS = mysqli_query($con, "
                    SELECT * FROM vw_sales 
                    WHERE YEAR(input_at) = $year
                    ORDER BY input_at
                ") or die (mysqli_error($con));

                $totalQty = 0;
                $totalPrice = 0;

                while ($dataSLS = mysqli_fetch_array($qSLS)) {
                    $qty = (int)$dataSLS['total_qty_box'];
                    $price = (float)$dataSLS['total_price'];

                    $rows[] = [
                        $dataSLS['code'],
                        $dataSLS['customer_code'],
                        $dataSLS['customer_name'],
                        $dataSLS['input_at'],
                        indoDateFormat($dataSLS['start_periode']) . '  -  ' . indoDateFormat($dataSLS['end_periode']),
                        priceFormat($qty),
                        'Rp' . priceFormat($price),
                    ];

                    $totalQty += $qty;
                    $totalPrice += $price;
                }

                $totalRow = count($rows) + 1;
                $rows[] = [
                    'Total', '', '', '', '', priceFormat($totalQty), 'Rp' . priceFormat($totalPrice)
                ];
                $styleMap[] = ['type' => 'total', 'row' => $totalRow];

                $rows[] = ['', '', '', '', '', '', ''];
            }

            createExcel('Laporan Penjualan_' . $_GET['start'] . '_' . $_GET['end'], [], $rows, function($sheet) use ($styleMap) {
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                for ($row = 1; $row <= $highestRow; $row++) {
                    $isEmptyRow = true;
                    foreach (range('A', $highestColumn) as $col) {
                        $value = $sheet->getCell("{$col}{$row}")->getValue();
                        if (trim((string)$value) !== '') {
                            $isEmptyRow = false;
                            break;
                        }
                    }
                    if (!$isEmptyRow) {
                        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                    'color' => ['argb' => '000000'],
                                ],
                            ],
                        ]);
                    }
                }

                foreach ($styleMap as $item) {
                    $row = $item['row'];
                    if ($item['type'] === 'year') {
                        $sheet->mergeCells("A{$row}:G{$row}");
                        $style = $sheet->getStyle("A{$row}");
                        $style->getFill()->setFillType('solid')->getStartColor()->setRGB('92D050');
                        $style->getFont()->setBold(true);
                        $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $style->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    } elseif ($item['type'] === 'total') {
                        $sheet->mergeCells("A{$row}:E{$row}");

                        $styleTotalLabel = $sheet->getStyle("A{$row}:E{$row}");
                        $styleTotalLabel->getFill()->setFillType('solid')->getStartColor()->setRGB('BDD7EE');
                        $styleTotalLabel->getFont()->setBold(true);
                        $styleTotalLabel->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $styleTotalLabel->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                        $styleTotalValue = $sheet->getStyle("F{$row}:G{$row}");
                        $styleTotalValue->getFill()->setFillType('solid')->getStartColor()->setRGB('BDD7EE');
                        $styleTotalValue->getFont()->setBold(true);
                    } elseif ($item['type'] === 'header') {
                        $style = $sheet->getStyle("A{$row}:G{$row}");
                        $style->getFill()->setFillType('solid')->getStartColor()->setRGB('F4B084');
                        $style->getFont()->setBold(true);
                        $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    }
                }

                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            });
            exit;
        } else if ($_GET['type'] == 'sls_year_pdf' && isset($_GET['start']) && isset($_GET['end'])) {
            $start = (int)$_GET['start'];
            $end = (int)$_GET['end'];

            $contentItems = '';

            for ($year = $end; $year >= $start; $year--) {
                $qSLS = mysqli_query($con, "
                    SELECT * FROM vw_sales 
                    WHERE YEAR(input_at) = $year
                    ORDER BY input_at
                ") or die(mysqli_error($con));

                $rows = '';
                $totalQty = 0;
                $totalPrice = 0;

                while ($dataSLS = mysqli_fetch_array($qSLS)) {
                    $qty = (int)$dataSLS['total_qty_box'];
                    $price = (float)$dataSLS['total_price'];

                    $rows .= '
                        <tr>
                            <td>' . $dataSLS['code'] . '</td>
                            <td>' . $dataSLS['customer_code'] . '</td>
                            <td>' . $dataSLS['customer_name'] . '</td>
                            <td>' . $dataSLS['input_at'] . '</td>
                            <td>' . indoDateFormat($dataSLS['start_periode']) . ' - ' . indoDateFormat($dataSLS['end_periode']) . '</td>
                            <td class="right">' . priceFormat($qty) . '</td>
                            <td class="right">Rp' . priceFormat($price) . '</td>
                        </tr>
                    ';

                    $totalQty += $qty;
                    $totalPrice += $price;
                }

                if ($rows === '') {
                    $rows = '
                        <tr>
                            <td colspan="7" style="text-align:center;font-style:italic;">Tidak ada data penjualan di tahun ' . $year . '</td>
                        </tr>
                    ';
                }

                $contentItems .= '
                    <div class="items">
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="7" style="text-align:left;background:#d4f4dd;">Tahun ' . $year . '</th>
                                </tr>
                                <tr>
                                    <th>Kode Penjualan</th>
                                    <th>Kode Pelanggan</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Waktu Transaksi</th>
                                    <th>Periode</th>
                                    <th class="right">Qty/Box</th>
                                    <th class="right">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                ' . $rows . '
                                <tr>
                                    <td colspan="5" class="total right">Total</td>
                                    <td class="total right">' . priceFormat($totalQty) . '</td>
                                    <td class="total right">Rp' . priceFormat($totalPrice) . '</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                ';
            }

            $content = templateInvoice('
                <div class="header">
                    <h2>LAPORAN PENJUALAN TAHUNAN</h2>
                </div>
                <div class="info">
                    <table>
                        <tr>
                            <th>Tahun</th>
                            <td>' . $start . ' - ' . $end . '</td>
                        </tr>
                    </table>
                </div>
                ' . $contentItems . '
            ');

            createPDF('Laporan Penjualan_' . $_GET['start'] . '_' . $_GET['end'], $content);
            exit;
        } else if ($_GET['type'] == 'sls_month_excel' && isset($_GET['month_year'])) {
            $headers = ['Kode Penjualan', 'Kode Pelanggan', 'Nama Pelanggan', 'Waktu Transaksi', 'Periode', 'Total Qty/Box', 'Total Harga'];
            $rows = [];
            $styleMap = [];

            $year = (int)$_GET['month_year'];
            $monthList = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            foreach ($monthList as $month => $monthName) {
                $startRow = count($rows) + 1;
                $rows[] = ["Bulan $monthName $year", '', '', '', ''];
                $styleMap[] = ['type' => 'year', 'row' => $startRow];

                $rows[] = $headers;
                $styleMap[] = ['row' => count($rows), 'type' => 'header'];

                $qSLS = mysqli_query($con, "
                    SELECT * FROM vw_sales 
                    WHERE YEAR(input_at) = $year AND MONTH(input_at) = $month
                    ORDER BY input_at
                ") or die (mysqli_error($con));

                $totalQty = 0;
                $totalPrice = 0;

                while ($dataSLS = mysqli_fetch_array($qSLS)) {
                    $qty = (int)$dataSLS['total_qty_box'];
                    $price = (float)$dataSLS['total_price'];

                    $rows[] = [
                        $dataSLS['code'],
                        $dataSLS['customer_code'],
                        $dataSLS['customer_name'],
                        $dataSLS['input_at'],
                        indoDateFormat($dataSLS['start_periode']) . ' - ' . indoDateFormat($dataSLS['end_periode']),
                        $qty,
                        'Rp' . priceFormat($price),
                    ];

                    $totalQty += $qty;
                    $totalPrice += $price;
                }

                $totalRow = count($rows) + 1;
                $rows[] = ['Total', '', '', '', '', $totalQty, 'Rp' . priceFormat($totalPrice)];
                $styleMap[] = ['type' => 'total', 'row' => $totalRow];

                $rows[] = ['', '', '', '', '', '', ''];
            }

            createExcel('Laporan Penjualan_' . $year, [], $rows, function($sheet) use ($styleMap) {
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                for ($row = 1; $row <= $highestRow; $row++) {
                    $isEmptyRow = true;
                    foreach (range('A', $highestColumn) as $col) {
                        $value = $sheet->getCell("{$col}{$row}")->getValue();
                        if (trim((string)$value) !== '') {
                            $isEmptyRow = false;
                            break;
                        }
                    }

                    if (!$isEmptyRow) {
                        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                    'color' => ['argb' => '000000'],
                                ],
                            ],
                        ]);
                    }
                }

                foreach ($styleMap as $item) {
                    $row = $item['row'];
                    if ($item['type'] === 'year') {
                        $sheet->mergeCells("A{$row}:G{$row}");
                        $style = $sheet->getStyle("A{$row}");
                        $style->getFill()->setFillType('solid')->getStartColor()->setRGB('92D050');
                        $style->getFont()->setBold(true);
                        $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $style->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    } elseif ($item['type'] === 'total') {
                        $sheet->mergeCells("A{$row}:E{$row}");

                        $styleTotalLabel = $sheet->getStyle("A{$row}:E{$row}");
                        $styleTotalLabel->getFill()->setFillType('solid')->getStartColor()->setRGB('BDD7EE');
                        $styleTotalLabel->getFont()->setBold(true);
                        $styleTotalLabel->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $styleTotalLabel->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                        $styleTotalValue = $sheet->getStyle("F{$row}:G{$row}");
                        $styleTotalValue->getFill()->setFillType('solid')->getStartColor()->setRGB('BDD7EE');
                        $styleTotalValue->getFont()->setBold(true);
                    } elseif ($item['type'] === 'header') {
                        $style = $sheet->getStyle("A{$row}:G{$row}");
                        $style->getFill()->setFillType('solid')->getStartColor()->setRGB('F4B084');
                        $style->getFont()->setBold(true);
                        $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    }
                }

                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            });
            exit;
        } else if ($_GET['type'] == 'sls_month_pdf' && isset($_GET['month_year'])) {
            $year = (int)$_GET['month_year'];

            $contentItems = '';
            $monthList = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            foreach ($monthList as $month => $monthName) {
                $qSLS = mysqli_query($con, "
                    SELECT * FROM vw_sales 
                    WHERE YEAR(input_at) = $year AND MONTH(input_at) = $month
                    ORDER BY input_at
                ") or die(mysqli_error($con));

                $rows = '';
                $totalQty = 0;
                $totalPrice = 0;

                while ($dataSLS = mysqli_fetch_array($qSLS)) {
                    $qty = (int)$dataSLS['total_qty_box'];
                    $price = (float)$dataSLS['total_price'];

                    $rows .= '
                        <tr>
                            <td>' . $dataSLS['code'] . '</td>
                            <td>' . $dataSLS['customer_code'] . '</td>
                            <td>' . $dataSLS['customer_name'] . '</td>
                            <td>' . $dataSLS['input_at'] . '</td>
                            <td>' . indoDateFormat($dataSLS['start_periode']) . ' - ' . indoDateFormat($dataSLS['end_periode']) . '</td>
                            <td class="right">' . priceFormat($qty) . '</td>
                            <td class="right">Rp' . priceFormat($price) . '</td>
                        </tr>
                    ';

                    $totalQty += $qty;
                    $totalPrice += $price;
                }

                if ($rows === '') {
                    $rows = '
                        <tr>
                            <td colspan="7" style="text-align:center;font-style:italic;">Tidak ada data penjualan di bulan ' . $monthName . '</td>
                        </tr>
                    ';
                }

                $contentItems .= '
                    <div class="items">
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="7" style="text-align:left;background:#d4f4dd;">Bulan ' . $monthName . '</th>
                                </tr>
                                <tr>
                                    <th>Kode Penjualan</th>
                                    <th>Kode Pelanggan</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Waktu Transaksi</th>
                                    <th>Periode</th>
                                    <th class="right">Qty/Box</th>
                                    <th class="right">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                ' . $rows . '
                                <tr>
                                    <td colspan="5" class="total right">Total</td>
                                    <td class="total right">' . priceFormat($totalQty) . '</td>
                                    <td class="total right">Rp' . priceFormat($totalPrice) . '</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                ';
            }

            $content = templateInvoice('
                <div class="header">
                    <h2>LAPORAN PENJUALAN BULANAN</h2>
                </div>
                <div class="info">
                    <table>
                        <tr>
                            <th>Tahun</th>
                            <td>' . $year . '</td>
                        </tr>
                    </table>
                </div>
                ' . $contentItems . '
            ');

            createPDF('Laporan Penjualan_' . $year, $content);
            exit;
        } else if ($_GET['type'] == 'sls_date_excel' && isset($_GET['day_year']) && isset($_GET['day_month'])) {
            $headers = ['Kode Penjualan', 'Kode Pelanggan', 'Nama Pelanggan', 'Waktu Transaksi', 'Periode', 'Total Qty/Box', 'Total Harga'];
            $rows = [];
            $styleMap = [];

            $year = (int)$_GET['day_year'];
            $month = (int)$_GET['day_month'];
            
            $rows[] = ["Bulan {$month} Tahun {$year}", '', '', '', ''];
            $styleMap[] = ['type' => 'title', 'row' => 1];

            $rows[] = $headers;
            $styleMap[] = ['type' => 'header', 'row' => 2];

            $qSLS = mysqli_query($con, "
                SELECT * FROM vw_sales 
                WHERE YEAR(input_at) = $year AND MONTH(input_at) = $month
                ORDER BY input_at ASC
            ") or die (mysqli_error($con));

            $totalQty = 0;
            $totalPrice = 0;

            while ($dataSLS = mysqli_fetch_array($qSLS)) {
                $qty = (int)$dataSLS['total_qty_box'];
                $price = (float)$dataSLS['total_price'];

                $rows[] = [
                    $dataSLS['code'],
                    $dataSLS['customer_code'],
                    $dataSLS['customer_name'],
                    $dataSLS['input_at'],
                    indoDateFormat($dataSLS['start_periode']) . ' - ' . indoDateFormat($dataSLS['end_periode']),
                    $qty,
                    'Rp' . priceFormat($price),
                ];

                $totalQty += $qty;
                $totalPrice += $price;
            }

            $totalRow = count($rows) + 1;
            $rows[] = ['Total', '', '', '', '', $totalQty, 'Rp' . priceFormat($totalPrice)];
            $styleMap[] = ['type' => 'total', 'row' => $totalRow];

            createExcel("Laporan_Penjualan_{$year}_{$month}", [], $rows, function($sheet) use ($styleMap) {
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                for ($row = 1; $row <= $highestRow; $row++) {
                    $isEmptyRow = true;
                    foreach (range('A', $highestColumn) as $col) {
                        $value = $sheet->getCell("{$col}{$row}")->getValue();
                        if (trim((string)$value) !== '') {
                            $isEmptyRow = false;
                            break;
                        }
                    }
                    if (!$isEmptyRow) {
                        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                    'color' => ['argb' => '000000'],
                                ],
                            ],
                        ]);
                    }
                }

                foreach ($styleMap as $item) {
                    $row = $item['row'];
                    if ($item['type'] === 'title') {
                        $sheet->mergeCells("A{$row}:G{$row}");
                        $style = $sheet->getStyle("A{$row}");
                        $style->getFill()->setFillType('solid')->getStartColor()->setRGB('92D050');
                        $style->getFont()->setBold(true);
                        $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    } elseif ($item['type'] === 'header') {
                        $style = $sheet->getStyle("A{$row}:G{$row}");
                        $style->getFill()->setFillType('solid')->getStartColor()->setRGB('F4B084');
                        $style->getFont()->setBold(true);
                        $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    } elseif ($item['type'] === 'total') {
                        $sheet->mergeCells("A{$row}:E{$row}");

                        $styleLabel = $sheet->getStyle("A{$row}:E{$row}");
                        $styleLabel->getFill()->setFillType('solid')->getStartColor()->setRGB('BDD7EE');
                        $styleLabel->getFont()->setBold(true);
                        $styleLabel->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                        $styleValue = $sheet->getStyle("F{$row}:G{$row}");
                        $styleValue->getFill()->setFillType('solid')->getStartColor()->setRGB('BDD7EE');
                        $styleValue->getFont()->setBold(true);
                    }
                }

                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            });
            exit;
        } else if ($_GET['type'] == 'sls_date_pdf' && isset($_GET['day_year']) && isset($_GET['day_month'])) {
            $year = (int)$_GET['day_year'];
            $month = (int)$_GET['day_month'];

            $monthList = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            $monthName = $monthList[$month] ?? '-';

            $qSLS = mysqli_query($con, "
                SELECT * FROM vw_sales 
                WHERE YEAR(input_at) = $year AND MONTH(input_at) = $month
                ORDER BY input_at ASC
            ") or die(mysqli_error($con));

            $rows = '';
            $totalQty = 0;
            $totalPrice = 0;

            while ($dataSLS = mysqli_fetch_array($qSLS)) {
                $qty = (int)$dataSLS['total_qty_box'];
                $price = (float)$dataSLS['total_price'];

                $rows .= '
                    <tr>
                        <td>' . $dataSLS['code'] . '</td>
                        <td>' . $dataSLS['customer_code'] . '</td>
                        <td>' . $dataSLS['customer_name'] . '</td>
                        <td>' . $dataSLS['input_at'] . '</td>
                        <td>' . indoDateFormat($dataSLS['start_periode']) . ' - ' . indoDateFormat($dataSLS['end_periode']) . '</td>
                        <td class="right">' . priceFormat($qty) . '</td>
                        <td class="right">Rp' . priceFormat($price) . '</td>
                    </tr>
                ';

                $totalQty += $qty;
                $totalPrice += $price;
            }

            if ($rows === '') {
                $rows = '
                    <tr>
                        <td colspan="7" style="text-align:center;font-style:italic;">
                            Tidak ada data penjualan di bulan ' . $monthName . '
                        </td>
                    </tr>
                ';
            } else {
                $rows .= '
                    <tr>
                        <td colspan="5" class="total right">Total</td>
                        <td class="total right">' . priceFormat($totalQty) . '</td>
                        <td class="total right">Rp' . priceFormat($totalPrice) . '</td>
                    </tr>
                ';
            }

            $content = templateInvoice('
                <div class="header">
                    <h2>LAPORAN PENJUALAN HARIAN ' . strtoupper($monthName) . ' ' . $year . '</h2>
                </div>
                <div class="info">
                    <table>
                        <tr>
                            <th>Bulan</th>
                            <td>' . $monthName . '</td>
                        </tr>
                        <tr>
                            <th>Tahun</th>
                            <td>' . $year . '</td>
                        </tr>
                    </table>
                </div>
                <div class="items">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode Penjualan</th>
                                <th>Kode Pelanggan</th>
                                <th>Nama Pelanggan</th>
                                <th>Waktu Transaksi</th>
                                <th>Periode</th>
                                <th class="right">Qty/Box</th>
                                <th class="right">Total Harga</th>
                            </tr>
                        </thead>
                        <tbody>' . $rows . '</tbody>
                    </table>
                </div>
            ');

            createPDF('Laporan Penjualan_' . $monthName . '_' . $year, $content);
            exit;
        } else if ($_GET['type'] == 'dlv_year_excel' && isset($_GET['start']) && isset($_GET['end'])) {
            $headers = ['Kode Pengantaran', 'Kode Penjualan', 'Kode Kurir', 'Nama Kurir', 'No. Plat Kendaraan', 'Kode Pelanggan', 'Nama Pelanggan', 'Alamat Pelanggan', 'Waktu Berangkat', 'Waktu Tiba', 'Status', 'Qty/Box'];
            $rows = [];
            $styleMap = [];

            $start = (int)$_GET['start'];
            $end = (int)$_GET['end'];

            for ($year = $end; $year >= $start; $year--) {
                $startRow = count($rows) + 1;
                $rows[] = ["Tahun $year", '', '', '', ''];
                $styleMap[] = ['type' => 'year', 'row' => $startRow];

                $rows[] = $headers;
                $styleMap[] = ['row' => count($rows), 'type' => 'header'];

                $qDLV = mysqli_query($con, "
                    SELECT * FROM vw_delivery 
                    WHERE YEAR(schedule_date) = $year
                    ORDER BY schedule_date
                ") or die (mysqli_error($con));

                $totalQty = 0;

                while ($dataDLV = mysqli_fetch_array($qDLV)) {
                    $qty = (int)$dataDLV['qty_box'];

                    $rows[] = [
                        $dataDLV['code'],
                        $dataDLV['sales_code'],
                        $dataDLV['kurir_code'],
                        $dataDLV['kurir_name'],
                        $dataDLV['no_vehicle'],
                        $dataDLV['customer_code'],
                        $dataDLV['customer_name'],
                        $dataDLV['customer_address'],
                        $dataDLV['departure_time'],
                        $dataDLV['arrival_time'],
                        $dataDLV['status'],
                        priceFormat($dataDLV['qty_box'])
                    ];

                    $totalQty += $qty;
                }

                $totalRow = count($rows) + 1;
                $rows[] = [
                    'Total', '', '', '', '', '', '', '', '', '', '', priceFormat($totalQty)
                ];
                $styleMap[] = ['type' => 'total', 'row' => $totalRow];

                $rows[] = ['', '', '', '', '', '', '', '', '', '', '', ''];
            }

            createExcel('Laporan Pengantaran_' . $_GET['start'] . '_' . $_GET['end'], [], $rows, function($sheet) use ($styleMap) {
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                for ($row = 1; $row <= $highestRow; $row++) {
                    $isEmptyRow = true;
                    foreach (range('A', $highestColumn) as $col) {
                        $value = $sheet->getCell("{$col}{$row}")->getValue();
                        if (trim((string)$value) !== '') {
                            $isEmptyRow = false;
                            break;
                        }
                    }
                    if (!$isEmptyRow) {
                        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                    'color' => ['argb' => '000000'],
                                ],
                            ],
                        ]);
                    }
                }

                foreach ($styleMap as $item) {
                    $row = $item['row'];
                    if ($item['type'] === 'year') {
                        $sheet->mergeCells("A{$row}:L{$row}");
                        $style = $sheet->getStyle("A{$row}");
                        $style->getFill()->setFillType('solid')->getStartColor()->setRGB('92D050');
                        $style->getFont()->setBold(true);
                        $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $style->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    } elseif ($item['type'] === 'total') {
                        $sheet->mergeCells("A{$row}:K{$row}");

                        $styleTotalLabel = $sheet->getStyle("A{$row}:K{$row}");
                        $styleTotalLabel->getFill()->setFillType('solid')->getStartColor()->setRGB('BDD7EE');
                        $styleTotalLabel->getFont()->setBold(true);
                        $styleTotalLabel->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $styleTotalLabel->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                        $styleTotalValue = $sheet->getStyle("L{$row}");
                        $styleTotalValue->getFill()->setFillType('solid')->getStartColor()->setRGB('BDD7EE');
                        $styleTotalValue->getFont()->setBold(true);
                    } elseif ($item['type'] === 'header') {
                        $style = $sheet->getStyle("A{$row}:L{$row}");
                        $style->getFill()->setFillType('solid')->getStartColor()->setRGB('F4B084');
                        $style->getFont()->setBold(true);
                        $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    }
                }

                foreach (range('A', 'L') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            });
            exit;
        } else if ($_GET['type'] == 'dlv_year_pdf' && isset($_GET['start']) && isset($_GET['end'])) {
            $start = (int)$_GET['start'];
            $end = (int)$_GET['end'];

            $contentItems = '';

            for ($year = $end; $year >= $start; $year--) {
                $qDLV = mysqli_query($con, "
                    SELECT * FROM vw_delivery 
                    WHERE YEAR(schedule_date) = $year
                    ORDER BY schedule_date
                ") or die(mysqli_error($con));

                $rows = '';
                $totalQty = 0;

                while ($dataDLV = mysqli_fetch_array($qDLV)) {
                    $qty = (int)$dataDLV['qty_box'];

                    $rows .= '
                        <tr>
                            <td>' . $dataDLV['code'] . '</td>
                            <td>' . $dataDLV['kurir_code'] . '</td>
                            <td>' . $dataDLV['kurir_name'] . '</td>
                            <td>' . $dataDLV['customer_code'] . '</td>
                            <td>' . $dataDLV['customer_name'] . '</td>
                            <td>' . $dataDLV['status'] . '</td>
                            <td class="right">' . priceFormat($qty) . '</td>
                        </tr>
                    ';

                    $totalQty += $qty;
                }

                if ($rows === '') {
                    $rows = '
                        <tr>
                            <td colspan="7" style="text-align:center;font-style:italic;">Tidak ada data pengantaran di tahun ' . $year . '</td>
                        </tr>
                    ';
                }

                $contentItems .= '
                    <div class="items">
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="7" style="text-align:left;background:#d4f4dd;">Tahun ' . $year . '</th>
                                </tr>
                                <tr>
                                    <th>Kode Pengantaran</th>
                                    <th>Kode Kurir</th>
                                    <th>Nama Kurir</th>
                                    <th>Kode Pelanggan</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Status</th>
                                    <th class="right">Qty/Box</th>
                                </tr>
                            </thead>
                            <tbody>
                                ' . $rows . '
                                <tr>
                                    <td colspan="6" class="total right">Total</td>
                                    <td class="total right">' . priceFormat($totalQty) . '</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                ';
            }

            $content = templateInvoice('
                <div class="header">
                    <h2>LAPORAN PENGANTARAN TAHUNAN</h2>
                </div>
                <div class="info">
                    <table>
                        <tr>
                            <th>Tahun</th>
                            <td>' . $start . ' - ' . $end . '</td>
                        </tr>
                    </table>
                </div>
                ' . $contentItems . '
            ');

            createPDF('Laporan Pengantaran_' . $_GET['start'] . '_' . $_GET['end'], $content);
            exit;
        } else if ($_GET['type'] == 'dlv_month_excel' && isset($_GET['month_year'])) {
            $headers = ['Kode Pengantaran', 'Kode Penjualan', 'Kode Kurir', 'Nama Kurir', 'No. Plat Kendaraan', 'Kode Pelanggan', 'Nama Pelanggan', 'Alamat Pelanggan', 'Waktu Berangkat', 'Waktu Tiba', 'Status', 'Qty/Box'];
            $rows = [];
            $styleMap = [];

            $year = (int)$_GET['month_year'];
            $monthList = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            foreach ($monthList as $month => $monthName) {
                $startRow = count($rows) + 1;
                $rows[] = ["Bulan $monthName $year", '', '', '', ''];
                $styleMap[] = ['type' => 'year', 'row' => $startRow];

                $rows[] = $headers;
                $styleMap[] = ['row' => count($rows), 'type' => 'header'];

                $qDLV = mysqli_query($con, "
                    SELECT * FROM vw_delivery 
                    WHERE YEAR(schedule_date) = $year AND MONTH(schedule_date) = $month
                    ORDER BY schedule_date
                ") or die (mysqli_error($con));

                $totalQty = 0;

                while ($dataDLV = mysqli_fetch_array($qDLV)) {
                    $qty = (int)$dataDLV['qty_box'];

                    $rows[] = [
                        $dataDLV['code'],
                        $dataDLV['sales_code'],
                        $dataDLV['kurir_code'],
                        $dataDLV['kurir_name'],
                        $dataDLV['no_vehicle'],
                        $dataDLV['customer_code'],
                        $dataDLV['customer_name'],
                        $dataDLV['customer_address'],
                        $dataDLV['departure_time'],
                        $dataDLV['arrival_time'],
                        $dataDLV['status'],
                        priceFormat($dataDLV['qty_box'])
                    ];

                    $totalQty += $qty;
                }

                $totalRow = count($rows) + 1;
                $rows[] = ['Total', '', '', '', '', '', '', '', '', '', '', priceFormat($totalQty)];
                $styleMap[] = ['type' => 'total', 'row' => $totalRow];

                $rows[] = ['', '', '', '', '', '', '', '', '', '', '', ''];
            }

            createExcel('Laporan Pengantaran_' . $year, [], $rows, function($sheet) use ($styleMap) {
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                for ($row = 1; $row <= $highestRow; $row++) {
                    $isEmptyRow = true;
                    foreach (range('A', $highestColumn) as $col) {
                        $value = $sheet->getCell("{$col}{$row}")->getValue();
                        if (trim((string)$value) !== '') {
                            $isEmptyRow = false;
                            break;
                        }
                    }

                    if (!$isEmptyRow) {
                        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                    'color' => ['argb' => '000000'],
                                ],
                            ],
                        ]);
                    }
                }

                foreach ($styleMap as $item) {
                    $row = $item['row'];
                    if ($item['type'] === 'year') {
                        $sheet->mergeCells("A{$row}:L{$row}");
                        $style = $sheet->getStyle("A{$row}");
                        $style->getFill()->setFillType('solid')->getStartColor()->setRGB('92D050');
                        $style->getFont()->setBold(true);
                        $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $style->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    } elseif ($item['type'] === 'total') {
                        $sheet->mergeCells("A{$row}:K{$row}");

                        $styleTotalLabel = $sheet->getStyle("A{$row}:K{$row}");
                        $styleTotalLabel->getFill()->setFillType('solid')->getStartColor()->setRGB('BDD7EE');
                        $styleTotalLabel->getFont()->setBold(true);
                        $styleTotalLabel->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $styleTotalLabel->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                        $styleTotalValue = $sheet->getStyle("L{$row}");
                        $styleTotalValue->getFill()->setFillType('solid')->getStartColor()->setRGB('BDD7EE');
                        $styleTotalValue->getFont()->setBold(true);
                    } elseif ($item['type'] === 'header') {
                        $style = $sheet->getStyle("A{$row}:L{$row}");
                        $style->getFill()->setFillType('solid')->getStartColor()->setRGB('F4B084');
                        $style->getFont()->setBold(true);
                        $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    }
                }

                foreach (range('A', 'L') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            });
            exit;
        } else if ($_GET['type'] == 'dlv_month_pdf' && isset($_GET['month_year'])) {
            $year = (int)$_GET['month_year'];

            $contentItems = '';
            $monthList = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            foreach ($monthList as $month => $monthName) {
                $qDLV = mysqli_query($con, "
                    SELECT * FROM vw_delivery 
                    WHERE YEAR(schedule_date) = $year AND MONTH(schedule_date) = $month
                    ORDER BY schedule_date
                ") or die(mysqli_error($con));

                $rows = '';
                $totalQty = 0;

                while ($dataDLV = mysqli_fetch_array($qDLV)) {
                    $qty = (int)$dataDLV['qty_box'];

                    $rows .= '
                        <tr>
                            <td>' . $dataDLV['code'] . '</td>
                            <td>' . $dataDLV['kurir_code'] . '</td>
                            <td>' . $dataDLV['kurir_name'] . '</td>
                            <td>' . $dataDLV['customer_code'] . '</td>
                            <td>' . $dataDLV['customer_name'] . '</td>
                            <td>' . $dataDLV['status'] . '</td>
                            <td class="right">' . priceFormat($qty) . '</td>
                        </tr>
                    ';

                    $totalQty += $qty;
                }

                if ($rows === '') {
                    $rows = '
                        <tr>
                            <td colspan="7" style="text-align:center;font-style:italic;">Tidak ada data pengantaran di bulan ' . $monthName . '</td>
                        </tr>
                    ';
                }

                $contentItems .= '
                    <div class="items">
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="7" style="text-align:left;background:#d4f4dd;">Bulan ' . $monthName . '</th>
                                </tr>
                                <tr>
                                    <th>Kode Pengantaran</th>
                                    <th>Kode Kurir</th>
                                    <th>Nama Kurir</th>
                                    <th>Kode Pelanggan</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Status</th>
                                    <th class="right">Qty/Box</th>
                                </tr>
                            </thead>
                            <tbody>
                                ' . $rows . '
                                <tr>
                                    <td colspan="6" class="total right">Total</td>
                                    <td class="total right">' . priceFormat($totalQty) . '</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                ';
            }

            $content = templateInvoice('
                <div class="header">
                    <h2>LAPORAN PENGANTARAN BULANAN</h2>
                </div>
                <div class="info">
                    <table>
                        <tr>
                            <th>Tahun</th>
                            <td>' . $year . '</td>
                        </tr>
                    </table>
                </div>
                ' . $contentItems . '
            ');

            createPDF('Laporan Pengantaran_' . $year, $content);
            exit;
        } else if ($_GET['type'] == 'dlv_date_excel' && isset($_GET['day_year']) && isset($_GET['day_month'])) {
            $headers = ['Kode Pengantaran', 'Kode Penjualan', 'Kode Kurir', 'Nama Kurir', 'No. Plat Kendaraan', 'Kode Pelanggan', 'Nama Pelanggan', 'Alamat Pelanggan', 'Waktu Berangkat', 'Waktu Tiba', 'Status', 'Qty/Box'];
            $rows = [];
            $styleMap = [];

            $year = (int)$_GET['day_year'];
            $month = (int)$_GET['day_month'];
            
            $rows[] = ["Bulan {$month} Tahun {$year}", '', '', '', ''];
            $styleMap[] = ['type' => 'title', 'row' => 1];

            $rows[] = $headers;
            $styleMap[] = ['type' => 'header', 'row' => 2];

            $qDLV = mysqli_query($con, "
                SELECT * FROM vw_delivery 
                WHERE YEAR(schedule_date) = $year AND MONTH(schedule_date) = $month
                ORDER BY schedule_date ASC
            ") or die (mysqli_error($con));

            $totalQty = 0;

            while ($dataDLV = mysqli_fetch_array($qDLV)) {
                $qty = (int)$dataDLV['qty_box'];

                $rows[] = [
                    $dataDLV['code'],
                    $dataDLV['sales_code'],
                    $dataDLV['kurir_code'],
                    $dataDLV['kurir_name'],
                    $dataDLV['no_vehicle'],
                    $dataDLV['customer_code'],
                    $dataDLV['customer_name'],
                    $dataDLV['customer_address'],
                    $dataDLV['departure_time'],
                    $dataDLV['arrival_time'],
                    $dataDLV['status'],
                    priceFormat($dataDLV['qty_box'])
                ];

                $totalQty += $qty;
            }

            $totalRow = count($rows) + 1;
            $rows[] = ['Total', '', '', '', '', '', '', '', '', '', '', priceFormat($totalQty)];
            $styleMap[] = ['type' => 'total', 'row' => $totalRow];

            createExcel("Laporan_Pengantaran_{$year}_{$month}", [], $rows, function($sheet) use ($styleMap) {
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                for ($row = 1; $row <= $highestRow; $row++) {
                    $isEmptyRow = true;
                    foreach (range('A', $highestColumn) as $col) {
                        $value = $sheet->getCell("{$col}{$row}")->getValue();
                        if (trim((string)$value) !== '') {
                            $isEmptyRow = false;
                            break;
                        }
                    }
                    if (!$isEmptyRow) {
                        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                    'color' => ['argb' => '000000'],
                                ],
                            ],
                        ]);
                    }
                }

                foreach ($styleMap as $item) {
                    $row = $item['row'];
                    if ($item['type'] === 'title') {
                        $sheet->mergeCells("A{$row}:L{$row}");
                        $style = $sheet->getStyle("A{$row}");
                        $style->getFill()->setFillType('solid')->getStartColor()->setRGB('92D050');
                        $style->getFont()->setBold(true);
                        $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    } elseif ($item['type'] === 'header') {
                        $style = $sheet->getStyle("A{$row}:L{$row}");
                        $style->getFill()->setFillType('solid')->getStartColor()->setRGB('F4B084');
                        $style->getFont()->setBold(true);
                        $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    } elseif ($item['type'] === 'total') {
                        $sheet->mergeCells("A{$row}:K{$row}");

                        $styleLabel = $sheet->getStyle("A{$row}:K{$row}");
                        $styleLabel->getFill()->setFillType('solid')->getStartColor()->setRGB('BDD7EE');
                        $styleLabel->getFont()->setBold(true);
                        $styleLabel->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                        $styleValue = $sheet->getStyle("L{$row}");
                        $styleValue->getFill()->setFillType('solid')->getStartColor()->setRGB('BDD7EE');
                        $styleValue->getFont()->setBold(true);
                    }
                }

                foreach (range('A', 'L') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            });
            exit;
        } else if ($_GET['type'] == 'dlv_date_pdf' && isset($_GET['day_year']) && isset($_GET['day_month'])) {
            $year = (int)$_GET['day_year'];
            $month = (int)$_GET['day_month'];

            $monthList = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            $monthName = $monthList[$month] ?? '-';

            $qDLV = mysqli_query($con, "
                SELECT * FROM vw_delivery 
                WHERE YEAR(schedule_date) = $year AND MONTH(schedule_date) = $month
                ORDER BY schedule_date ASC
            ") or die(mysqli_error($con));

            $rows = '';
            $totalQty = 0;

            while ($dataDLV = mysqli_fetch_array($qDLV)) {
                $qty = (int)$dataDLV['qty_box'];

                $rows .= '
                    <tr>
                        <td>' . $dataDLV['code'] . '</td>
                        <td>' . $dataDLV['kurir_code'] . '</td>
                        <td>' . $dataDLV['kurir_name'] . '</td>
                        <td>' . $dataDLV['customer_code'] . '</td>
                        <td>' . $dataDLV['customer_name'] . '</td>
                        <td>' . $dataDLV['status'] . '</td>
                        <td class="right">' . priceFormat($qty) . '</td>
                    </tr>
                ';

                $totalQty += $qty;
            }

            if ($rows === '') {
                $rows = '
                    <tr>
                        <td colspan="7" style="text-align:center;font-style:italic;">
                            Tidak ada data pengantaran di bulan ' . $monthName . '
                        </td>
                    </tr>
                ';
            } else {
                $rows .= '
                    <tr>
                        <td colspan="6" class="total right">Total</td>
                        <td class="total right">' . priceFormat($totalQty) . '</td>
                    </tr>
                ';
            }

            $content = templateInvoice('
                <div class="header">
                    <h2>LAPORAN PENGANTARAN HARIAN ' . strtoupper($monthName) . ' ' . $year . '</h2>
                </div>
                <div class="info">
                    <table>
                        <tr>
                            <th>Bulan</th>
                            <td>' . $monthName . '</td>
                        </tr>
                        <tr>
                            <th>Tahun</th>
                            <td>' . $year . '</td>
                        </tr>
                    </table>
                </div>
                <div class="items">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode Pengantaran</th>
                                <th>Kode Kurir</th>
                                <th>Nama Kurir</th>
                                <th>Kode Pelanggan</th>
                                <th>Nama Pelanggan</th>
                                <th>Status</th>
                                <th class="right">Qty/Box</th>
                            </tr>
                        </thead>
                        <tbody>' . $rows . '</tbody>
                    </table>
                </div>
            ');

            createPDF('Laporan Pengantaran_' . $monthName . '_' . $year, $content);
            exit;
        }
    }
?>