<meta charset="UTF-8">
<meta name="description" content="Catering Delivery">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">

<!--=============== BOXICONS ===============-->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">

<!--=============== CSS ===============-->
<link rel="stylesheet" href="<?=base_url()?>/assets/css/styles.css?v=<?=time()?>">

<!--=============== FAVICON ===============-->
<link rel="icon" type="image/png" href="<?=base_url()?>/favicon/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="<?=base_url()?>/favicon.svg" />
<link rel="shortcut icon" href="<?=base_url()?>/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="<?=base_url()?>/favicon/apple-touch-icon.png" />
<meta name="apple-mobile-web-app-title" content="Vfoodia" />
<link rel="manifest" href="<?=base_url()?>/site.webmanifest" />

<!--=============== OTHER ===============-->
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/5.0.1/css/fixedColumns.dataTables.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/5.0.1/js/dataTables.fixedColumns.js"></script>

<?php
    if ($sourceMAP == 'OPENSTREET') {
?>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://unpkg.com/@mapbox/polyline@1.1.1/src/polyline.js"></script>
        <script>
            L.Polyline.fromEncoded = function(encoded) {
                let coords = polyline.decode(encoded);
                return L.polyline(coords.map(function(pair) {
                    return [pair[0], pair[1]];
                }));
            };
        </script>
<?php
    }
?>

<title>Vfoodia</title>