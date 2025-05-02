<?php
$year = $_GET['year'] ?? '';
$month = $_GET['month'] ?? '';
$dir = "gpuploads/$year/$month/";

$pdfs = [];
if (is_dir($dir)) {
    foreach (scandir($dir) as $file) {
        if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'pdf') {
            $pdfs[] = "$dir/$file";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search GurmatParkash</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function fetchMonths(year) {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "gpget_months.php?year=" + year, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById("monthSelect").innerHTML = "<option value=''>Select Month</option>" + xhr.responseText;
                }
            };
            xhr.send();
        }

        window.onload = function () {
            const yearSelect = document.getElementById("yearSelect");
            const selectedYear = yearSelect.value;
            const selectedMonth = "<?php echo $month; ?>";

            if (selectedYear) {
                fetchMonths(selectedYear);
                // Restore selected month after fetch
                setTimeout(function () {
                    if (selectedMonth) {
                        document.getElementById("monthSelect").value = selectedMonth;
                    }
                }, 500);
            }

            yearSelect.onchange = function () {
                fetchMonths(this.value);
            };
        };
    </script>
</head>
<body>
<div class="container">

<div style="text-align: right; margin-bottom: 20px;">
        <a href="index.php" style="
            text-decoration: none;
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        " onmouseover="this.style.backgroundColor='#0056b3'" onmouseout="this.style.backgroundColor='#007BFF'">
          <span >⬅️ Back</span>
        </a>
    </div>
    <h2>🔍 Search Gurmat Parkash PDFs</h2>
    <form method="get">
        <select name="year" id="yearSelect" required>
            <option value="">Select Year</option>
            <?php
            $years = array_filter(glob('gpuploads/*'), 'is_dir');
            foreach ($years as $y) {
                $yName = basename($y);
                $selected = ($yName === $year) ? "selected" : "";
                echo "<option value='$yName' $selected>$yName</option>";
            }
            ?>
        </select>

        <select name="month" id="monthSelect" required>
            <option value="">Select Month</option>
            <!-- Months will be loaded dynamically -->
        </select>

        <input class ="mt-4"type="submit"  style="margin-top: 20px; display: block; margin-left: auto; margin-right: auto;" value="Search">
    </form>

    <div class="pdf-list">
        <h3>📂 Gurmat Parkash <?php echo "$month $year"; ?></h3>
      

        <?php
        if ($pdfs) {
            foreach ($pdfs as $pdf) {
                echo "<p><a href='$pdf' download>📥 Click here to Download " . basename($pdf) . "</a></p>";
            }
        }
        ?>

    </div>
</div>

<!-- Modal -->
<div id="noPdfModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <p>⚠️ No Gurmat Parkash uploaded for the selected year and month.</p>
  </div>
</div>

<script>
    function fetchMonths(year, selectedMonth = "") {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "gpget_months.php?year=" + year, true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                const monthSelect = document.getElementById("monthSelect");
                monthSelect.innerHTML = "<option value=''>Select Month</option>" + xhr.responseText;

                if (selectedMonth) {
                    setTimeout(() => {
                        monthSelect.value = selectedMonth;
                    }, 50);
                }
            }
        };
        xhr.send();
    }

    window.onload = function () {
        const yearSelect = document.getElementById("yearSelect");
        const selectedYear = yearSelect.value;
        const selectedMonth = "<?php echo $month; ?>";

        if (selectedYear) {
            fetchMonths(selectedYear, selectedMonth);
        }

        yearSelect.onchange = function () {
            fetchMonths(this.value);
        };

        // Modal logic
        const modal = document.getElementById("noPdfModal");
        const span = document.getElementsByClassName("close")[0];

        <?php if ($year && $month && empty($pdfs)) : ?>
            modal.style.display = "block";
        <?php endif; ?>

        span.onclick = function () {
            window.location.href = "gpsearch.php";
        }

        window.onclick = function (event) {
            if (event.target === modal) {
                window.location.href = "gpsearch.php";
            }
        }
    };
</script>

</body>
</html>
