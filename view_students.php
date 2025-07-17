<?php
require_once 'scripts/feeReport.php';
include("scripts/settings.php"); 
page_header_start();
page_header_end();
page_sidebar();
$class_id = $_GET['class_id'] ?? '';
$session = $_GET['session'] ?? '2024';

$report = new FeeReport();
$students = $report->getStudentsInClass($class_id, $session);
$className = $report->getClassName($class_id);
?>

    <title>Students of <?= htmlspecialchars($className) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<body class="p-4">
<h3>Students of <?= htmlspecialchars($className) ?> (Session: <?= $session ?>)</h3>
<a href="javascript:history.back()" class="btn btn-secondary btn-sm mb-3"> Back</a>

<table class="table table-bordered table-hover">
        <tr   class="text-white bg-primary" align="center">
            <th>UIN</th>
            <th>Name</th>
            <th>Father Name</th>
            <th>Total</th>
            <th>Paid</th>
            <th>Due</th>
        </tr>
    <tbody>
    <?php 
    $total = $paid = $due = 0;
    foreach ($students as $s): 
        $total += $s['total_fee'];
        $paid += $s['paid'];
        $due += $s['due'];
    ?>
    <tr>
        <td><?= $s['student_id'] ?></td>
        <td><?= htmlspecialchars($s['stu_name']) ?></td>
        <td><?= htmlspecialchars($s['father_name']) ?></td>
        <td>₹<?= formatIndianCurrency($s['total_fee']) ?></td>
        <td>₹<?= formatIndianCurrency($s['paid']) ?></td>
        <td>₹<?= formatIndianCurrency($s['due']) ?></td>
    </tr>
    <?php endforeach; ?>
    <tr class="table-secondary fw-bold">
        <td colspan="3" class="text-end" ><b>Total</b></td>
        <td>₹<?= formatIndianCurrency($total) ?></td>
        <td>₹<?= formatIndianCurrency($paid) ?></td>
        <td>₹<?= formatIndianCurrency($due) ?></td>
    </tr>
</tbody>
</table>
<?php
	page_footer_start();
	page_footer_end();
	?>