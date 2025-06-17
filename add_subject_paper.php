<?php 
include("scripts/settings.php");


$msg='';
page_header_start();
page_header_end();
page_sidebar();
	if($_POST['type']=="Vocational" || $_POST['type']=="Cocurricular" || $_POST['type']=="Minor"){
		$type_status="2";
	}else{
		$type_status="1";
	}
	  // Handle form submission
    if (isset($_POST['class_id'])) {
        $paper_code = $_POST['paper_code'];

        if (isset($_POST['edit']) && $_POST['edit'] != '') {
            //  Edit Mode
            $sno = $_POST['edit'];

            // 🔍 Check for duplicate paper_code in other records
            $check_sql = 'SELECT * FROM add_subject_details
                      WHERE paper_code = "' . $paper_code . '"
                      AND sno != "' . $sno . '"';
            $check_result = execute_query($db, $check_sql);

            if (mysqli_num_rows($check_result) > 0) {
                echo "<script>alert('Error: Paper Code $paper_code already exists in another record.');</script>";
            } else {
                //  Safe to update
                $sql = 'UPDATE add_subject_details SET
                    type="' . $_POST['type'] . '",
                    type_status="' . $type_status . '",
                    optional_group="' . $_POST['optional_group'] . '",
                    class_id="' . $_POST['class_id'] . '",
                    subject_id="' . $_POST['subject_id'] . '",
                    paper_code="' . $_POST['paper_code'] . '",
                    title_of_paper="' . $_POST['title_of_paper'] . '",
                    theory_practical="' . $_POST['theory_practical'] . '",
                    credit="' . $_POST['credit'] . '",
                    max_marks="' . $_POST['max_marks'] . '",
                    theory="' . $_POST['theory'] . '",
                    mid="' . $_POST['mid'] . '",
                    practical="' . $_POST['practical'] . '",
                    sessional="' . $_POST['sessional'] . '",
                    edited_by="' . $_SESSION['username'] . '",
                    edition_time="' . date('Y-m-d H:i:s') . '"
                    WHERE sno = ' . $sno;

                $update_result = execute_query($db, $sql);

                if (mysqli_errno($db)) {
                    echo "<div style='color:red;'> Update failed: " . mysqli_error($db) . "</div>";
                } else {
                    echo "<div style='color:green;'> Successfully updated.</div>";
                }
            }
        } else {
            // New Entry
            $check_sql    = 'SELECT * FROM add_subject_details WHERE paper_code = "' . $paper_code . '"';
            $check_result = execute_query($db, $check_sql);

            if (mysqli_num_rows($check_result) > 0) {
                echo "<script>alert('Error: Paper Code $paper_code already exists.');</script>";
            } else {
                $sql = 'INSERT INTO add_subject_details
                    (type, type_status, optional_group, class_id, subject_id, paper_code, title_of_paper, theory_practical, credit, max_marks, theory, mid, practical, sessional, created_by, creation_time)
                    VALUES
                    ("' . $_POST['type'] . '", "' . $type_status . '", "' . $_POST['optional_group'] . '", "' . $_POST['class_id'] . '", "' . $_POST['subject_id'] . '", "' . $_POST['paper_code'] . '", "' . $_POST['title_of_paper'] . '", "' . $_POST['theory_practical'] . '", "' . $_POST['credit'] . '", "' . $_POST['max_marks'] . '", "' . $_POST['theory'] . '", "' . $_POST['mid'] . '", "' . $_POST['practical'] . '", "' . $_POST['sessional'] . '", "' . $_SESSION['username'] . '", "' . date('Y-m-d H:i:s') . '")';

                execute_query($db, $sql);
                if (mysqli_errno($db)) {
                    echo "<div style='color:red;'> Insertion failed: " . mysqli_error($db) . "</div>";
                } else {
                    echo "<div style='color:green;'> Data inserted successfully.</div>";
                }
            }
        }
    }
	
		
	if(isset($_GET['del'])){
		$sql = 'delete from add_subject_details where sno="'.$_GET['del'].'"';
		execute_query($db, $sql);
		if(mysqli_error($db)){
			$msg .= '<h3 style="color:red;">Error in deleting . '.mysqli_error($db).' >> '.$sql.'</h3>';
		}
		else{
			$msg .= '<h3 style="color:red;">Deleted</h3>';
		}
	}
	

	if(isset($_GET['edit'])){
		$sql = 'select * from add_subject_details where sno = '.$_GET['edit'];
		$qry = execute_query($db, $sql);
		$res = mysqli_fetch_assoc($qry);
		
	}
?>


<style>
form div.row:nth-child(odd) {
    background: #eeeeee;
    border-radius: 5px;
    margin-bottom: 5px;
    margin-top: 5px;
    padding: 5px;
}

