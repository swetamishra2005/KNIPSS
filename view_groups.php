<?php
require_once 'scripts/feeReport.php';
include("scripts/settings.php"); // Ensure DB connection is included
page_header_start();
page_header_end();
page_sidebar();

$session = $_GET['session'] ?? '2024';

$report = new FeeReport();
//print ("sweta"); die;

$groups = $report->getGroupCodes(); // ['BA', 'BSC', ...]
$groupSummary = $report->getGroupSummaries($session); // ['BA' => [...], ...]
?>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<body class="p-4">
<h3>Select a Group (Session: <?= $session ?>)</h3>
<a href="index.php?session=<?= $session ?>" class="btn btn-secondary btn-sm mb-3">Back</a>

<table class="table table-bordered">
   
        <tr class="text-white bg-primary" align="center">
            <th>Group</th>
            <th>Total Fees</th>
            <th>Paid</th>
            <th>Due</th>
            <th>Action</th>
        </tr>
   
    <tbody>
    <?php foreach ($groups as $g): 
        $sum = $groupSummary[$g] ?? ['total_fee' => 0, 'paid' => 0, 'due' => 0];
    ?>
        <tr>
            <td><?= htmlspecialchars($g) ?></td>
            <td><?= formatIndianCurrency($sum['total_fee']) ?></td>
            <td><?= formatIndianCurrency($sum['paid']) ?></td>
            <td><?= formatIndianCurrency($sum['due']) ?></td>
            <td>
                <a href="view_classes.php?group=<?= urlencode($g) ?>&session=<?= $session ?>" class="btn btn-sm btn-info">View</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php
	page_footer_start();
	page_footer_end();
	?>