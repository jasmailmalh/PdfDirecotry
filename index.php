<?php
$uploadDir = "uploads/";

function getLatestPDFs($dir) {
    $pdfs = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

    foreach ($iterator as $file) {
        if ($file->isFile() && strtolower($file->getExtension()) === "pdf") {
            $pdfs[$file->getMTime()] = $file->getPathname();
        }
    }

    krsort($pdfs);
    return array_slice($pdfs, 0, 5, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["pdf"])) {
    $month = date("F"); // Full month name e.g. January
    $year = date("Y");
    $targetDir = "$uploadDir$year/$month/";

    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($_FILES["pdf"]["name"]);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["pdf"]["tmp_name"], $targetFile)) {
        $message = "✅ PDF uploaded to <strong>$year/$month</strong>";
    } else {
        $message = "❌ Error uploading PDF.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload PDF Monthly</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>📤 Upload PDF</h2>
    <?php if (isset($message)) echo "<p>$message</p>"; ?>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="pdf" accept="application/pdf" required>
        <input type="submit" value="Upload PDF">
    </form>

    <h2>🔍 Search PDFs</h2>
    <form action="search.php" method="get">
        <select name="year" id="yearSelect" required>
            <option value="">Select Year</option>
            <?php
            $years = array_filter(glob($uploadDir . '*'), 'is_dir');
            foreach ($years as $yearPath) {
                $y = basename($yearPath);
                echo "<option value='$y'>$y</option>";
            }
            ?>
        </select>

        <select name="month" id="monthSelect" required>
            <option value="">Select Month</option>
        </select>

        <input type="submit" value="Search">
    </form>

    <h2>🆕 Latest PDFs</h2>
    <div class="pdf-list">
        <?php
        $latestPDFs = getLatestPDFs($uploadDir);
        foreach ($latestPDFs as $path) {
            echo "<a href='$path' target='_blank'>" . basename($path) . "</a>";
        }
        ?>
    </div>
</div>

<script>
document.getElementById("yearSelect").addEventListener("change", function () {
    const year = this.value;
    const monthSelect = document.getElementById("monthSelect");

    fetch(`get_months.php?year=${year}`)
        .then(response => response.json())
        .then(data => {
            monthSelect.innerHTML = '<option value="">Select Month</option>';
            data.forEach(month => {
                monthSelect.innerHTML += `<option value="${month}">${month}</option>`;
            });
        });
});
</script>
</body>
</html>
