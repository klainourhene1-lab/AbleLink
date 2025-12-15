<?php
$source = '../view/FrontOffice/evaluations-evenements.php';
$dest = 'evaluations-evenements.php';

if (!file_exists($source)) {
    die("Source file not found: $source");
}

$content = file_get_contents($source);

// Fix paths
$replacements = [
    "require_once __DIR__ . '/../../Control/" => "require_once __DIR__ . '/",
    "require_once __DIR__ . '/../../Model/" => "require_once __DIR__ . '/../Model/",
    "'../../uploads/" => "'../uploads/",
    "'../general/" => "'../view/general/",
    'href="../general/' => 'href="../view/general/',
    'href="../../videograph-master/' => 'href="../videograph-master/',
    'href="../../Control/' => 'href="',
    'src="../../videograph-master/' => 'src="../videograph-master/',
    'fetch(\'../../Control/' => 'fetch(\'',
    'fetch(\'../Control/' => 'fetch(\'',
    'url(../../' => 'url(../',
    'link rel="stylesheet" href="../../videograph-master/' => 'link rel="stylesheet" href="../videograph-master/',
];

foreach ($replacements as $from => $to) {
    $content = str_replace($from, $to, $content);
}

if (file_put_contents($dest, $content)) {
    echo "Restored and patched $dest successfully.";
} else {
    echo "Failed to write to $dest.";
}
?>
