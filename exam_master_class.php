<?php 
include("scripts/settings.php");

$msg = '';
$statusMessage = '';

page_header_start();
page_header_end();
page_sidebar();

if (isset($_GET['type']) && isset($_GET['status']) && isset($_GET['id'])) {
    $type_column = ($_GET['type'] === 'back') ? 'show_back' : 'show_result';
    $new_status = ($_GET['status'] == '0') ? '1' : '0';
    $sno = $_GET['id'];
    $sql = "UPDATE result_class SET $type_column = '$new_status' WHERE sno = '$sno'";
    execute_query($db, $sql);

} elseif (isset($_GET['group_dis']) && isset($_GET['group_name']) && isset($_GET['form_type'])) {
    $type_column = ($_GET['form_type'] === 'back') ? 'show_back' : 'show_result';
    $new_status = ($_GET['group_dis'] == '0') ? '1' : '0';
    $group_name = $_GET['group_name'];
    $sql = "UPDATE result_class SET $type_column = '$new_status' WHERE type = '$group_name'";
    execute_query($db, $sql);

} elseif (isset($_GET['toggle_all']) && isset($_GET['form_type'])) {
    $type_column = ($_GET['form_type'] === 'back') ? 'show_back' : 'show_result';
    $new_status = ($_GET['toggle_all'] == '0') ? '1' : '0';
    $sql = "UPDATE result_class SET $type_column = '$new_status'";
    execute_query($db, $sql);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sno'])) {
    $sno = $_POST['sno'];
    $date = !empty($_POST['result_date']) ? "'" . $_POST['result_date'] . "'" : "NULL";
    $sql = "UPDATE result_class SET result_declaration_date = $date WHERE sno = '$sno'";
    execute_query($db, $sql);
}
?>

<style>
    tr.group-header td {
        background-color: #6c757d !important;
        color: #fff;
        font-size: 18px;
        font-weight: bold;
        padding: 10px;
    }
    td span.btn {
        pointer-events: none;
    }
    html {
        scroll-behavior: smooth;
    }
</style>

