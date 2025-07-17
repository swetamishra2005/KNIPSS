<?php
require_once 'scripts/feeReport.php';
include("scripts/settings.php"); // Ensure DB connection is included
page_header_start();
page_header_end();
page_sidebar();
$group = $_GET['group'] ?? '';
$session = $_GET['session'] ?? '2024';

$report = new FeeReport();
$classes = $report->getClassesByGroup($group, $session);
?>

    <title>Classes in <?= htmlspecialchars($group) ?> - Session <?= $session ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
<h3> Classes in Group <strong><?= htmlspecialchars($group) ?></strong> (Session: <?= $session ?>)</h3>
<a href="view_groups.php?session=<?= $session ?>" class="btn btn-secondary btn-sm mb-3">Back</a>

<table class="table table-bordered">
  
        <tr class="text-white bg-primary" align="center">
            <th>Class</th>
            <th>Total Fee</th>
            <th>Paid</th>
            <th>Due</th>
            <th>Students</th>
        </tr>
    <tbody>
        <?php foreach ($classes as $class): ?>
        <tr>
            <td><?= $class['class_description'] ?></td>
            <td>₹<?= formatIndianCurrency($class['total_fee']) ?></td>
            <td>₹<?= formatIndianCurrency($class['paid']) ?></td>
            <td>₹<?= formatIndianCurrency($class['due']) ?></td>
            <td>
                <a href="view_students.php?class_id=<?= $class['class_id'] ?>&session=<?= $session ?>" class="btn btn-sm btn-info">View</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
	page_footer_start();
	page_footer_end();
	?>