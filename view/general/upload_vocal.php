<?php
if (!empty($_FILES['audio']['name'])) {

    $name = time() . "_" . $_FILES['audio']['name'];
    $path = __DIR__ . "/uploads_vocals/" . $name;

    if (!is_dir(__DIR__ . "/uploads_vocals")) {
        mkdir(__DIR__ . "/uploads_vocals");
    }

    move_uploaded_file($_FILES['audio']['tmp_name'], $path);

    echo $name;
}
?>
