<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_POST['file'] ?? '';

    if ($file && file_exists($file)) {
        unlink($file); // Delete the file
        echo "success";
    } else {
        echo "File not found!";
    }
} else {
    echo "Invalid request!";
}

?>