<div class="card card-body">
    <div class="bg-primary text-white p-2 d-flex justify-content-between align-items-center">
        <h3>Enable/Disable Class for Result Display</h3>
        <div class="d-flex gap-2">
            <a href="?toggle_all=0&form_type=regular" class="btn btn-success" onclick="return confirm('Enable all Regular?')">Enable All Regular</a>
            <a href="?toggle_all=1&form_type=regular" class="btn btn-danger" onclick="return confirm('Disable all Regular?')">Disable All Regular</a>
            <a href="?toggle_all=0&form_type=back" class="btn btn-success" onclick="return confirm('Enable all Back?')">Enable All Back</a>
            <a href="?toggle_all=1&form_type=back" class="btn btn-danger" onclick="return confirm('Disable all Back?')">Disable All Back</a>
        </div>
    </div>

    <?php if (!empty($statusMessage)): ?>
        <div class="alert alert-info mt-2"><?php echo $statusMessage; ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-hover mt-3">
             <tr class="text-white bg-primary" align="center">
                <th>Sno.</th>
                <th>Class Name</th>
                <th>Regular</th>
                <th>ON/OFF Button</th>
                <th>Back</th>
                <th>ON/OFF Button</th>
                <th>Date</th>
                <th>Update</th>
            </tr>
        <tbody>
        <?php
        $sql = "SELECT * FROM result_class ORDER BY sort_no, course_year, sno";
        $res = execute_query($db, $sql);
        $i = 1;
        $groupCount = 1;
        $currentGroup = '';

        while ($row = mysqli_fetch_assoc($res)) {
            $groupLabel = $row['sort_no'];

            if ($currentGroup != $groupLabel) {
                $currentGroup = $groupLabel;

               $groupId = 'group-' . urlencode($row['type']);

echo '<tr id="' . $groupId . '" class="group-header">
        <td colspan="8">' . $groupCount++ . '. ' . strtoupper($currentGroup) . '
            <div style="float:right;">
                <a href="?group_dis=' . $row['show_result'] . '&form_type=regular&group_name=' . urlencode($row['type']) . '" 
                   onclick="scrollTarget(\'' . $groupId . '\'); return confirm(\'Toggle Group Regular?\');"
                   class="btn btn-sm ' . ($row['show_result'] == '1' ? 'btn-danger' : 'btn-success') . '">'
                   . ($row['show_result'] == '1' ? 'Disable Regular' : 'Enable Regular') . '</a>

                <a href="?group_dis=' . $row['show_back'] . '&form_type=back&group_name=' . urlencode($row['type']) . '" 
                   onclick="scrollTarget(\'' . $groupId . '\'); return confirm(\'Toggle Group Back?\');"
                   class="btn btn-sm ' . ($row['show_back'] == '1' ? 'btn-danger' : 'btn-success') . '">'
                   . ($row['show_back'] == '1' ? 'Disable Back' : 'Enable Back') . '</a>
            </div>
        </td>
      </tr>';

            }

            echo '<tr id="row-' . $row['sno'] . '" align="center">
                <td>' . $i++ . '</td>
                <td>' . $row['class_description'] . '</td>

                <!-- Regular -->
                <td>' . ($row['show_result'] == '1' 
                        ? '<span class="btn btn-success btn-sm">Active</span>' 
                        : '<span class="btn btn-danger btn-sm">Disabled</span>') . '</td>
                <td>
                    <a href="?type=regular&status=' . $row['show_result'] . '&id=' . $row['sno'] . '" 
                       onclick="scrollTarget(\'row-' . $row['sno'] . '\'); return confirm(\'Toggle Regular?\');">'
                       . ($row['show_result'] == '1' 
                            ? '<span class="btn btn-danger btn-sm">CLOSE</span>' 
                            : '<span class="btn btn-warning btn-sm">OPEN</span>') . '</a>
                </td>

                <!-- Back -->
                <td>' . ($row['show_back'] == '1' 
                        ? '<span class="btn btn-success btn-sm">Active</span>' 
                        : '<span class="btn btn-danger btn-sm">Disabled</span>') . '</td>
                <td>
                    <a href="?type=back&status=' . $row['show_back'] . '&id=' . $row['sno'] . '" 
                       onclick="scrollTarget(\'row-' . $row['sno'] . '\'); return confirm(\'Toggle Back?\');">'
                       . ($row['show_back'] == '1' 
                            ? '<span class="btn btn-danger btn-sm">CLOSE</span>' 
                            : '<span class="btn btn-warning btn-sm">OPEN</span>') . '</a>
                </td>

                <!-- Date -->
                <td>
                    <form method="POST" action="exam_master_class.php" style="display:flex;gap:4px;">
                        <input type="hidden" name="sno" value="' . $row['sno'] . '">
                        <input type="date" name="result_date" value="' . $row['result_declaration_date'] . '" class="form-control form-control-sm">
                </td>
                <td><button type="submit" class="btn btn-sm btn-primary">Update</button></form></td>
            </tr>';
        }
        ?>
        </tbody>
    </table>
</div>

<script>
function scrollTarget(id) {
    localStorage.setItem('scrollToRow', id);
}
window.addEventListener("load", function () {
    const rowId = localStorage.getItem('scrollToRow');
    if (rowId) {
        const el = document.getElementById(rowId);
        if (el) {
            el.scrollIntoView({ behavior: "smooth" });

           
            el.style.transition = "background-color 0.5s ease-in-out";
            el.style.backgroundColor = "#ffffcc";
            setTimeout(() => el.style.backgroundColor = "", 1500);
        }
        localStorage.removeItem('scrollToRow');
    }
});
</script>

<?php
page_footer_start();
page_footer_end();
?>
