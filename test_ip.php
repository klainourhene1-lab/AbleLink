<?php
echo "Hostname: " . gethostname() . "\n";
echo "Host IP (gethostbyname): " . gethostbyname(gethostname()) . "\n";
echo "SERVER_ADDR: " . ($_SERVER['SERVER_ADDR'] ?? 'Not Set') . "\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'Not Set') . "\n";

$ips = gethostbynamel(gethostname());
echo "All IPs:\n";
if ($ips) {
    foreach ($ips as $ip) {
        echo " - $ip\n";
    }
} else {
    echo " - None found via gethostbynamel\n";
}
