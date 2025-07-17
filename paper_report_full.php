<?php
include("scripts/settings.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    $sql = "DELETE FROM exam_paper_code_mapping WHERE sno = $id";
    $result = execute_query($db, $sql);
    if ($result) {
        echo "<div class='alert alert-success'>Record deleted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Delete failed: " . mysqli_error($db) . "</div>";
    }
    exit;
}

page_header_start();
page_header_end();
page_sidebar();
?>

<div class="container mt-4">
    <div class="card card-body">
        <div  class="bg-secondary text-white d-flex justify-content-between align-items-center mb-3 p-2">
            <h3 >Full Paper Code Report</h3>
            <a href="add_subject_erp.php" class="btn btn-secondary">← Back to Main</a>
        </div>

        <div id="responseMsg"></div>

        <?php
        $sql = "SELECT sno, paper, theory_paper_code, practical_paper_code FROM exam_paper_code_mapping ORDER BY sno ASC";
        $result = execute_query($db, $sql);

        if (mysqli_num_rows($result) === 0): ?>
            <div class="alert alert-info">No data found in <strong>exam_paper_code_mapping</strong>.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="reportTable">
                    
                        <tr >
                            <th>S.No</th>
                            <th>Paper Title</th>
                            <th>Theory Paper Code</th>
                            <th>Practical Paper Code</th>
                            <th>Action</th>
                        </tr>
                    
                    
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="text-center" id="row-<?= $row['sno'] ?>">
                                <td><?= htmlspecialchars($row['sno']) ?></td>
                                <td><?= htmlspecialchars($row['paper'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['theory_paper_code'] ?? '') ?></td>
                                <td><?= $row['practical_paper_code'] === null ? 'NULL' : htmlspecialchars($row['practical_paper_code']) ?></td>
                                <td>
                                    <button class="btn btn-sm delete-btn btn-danger" data-id="<?= $row['sno'] ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            if (confirm('Are you sure you want to delete this record?')) {
                fetch('', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'delete_id=' + id
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('responseMsg').innerHTML = html;
                    const row = document.getElementById('row-' + id);
                    if (row) row.remove();
                });
            }
        });
    });
});
</script>

<?php
page_footer_start();
page_footer_end();
?>
