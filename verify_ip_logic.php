<?php
// Dynamic IP Detection Logic Verification Script
// This script mirrors the exact logic implemented in ParticipationController.php

echo "--------------------------------------------------\n";
echo "Verifying IP Detection Logic...\n";
echo "--------------------------------------------------\n";

$serverHost = 'localhost'; // Safe fallback

// Simulate SERVER_ADDR not being set or being dynamic
// In CLI mode, SERVER_ADDR is usually not set, so it tests the fallback logic (Method 2)
if (isset($_SERVER['SERVER_ADDR']) && 
    filter_var($_SERVER['SERVER_ADDR'], FILTER_VALIDATE_IP) && 
    $_SERVER['SERVER_ADDR'] !== '127.0.0.1' && 
    $_SERVER['SERVER_ADDR'] !== '::1') {
    $serverHost = $_SERVER['SERVER_ADDR'];
    echo "[Method 1] Found SERVER_ADDR: $serverHost\n";
} 
else {
    echo "[Method 1] SERVER_ADDR not usable or empty. Trying Method 2...\n";
    
    $hostName = gethostname();
    $ips = gethostbynamel($hostName);
    
    if ($ips && is_array($ips)) {
        echo "Available IPs: " . implode(', ', $ips) . "\n\n";
        
        // Priority 1: 192.168.1.x (Common home/office LAN)
        foreach ($ips as $ip) {
            if (strpos($ip, '192.168.1.') === 0) {
                $serverHost = $ip;
                echo "[Method 2 - Priority 1] Found 192.168.1.x IP: $serverHost\n";
                break;
            }
        }
        
        // Priority 2: 192.168.x.x (General LAN) AND NOT 192.168.56.x (VirtualBox default)
        if ($serverHost === 'localhost') {
            foreach ($ips as $ip) {
                if (strpos($ip, '192.168.') === 0 && strpos($ip, '192.168.56.') === false) {
                    $serverHost = $ip;
                    echo "[Method 2 - Priority 2] Found general LAN IP: $serverHost\n";
                    break;
                }
            }
        }
        
        // Priority 3: Any non-loopback IP
        if ($serverHost === 'localhost') {
            foreach ($ips as $ip) {
                if ($ip !== '127.0.0.1' && $ip !== '::1') {
                    $serverHost = $ip;
                    echo "[Method 2 - Priority 3] Found fallback IP: $serverHost\n";
                    break;
                }
            }
        }
    } else {
        echo "No IPs returned by gethostbynamel().\n";
    }
}

echo "--------------------------------------------------\n";
echo "FINAL DETECTED IP: $serverHost\n";
echo "--------------------------------------------------\n";
