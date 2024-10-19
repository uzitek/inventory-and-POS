<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user = get_user_by_id($_SESSION['user_id']);

// Handle report generation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generate_report'])) {
    $report_type = $_POST['report_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $export_format = $_POST['export_format'];

    $report_data = generate_report($report_type, $start_date, $end_date);

    if ($export_format == 'pdf') {
        export_pdf($report_type, $report_data, $start_date, $end_date);
    } elseif ($export_format == 'excel') {
        export_excel($report_type, $report_data, $start_date, $end_date);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - <?php echo COMPANY_NAME; ?> Inventory & POS</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Reports</h1>
                </div>
                
                <form method="post" action="">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="report_type">Report Type</label>
                            <select class="form-control" id="report_type" name="report_type" required>
                                <option value="sales">Sales Report</option>
                                <option value="inventory">Inventory Report</option>
                                <option value="supplier_performance">Supplier Performance</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="export_format">Export Format</label>
                            <select class="form-control" id="export_format" name="export_format" required>
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="generate_report" class="btn btn-primary">Generate Report</button>
                </form>
                
                <div class="mt-4" id="report_results">
                    <!-- Report results will be displayed here -->
                </div>
                
                <canvas id="reportChart" class="mt-4"></canvas>
            </main>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // JavaScript code for handling report generation and chart display
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                
                $.ajax({
                    url: 'generate_report.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        $('#report_results').html(response.html);
                        
                        // Create chart
                        const ctx = document.getElementById('reportChart').getContext('2d');
                        new Chart(ctx, {
                            type: response.chartType,
                            data: response.chartData,
                            options: response.chartOptions
                        });
                    },
                    error: function() {
                        alert('Error generating report. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>