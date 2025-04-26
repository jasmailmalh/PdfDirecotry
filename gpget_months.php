<?php
if (isset($_GET['year'])) {
    $year = $_GET['year'];
    $monthDirs = glob("gpuploads/$year/*", GLOB_ONLYDIR);

    $months = array_map('basename', $monthDirs);
    sort($months);

    foreach ($months as $month) {
        echo "<option value='$month'>$month</option>";
    }
}


if (isset($_GET['year'])) {
    $year = $_GET['year'];
    $monthDirs = glob("gguploads/$year/*", GLOB_ONLYDIR);

    $months = array_map('basename', $monthDirs);
    sort($months);

    foreach ($months as $month) {
        echo "<option value='$month'>$month</option>";
    }
}




?>
