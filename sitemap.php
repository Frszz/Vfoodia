<?php
    header('Content-type: application/xml');
    echo "<?xml version='1.0' encoding='UTF-8'?>"."\n";
    echo "<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>"."\n";

    $lastmod = date('Y-m-d\TH:i:s+00:00');

    echo "<url>";
    echo "<loc>https://vfoodia-production.up.railway.app/home.php</loc>";
    echo "<lastmod>$lastmod</lastmod>";
    echo "<priority>1.00</priority>";
    echo "</url>";

    echo "<url>";
    echo "<loc>https://vfoodia-production.up.railway.app/delivery.php</loc>";
    echo "<lastmod>$lastmod</lastmod>";
    echo "<priority>1.00</priority>";
    echo "</url>";

    echo "<url>";
    echo "<loc>https://vfoodia-production.up.railway.app/customer.php</loc>";
    echo "<lastmod>$lastmod</lastmod>";
    echo "<priority>1.00</priority>";
    echo "</url>";

    echo "<url>";
    echo "<loc>https://vfoodia-production.up.railway.app/report.php</loc>";
    echo "<lastmod>$lastmod</lastmod>";
    echo "<priority>1.00</priority>";
    echo "</url>";

    echo "<url>";
    echo "<loc>https://vfoodia-production.up.railway.app/account.php</loc>";
    echo "<lastmod>$lastmod</lastmod>";
    echo "<priority>1.00</priority>";
    echo "</url>";

    echo "<url>";
    echo "<loc>https://vfoodia-production.up.railway.app/login.php</loc>";
    echo "<lastmod>$lastmod</lastmod>";
    echo "<priority>1.00</priority>";
    echo "</url>";

    echo "</urlset>";
?>