<?php
require_once 'scripts/feeReport.php';
include("scripts/settings.php");
page_header_start();
page_header_end();
page_sidebar();
$session = $_GET['session'] ?? '2024';
$report = new FeeReport();
$summary = $report->getOverallSummary($session);
?>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <h2>KNIPSS Fee Summary (Session: <?= htmlspecialchars($session) ?>)</h2>

    <div class="alert alert-info">
        <strong>Total:</strong> ₹<?= formatIndianCurrency($summary['total_fee']) ?> |
     <strong>Paid:</strong> ₹<?= formatIndianCurrency($summary['paid']) ?> |
    <strong>Due:</strong> ₹<?= formatIndianCurrency($summary['due']) ?>
    </div>

    <a href="view_groups.php?session=<?= $session ?>" class="btn btn-primary">
        View All Groups
    </a>
</body>
</html>
