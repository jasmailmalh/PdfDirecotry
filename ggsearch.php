<?php
$year = $_GET['year'] ?? '';
$month = $_GET['month'] ?? '';
$dir = "gguploads/$year/$month/";

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
    <title>Search Gurmat Gyan</title>
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
    <h2>🔍 Search Gurmat Gyan PDFs</h2>
    <form method="get">
        <select name="year" id="yearSelect" required>
            <option value="">Select Year</option>
            <?php
            $years = array_filter(glob('gguploads/*'), 'is_dir');
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
        <h3>📂 Gurmat Gyan <?php echo "$month $year"; ?></h3>
      

        <?php
       foreach ($pdfs as $pdf) {
        $basename = basename($pdf);
        echo "<div style='margin-bottom: 10px;'>
                <a href='$pdf' download>📥 $basename</a>
                <button onclick=\"deleteFile('$pdf', this)\" style='margin-left: 10px; color: red;'>🗑️ Delete</button>
              </div>";
    }
    
        ?>

    </div>
</div>

<!-- Modal -->
<div id="noPdfModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <p>⚠️ No Gurmat Gyan uploaded for the selected year and month.</p>
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


<script>
    function deleteFile(filePath, button) {
        if (confirm("Are you sure you want to delete this file?")) {
            const formData = new FormData();
            formData.append("file", filePath);

            fetch("gpdelete.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                if (result.trim() === "success") {
                    button.parentElement.remove(); // Remove the file row
                } else {
                    alert("❌ Failed to delete file: " + result);
                }
            })
            .catch(error => {
                alert("Error: " + error);
            });
        }
    }
</script>



</body>
</html>
