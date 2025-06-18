<?php
include("scripts/settings.php");

$msg = '';
page_header_start();
page_header_end();
page_sidebar();
?>

<div id="container">
    <div class="card card-body VkS">
        <div class="bg-primary text-white p-2 mb-2">
            <h3>Paper Report</h3>
        </div>

        <form method="GET" action="">
            <div class="form-group mb-3">
                <div class="col-md-4">
                    <label for="class_filter"><strong>Select Class:</strong></label>
                    <select name="class_id" id="class_filter" class="form-control" onchange="this.form.submit()">
                        <option value="">-- All Classes --</option>
                        <?php
                        $sql_classes = 'SELECT * FROM class_detail WHERE semester IN ("1","2","3","4") ORDER BY ABS(group_short) ASC, ABS(semester) ASC';
                        $result_classes = execute_query($db, $sql_classes);
                        while ($class_row = mysqli_fetch_assoc($result_classes)) {
                            $selected = isset($_GET['class_id']) && $_GET['class_id'] == $class_row['sno'] ? 'selected' : '';
                            echo '<option value="' . $class_row['sno'] . '" ' . $selected . '>' . $class_row['class_description'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </form>

        <?php
        if (!empty($msg)) {
            echo $msg;
        }

        if (isset($_GET['class_id']) && $_GET['class_id'] != '') {
            $class_filter = $_GET['class_id'];

            $sql = 'SELECT asd.paper_code, asd.title_of_paper, cd.class_description, asd.theory_practical 
                    FROM add_subject_details asd 
                    JOIN class_detail cd ON asd.class_id = cd.sno 
                    WHERE asd.class_id = "' . $class_filter . '"';
            $result = execute_query($db, $sql);

            if (!$result) {
                echo "<div style='color:red;'>Error fetching subjects: " . mysqli_error($db) . " (Query: $sql)</div>";
            } else {
                $rows = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $rows[] = $row;
                }

                if (count($rows) == 0) {
                    echo "<div style='color:red;'>No subjects found for the selected class.</div>";
                } else {
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
                        foreach ($rows as $row) {
                            $paper_code = mysqli_real_escape_string($db, $row['paper_code']);
                            $title_of_paper = mysqli_real_escape_string($db, $row['title_of_paper']);
                            $course_name = mysqli_real_escape_string($db, $row['class_description']);
                            $practical_paper_code = ($row['theory_practical'] == 'Practical' || $row['theory_practical'] == 'Theory+Practical')
                                ? '"' . $paper_code . '"' : 'NULL';

                            echo "<div style='color:purple;'>Processing Paper Code: $paper_code for Course: $course_name</div>";

                            $check_sql = 'SELECT sno FROM exam_paper_code_mapping WHERE theory_paper_code = "' . $paper_code . '" LIMIT 1';
                            $check_result = execute_query($db, $check_sql);

                            if (mysqli_num_rows($check_result) > 0) {
                                $update_sql = 'UPDATE exam_paper_code_mapping 
                                               SET paper = "' . $title_of_paper . '", 
                                                   practical_paper_code = ' . $practical_paper_code . ' 
                                               WHERE theory_paper_code = "' . $paper_code . '"';
                                $update_result = execute_query($db, $update_sql);

                                if (!$update_result) {
                                    echo "<div style='color:red;'>Update failed for $paper_code: " . mysqli_error($db) . "</div>";
                                } else {
                                    echo "<div style='color:green;'>Updated $paper_code for $course_name in exam_paper_code_mapping.</div>";
                                }
                            } else {
                                $insert_sql = 'INSERT INTO exam_paper_code_mapping (paper, theory_paper_code, practical_paper_code) 
                                               VALUES ("' . $title_of_paper . '", "' . $paper_code . '", ' . $practical_paper_code . ')';
                                $insert_result = execute_query($db, $insert_sql);

                                if (!$insert_result) {
                                    echo "<div style='color:red;'>Insert failed for $paper_code: " . mysqli_error($db) . "</div>";
                                } else {
                                    echo "<div style='color:green;'>Inserted $paper_code for $course_name into exam_paper_code_mapping.</div>";
                                }
                            }
                        }
                    }

                    ?>
                    <div class="card card-body">
                        <form action="" method="POST">
                            <table width="100%" class="table table-striped table-hover rounded">
                                <tr class="text-white bg-primary" align="center">
                                    <th>Paper Code</th>
                                    <th>Paper Title</th>
                                    <th>Course Name</th>
                                    <th>Practical Paper Code</th>
                                </tr>
                                <?php
                                foreach ($rows as $row) {
                                    $practical_paper_code = ($row['theory_practical'] == 'Practical' || $row['theory_practical'] == 'Theory+Practical') 
                                        ? $row['paper_code'] : 'N/A';
                                    echo '<tr align="center">
                                            <td>' . $row['paper_code'] . '</td>
                                            <td>' . $row['title_of_paper'] . '</td>
                                            <td>' . $row['class_description'] . '</td>
                                            <td>' . $practical_paper_code . '</td>
                                          </tr>';
                                }
                                ?>
                            </table>
                            <button type="submit" class="btn btn-primary mt-2" name="submit">Submit</button>
                        </form>
                    </div>
                    <?php
                }
            }
        }
        ?>
    </div>
</div>

<?php
page_footer_start();
page_footer_end();
?>