form div.row label {
    color: #000000;
}
</style>

<div id="container">
    <div class="card card-body">
        <div class="row d-flex my-auto">
            <form action="<?php echo $_SERVER['PHP_SELF']?>" class="wufoo leftLabel page1" name="feesdeposit"
                enctype="multipart/form-data" method="post" onSubmit="" autocomplete="off">
                <div class="bg-primary text-white p-2">
                    <h3> Add Paper</h3>
                </div>
                <div class="col-md-12">
                    <!-- first row -->
                    <table width="100%" class="table table-striped table-hover rounded">
                        <tr>
                            <th width="15%">Subject Type</th>
                            <th width="15%">
                                <select name="type" id="type"
                                    value="<?php echo isset($_GET['edit']) ? $res['type'] : '' ?>" class="form-control"
                                    required>
                                    <option disabled>---Select Class---</option>
                                    <option value="Major">Major</option>
                                    <option value="Minor">Minor</option>
                                    <option value="Elective">Elective</option>
                                    <option value="Cocurricular">Cocurricular</option>
                                    <option value="Vocational">Vocational</option>
                                    <option value="Remedial">Remedial</option>
                                    <option value="Non-Gradial">Non-Gradial</option>
                                    <option value="Supporting">Supporting</option>
                                    <option value="Common">Common</option>
                                    <option value="Core">Core</option>
                                    <option value="Project">Project</option>
                                    <option value="Research">Research</option>
                                </select>
                            </th>
                            <th>Class Name</th>
                            <th>
                                <select name="class_id" id="class_id"
                                    value="<?php echo isset($_GET['edit']) ? $res['class_id'] : '' ?>"
                                    class="form-control" required>
                                    <option disabled
                                        <?php echo isset($_GET['edit']) ? "" : ' selected = "selected" ' ?>>---Select
                                        Class---</option>
                                    <?php
										$sql = 'select * from class_detail where semester IN ("2","4") ORDER BY ABS(group_short) ASC';
										$dept_list = execute_query($db, $sql);
										if ($dept_list) {
											while ($list = mysqli_fetch_assoc($dept_list)) {
												echo '<option value="' . $list['sno'] . '" ' . (isset($_GET['edit']) && $res['class_id'] == $list['sno'] ? ' selected = "selected" ' : "") . '>' . $list['class_description'] . '</option>';
											}
										}
										?>
                                </select>
                            </th>

                            <th>Subject Name</th>
                            <th id="subject_id">
                                <select name="subject_id" class="form-control" required>
                                    <option disabled
                                        <?php echo isset($_GET['edit']) ? "" : ' selected = "selected" ' ?>>---Select
                                        Subject---</option>
                                    <?php
										$sql = 'SELECT * FROM add_subject';
										$dept_list = execute_query($db, $sql);
										if ($dept_list) {
											while ($list = mysqli_fetch_assoc($dept_list)) {
												echo '<option value="' . $list['sno'] . '" ' . (isset($_GET['edit']) && $res['subject_id'] == $list['sno'] ? ' selected="selected" ' : "") . '>' . $list['subject'] . '</option>';
											}
										}
										?>
                                </select>
                            </th>

                            <th id="subject_id2" style="display: none;">
                                <select name="subject_id" class="form-control" required>
                                    <option disabled
                                        <?php echo isset($_GET['edit']) ? "" : ' selected = "selected" ' ?>>---Select
                                        Subject---</option>
                                    <?php
										$sql = 'SELECT * FROM add_subject2';
										$dept_list = execute_query($db, $sql);
										if ($dept_list) {
											while ($list = mysqli_fetch_assoc($dept_list)) {
												echo '<option value="' . $list['sno'] . '" ' . (isset($_GET['edit']) && $res['subject_id'] == $list['sno'] ? ' selected="selected" ' : "") . '>' . $list['subject'] . '</option>';
											}
										}
										?>
                                </select>
                            </th>

                            <script>
                            document.getElementById("type").addEventListener("change", function() {
                                let selectedValue = this.value;
                                let subjectId1 = document.getElementById("subject_id");
                                let subjectId2 = document.getElementById("subject_id2");

                                if (selectedValue === "Vocational" || selectedValue === "Cocurricular" ||
                                    selectedValue === "Minor") {
                                    subjectId1.style.display = "none";
                                    subjectId2.style.display = "";
                                } else {
                                    subjectId1.style.display = "";
                                    subjectId2.style.display = "none";
                                }
                            });

                            // Trigger change event on page load if editing
                            window.addEventListener("DOMContentLoaded", function() {
                                document.getElementById("type").dispatchEvent(new Event("change"));
                            });
                            </script>


                        </tr>
                        <tr>
                            <th width="15%">Paper Code</th>
                            <th width="15%"><input type="text" name="paper_code" id="paper_code"
                                    value="<?php echo isset($_GET['edit']) ? $res['paper_code'] : '' ?>"
                                    class="form-control" required="required"></th>
                            <th>Paper Title</th>
                            <th><input type="text" name="title_of_paper" id="title_of_paper" class="form-control"
                                    required="required"
                                    value="<?php echo isset($_GET['edit']) ? $res['title_of_paper'] : '' ?>"></th>
                            <th>Theory/Practical</th>
                            <th><select name="theory_practical" id="theory_practical"
                                    value="<?php echo isset($_GET['edit']) ? $res['theory_practical'] : '' ?>"
                                    class="form-control" required>
                                    <option disabled>---Select Class---</option>
                                    <option value="Theory">Theory</option>
                                    <option value="Practical">Practical</option>
                                    <option value="Theory+Practical">Theory+Practical</option>
                                    <option value="Viva-voce">Viva-voce</option>
                                    <option value="Project">Project</option>
                                    <option value="Research">Research</option>

                                </select></th>
                        </tr>
                        <tr>
                            <th width="15%">Credit</th>
                            <th width="15%"><input type="text" name="credit" id="credit"
                                    value="<?php echo isset($_GET['edit']) ? $res['credit'] : '' ?>"
                                    class="form-control"></th>
                            <th>Optional Group</th>
                            <th><select name="optional_group" id="optional_group" class="form-control">
                                    <option value="">---Optional Groups---</option>
                                    <option value="1">Optional Group 1</option>
                                    <option value="2">Optional Group 2</option>
                                    <option value="3">Optional Group 3</option>
                                    <option value="4">Optional Group 4</option>
                                    <option value="5">Optional Group 5</option>
                                    <option value="6">Optional Group 6</option>
                                    <option value="7">Optional Group 7</option>
                                    <option value="8">Optional Group 8</option>

                                </select>
                            </th>
                            <th>Paper Marks</th>

                            <th>
                                <select name="optional_paper" id="optional_paper" class="form-control">
                                    <option disabled
                                        <?php echo isset($_GET['edit']) ? "" : ' selected = "selected" ' ?>>---Select
                                        Marks---</option>
                                    <option value="SATISFACTORY">SATISFACTORY</option>
                                    <option value="Marks">Marks</option>
                                </select>
                            </th>
                        </tr>
                        <tr id="marks" style="display: none;">
                            <th>Theory Marks: <input type="number" name="theory" id="theory"
                                    class="form-control marks-input"></th>
                            <th>Practical Marks: <input type="number" name="practical" id="practical"
                                    class="form-control marks-input"></th>
                            <th>Mid Marks: <input type="number" name="mid" id="mid" class="form-control marks-input">
                            </th>
                            <th>Sessional Marks: <input type="number" name="sessional" id="sessional"
                                    class="form-control marks-input"></th>
                            <th>Total Marks: <input type="number" name="max_marks" id="total_marks" class="form-control"
                                    readonly></th>
                        </tr>
                    </table>

                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script>
                    $(document).ready(function() {
                        // Show/Hide the row based on dropdown selection
                        $('#optional_paper').change(function() {
                            if ($(this).val() === 'Marks') {
                                $('#marks').show();
                            } else {
                                $('#marks').hide();
                                $('#total_marks').val(''); // Clear total when hiding
                            }
                        });

                        // Calculate total marks
                        $('.marks-input').on('input', function() {
                            let total = 0;
                            $('.marks-input').each(function() {
                                let value = parseFloat($(this).val()) ||
                                0; // Convert input to number
                                total += value;
                            });
                            $('#total_marks').val(total); // Set total marks
                        });
                    });
                    </script>
                    </table>
                    <button type="submit" class="btn btn-primary " name="save" value="">Submit </button>
                    <input type="hidden" name="edit" value="<?php echo isset($_GET['edit'])? $res['sno']: '' ?>">
                </div>
            </form>
        </div>
    </div>
    <div class="card card-body">
        <div class="bg-primary text-white p-2 mb-2">
            <h3>Paper Report</h3>
        </div>

        <!-- Class Filter Dropdown -->
        <form method="GET" action="">
            <div class="form-group mb-3">
                <div class="col-md-4">
                    <label for="class_filter"><strong>Select Class:</strong></label>
                    <select name="class_id" id="class_filter" class="form-control" onchange="this.form.submit()">
                        <option value="">-- All Classes --</option>
                        <?php
							$sql_classes = 'SELECT * FROM class_detail where semester IN ("1","2","3","4") ORDER BY ABS(group_short) ASC, ABS(semester) ASC';
							$result_classes = execute_query($db, $sql_classes);
							while ($class_row = mysqli_fetch_assoc($result_classes)) {
								$selected = isset($_GET['class_id']) && $_GET['class_id'] == $class_row['sno'] ? 'selected' : '';
								echo '<option value="'.$class_row['sno'].'" '.$selected.'>'.$class_row['class_description'].'</option>';
							}
						?>
                    </select>
                </div>
            </div>
        </form>

        <!-- Paper Report Table -->
        <table width="100%" class="table table-striped table-hover rounded">
            <tr class="text-white bg-primary" align="center">
                <th>Sno.</th>
                <th>Type</th>
                <th>Class Name</th>
                <th>Subject Name</th>
                <th>Paper Code</th>
                <th>Paper Title</th>
                <th>Theory/Practical</th>
                <th>Credit</th>
                <th>Max. Marks</th>
                <th>Theory Marks</th>
                <th>Practical Marks</th>
                <th>Mid Marks</th>
                <th>Sessional Marks</th>
                <th>Optional Group</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>

            <?php
				$class_filter = isset($_GET['class_id']) ? $_GET['class_id'] : '';
				$sql = 'SELECT * FROM add_subject_details';
				if ($class_filter != '') {
					$sql .= ' WHERE class_id = "'.$class_filter.'"';
				}
				$result = execute_query($db, $sql);
				$i = 1;

				while ($row = mysqli_fetch_assoc($result)) {
					// Fetch Class Name
					$sql_class = 'SELECT * FROM class_detail WHERE sno = "'.$row['class_id'].'"';
					$result_class = execute_query($db, $sql_class);
					$class = (mysqli_num_rows($result_class) != 0) ? mysqli_fetch_assoc($result_class)['class_description'] : '';

					// Fetch Subject Name
					if ($row['type_status'] == "1") {
						$sql_subject = 'SELECT * FROM add_subject WHERE sno = "'.$row['subject_id'].'"';
					} else {
						$sql_subject = 'SELECT * FROM add_subject2 WHERE sno = "'.$row['subject_id'].'"';
					}
					$result_subject = execute_query($db, $sql_subject);
					$subject = (mysqli_num_rows($result_subject) != 0) ? mysqli_fetch_assoc($result_subject)['subject'] : '';

					echo '<tr align="center">
							<td>'.$i++.'</td>
							<td>'.$row['type'].'</td>
							<td>'.$class.'</td>
							<td>'.$subject.'</td>
							<td>'.$row['paper_code'].'</td>
							<td>'.$row['title_of_paper'].'</td>
							<td>'.$row['theory_practical'].'</td>
							<td>'.$row['credit'].'</td>
							<td>'.$row['max_marks'].'</td>
							<td>'.$row['theory'].'</td>
							<td>'.$row['practical'].'</td>
							<td>'.$row['mid'].'</td>
							<td>'.$row['sessional'].'</td>
							<td>'.($row['optional_group'] != '' ? 'Group '.$row['optional_group'] : '').'</td>
							<td><a href="add_subject_paper.php?edit='.$row['sno'].'" onClick="return confirm(\'Are you sure?\');"><h6 style="color: #3066ec;">Edit</h6></a></td>
							<td><a href="add_subject_paper.php?del='.$row['sno'].'" onClick="return confirm(\'Are you sure?\');"><h6 style="color:red;">Delete</h6></a></td>
						</tr>';
				}
			?>
        </table>
    </div>
</div>
<script>
// Get references to the input fields
var examFeeInput = document.getElementById("exam_fee");
var marksheetFeeInput = document.getElementById("marksheet_fee");
var enrolmentFeeInput = document.getElementById("enrolment_fee");
var gameFeeInput = document.getElementById("game_fee");
var totalFeeInput = document.getElementById("total_fee");

// Add an input event listener to update the Total Fee
examFeeInput.addEventListener("input", updateTotalFee);
marksheetFeeInput.addEventListener("input", updateTotalFee);
enrolmentFeeInput.addEventListener("input", updateTotalFee);
gameFeeInput.addEventListener("input", updateTotalFee);

function updateTotalFee() {
    // Calculate the sum of all fee components
    var examFee = parseFloat(examFeeInput.value) || 0;
    var marksheetFee = parseFloat(marksheetFeeInput.value) || 0;
    var enrolmentFee = parseFloat(enrolmentFeeInput.value) || 0;
    var gameFee = parseFloat(gameFeeInput.value) || 0;
    var totalFee = examFee + marksheetFee + enrolmentFee + gameFee;

    // Update the Total Fee input field
    totalFeeInput.value = totalFee;
}
</script>
<?php
page_footer_start();
page_footer_end();


?>
