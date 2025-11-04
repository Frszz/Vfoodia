<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?=base_url()?>/assets/js/main.js?v=<?=time()?>"></script>
<?php
    if ($sourceMAP == 'GOOGLE') {
?>
        <script src="https://maps.googleapis.com/maps/api/js?key=<?=$googleAPIkey?>&callback=initMap"></script>
<?php
    }
?>