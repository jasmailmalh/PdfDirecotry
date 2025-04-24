<?php
$uploadDir = "uploads/";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["pdf"])) {
    $year = $_POST['year'];
    $month = $_POST['month'];

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
    <title>Admin Panel - Upload Gurmat Parkash PDFs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>📤 Admin Panel - Upload   Gurmat Parkash PDF</h2>
    <?php if (isset($message)) echo "<p>$message</p>"; ?>
    <form method="post" enctype="multipart/form-data">
        <label>Select Year:</label>
        <select name="year" required>
            <?php for ($y = date("Y"); $y >= 2020; $y--) echo "<option value='$y'>$y</option>"; ?>
        </select><br>

        <label>Select Month:</label>
        <select name="month" required>
            <?php
            $months = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];
            foreach ($months as $m) echo "<option value='$m'>$m</option>";
            ?>
        </select>

        <br><br>
        <input type="file" name="pdf" accept="application/pdf" required>
        <input type="submit" value="Upload PDF">
    </form>

    <br>
    <a href="search.php">🔍 Go to User Search Page</a>
</div>
</body>
</html>
