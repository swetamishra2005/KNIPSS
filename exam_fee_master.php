<?php 
include("scripts/settings.php");


$msg='';
page_header_start();
page_header_end();
page_sidebar();
?>


<?php
	if(isset($_POST['late_fee_status']) && $_POST['late_fee_status'] != ''){
		$sql = 'UPDATE exam_fee_master SET 
				late_fee_status = "'.$_POST['late_fee_status'].'"
				WHERE sno = '.$_POST['sno'];

		execute_query($db, $sql);
		
		if(mysqli_errno($db)){
			echo "Updation failed: ".mysqli_errno($db).mysqli_error($db);
		} else {
			echo "Successfully updated";
		}
	}
	if(isset($_POST['class_id'])){
		if(isset($_POST['edit']) && $_POST['edit'] != ''){
			$sql = 'update exam_fee_master set 
					class_id="'.$_POST['class_id'].'", 
					type="'.$_POST['type'].'", 
					exam_fee="'.$_POST['exam_fee'].'", 
					forwording_fee="'.$_POST['forwording_fee'].'", 
					marksheet_fee="'.$_POST['marksheet_fee'].'" ,
					enrolment_fee="'.$_POST['enrolment_fee'].'",
					game_fee="'.$_POST['game_fee'].'",
					total_fee="'.$_POST['total_fee'].'",
					edited_by="'.$_SESSION['username'].'",
					edition_time="'.date('Y-m-d H:m:s').'"
					where sno = '.$_POST['edit'];
			//echo $sql;
			execute_query($db, $sql);
			if(mysqli_errno($db)){
				echo "Updation failed".mysqli_errno($db).mysqli_error($db);
			}
			else{
				echo "Successfully updated";
			}
		}
		else{
			$sql = 'insert into exam_fee_master (type,class_id, exam_fee,forwording_fee, marksheet_fee, enrolment_fee,game_fee,total_fee, created_by, creation_time ) 
					values("'.$_POST['type'].'","'.$_POST['class_id'].'","'.$_POST['exam_fee'].'","'.$_POST['forwording_fee'].'","'.$_POST['marksheet_fee'].'","'.$_POST['enrolment_fee'].'","'.$_POST['game_fee'].'","'.$_POST['total_fee'].'","'.$_SESSION['username'].'","'.date('Y-m-d H:m:s').'")';
			//echo $sql;
			execute_query($db,$sql);
			if(mysqli_errno($db)){
				echo "Insertion failed".mysqli_errno($db).mysqli_error($db);
			}
			else{
				echo "Data inserted";
			}
		}
	}
	
		
	if(isset($_GET['del'])){
		$sql = 'delete from exam_fee_master where sno="'.$_GET['del'].'"';
		execute_query($db, $sql);
		if(mysqli_error($db)){
			$msg .= '<h3 style="color:red;">Error in deleting . '.mysqli_error($db).' >> '.$sql.'</h3>';
		}
		else{
			$msg .= '<h3 style="color:red;">Deleted</h3>';
		}
	}
	

	if(isset($_GET['edit'])){
		$sql = 'select * from exam_fee_master where sno = '.$_GET['edit'];
		$qry = execute_query($db, $sql);
		$res = mysqli_fetch_assoc($qry);
		
	}
?>


<style>
form div.row:nth-child(odd) {
  background: #eeeeee;
  border-radius: 5px;
  margin-bottom:5px;
  margin-top:5px;
  padding:5px;
}
form div.row label{
	color:#000000;
}
</style>

<div id="container">
        <div class="card card-body">
            <div class="row d-flex my-auto">
                <form action="<?php echo $_SERVER['PHP_SELF']?>" class="wufoo leftLabel page1" name="feesdeposit"
                    enctype="multipart/form-data" method="post" onSubmit="" autocomplete="off">
                    <div class="bg-secondary text-white p-2"><h3> Add Examination Fee (In Indian Rupee)</h3></div>
                    <div class="col-md-12">
                        <!-- first row -->
						<table width="100%" class="table table-striped table-hover rounded">
							<tr>
								<th width="15%">Student Type</th>
								<th width="15%">
									<select name="type" id="type" value="<?php echo isset($_GET['edit']) ? $res['type'] : '' ?>" class="form-control" required>
										<option disabled <?php echo isset($_GET['edit']) ? "" : ' selected = "selected" ' ?>>---Select type---</option>
										<option value="Regular">Regular</option>
										<option value="Supplimentry">Supplimentry</option>
										<option value="Backpaper">Backpaper</option>
										<option value="Ex-Student">Ex-Student</option>
										
									</select>
								</th>
								<th width="15%">Class Name </th>
								<th width="15%">
									<select name="class_id" id="class_id" value="<?php echo isset($_GET['edit']) ? $res['class_id'] : '' ?>" class="form-control" required>
										<option disabled <?php echo isset($_GET['edit']) ? "" : ' selected = "selected" ' ?>>---Select class---</option>
										
										<?php
										$sql = 'select * from class_detail where semester in ("1","2","3","4")';
										$dept_list = execute_query($db, $sql);
										if ($dept_list) {
											while ($list = mysqli_fetch_assoc($dept_list)) {
												echo '<option value="' . $list['sno'] . '" ' . (isset($_GET['edit']) && $res['class_id'] == $list['sno'] ? ' selected = "selected" ' : "") . '>' . $list['class_description'] . '</option>';
											}
										}
										?>
									</select>
								</th>
								<th>Exam Fee (Rs.)</th>
								<th><input type="text" name="exam_fee" id="exam_fee" class="form-control" required="required" value="<?php echo isset($_GET['edit']) ? $res['exam_fee'] : '' ?>"></th>
								
							</tr>
							<tr>
								<th>Forwording Fee  (Rs.)</th>
								<th><input type="text" name="forwording_fee" id="forwording_fee" class="form-control"  value="<?php echo isset($_GET['edit']) ? $res['forwording_fee'] : '' ?>"></th>
								<th>Marksheet Fee (Rs.)</th>
								<th><input type="text" name="marksheet_fee" id="marksheet_fee" class="form-control" value="<?php echo isset($_GET['edit']) ? $res['marksheet_fee'] : '' ?>"></th>
								<th width="15%">Enrollment Fee (Rs.)</th>
								<th width="15%"><input type="text" name="enrolment_fee" id="enrolment_fee" value="<?php echo isset($_GET['edit']) ? $res['enrolment_fee'] : '' ?>" class="form-control" ></th>
								
							</tr>
							<tr>
								<th>Game Fee (Rs.)</th>
								<th><input type="text" name="game_fee" id="game_fee" class="form-control"  value="<?php echo isset($_GET['edit']) ? $res['game_fee'] : '' ?>"></th>
								<th>Total Fee (Rs.)</th>
								<th><input type="text" name="total_fee" id="total_fee" class="form-control" value="<?php echo isset($_GET['edit']) ? $res['total_fee'] : '' ?>" required="required" readonly ></th>
							</tr>
						</table>



                       
                        <button type="submit" class="btn btn-primary " name="save" value="">Submit </button>
						<input type="hidden" name="edit" value="<?php echo isset($_GET['edit'])? $res['sno']: '' ?>">
                    </div>
                </form>
            </div>
        </div>
		<div class="card card-body">
    <div class="bg-secondary text-white p-2 mb-2">
        <h3> Examination Fee Report </h3>
    </div>
    
    <!-- Filter Section -->
    <form method="GET" action="">
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="filterType">Filter by Type</label>
                <select name="filterType" id="filterType" class="form-control">
					<option value="">All</option>
					<option value="Regular" <?php if(isset($_GET['filterType']) && $_GET['filterType'] == 'Regular') echo 'selected'; ?>>Regular</option>
					<option value="Supplimentry" <?php if(isset($_GET['filterType']) && $_GET['filterType'] == 'Supplimentry') echo 'selected'; ?>>Supplimentry</option>
					<option value="Backpaper" <?php if(isset($_GET['filterType']) && $_GET['filterType'] == 'Backpaper') echo 'selected'; ?>>Backpaper</option>
					<option value="Ex-Student" <?php if(isset($_GET['filterType']) && $_GET['filterType'] == 'Ex-Student') echo 'selected'; ?>>Ex-Student</option>
				</select>

            </div>
            <div class="col-md-4">
                <label for="filterClass">Filter by Class</label>
                <select name="filterClass" id="filterClass" class="form-control">
                    <option value="">All</option>
                    <?php
                    $sql_classes = 'select * from class_detail where semester in ("1","2","3","4")';
                    $result_classes = execute_query($db, $sql_classes);
                    while ($row_class = mysqli_fetch_assoc($result_classes)) {
                        $selected = (isset($_GET['filterClass']) && $_GET['filterClass'] == $row_class['sno']) ? 'selected' : '';
                        echo '<option value="'.$row_class['sno'].'" '.$selected.'>'.$row_class['class_description'].'</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block mt-3">Filter</button>
            </div>
			<div class="col-md-2">
                <label>&nbsp;</label>
               <a href="exam_late_fee_master.php" class="btn btn-success" style="font-weight:bolder; margin-top:15px;"> Update Late Fee Master</a>
            </div>
			
        </div>
    </form>

    <!-- Table Section -->
    <table width="100%" class="table table-striped table-hover rounded">
        <tr class="text-white bg-secondary" align="center">
            <th>Sno.</th>
            <th>Type</th>
            <th>Class Name</th>
            <th>Late Fee(Rs.)</th>
            <th>Exam Fee(Rs.)</th>
            <th>Forwording Fee</th>
            <th>Marksheet Fee (Rs.)</th>
            <th>Enrollment Fee (Rs.)</th>
            <th>Game Fee (Rs.)</th>
            <th>Total Fee (Rs.)</th>
            <th>Edit</th>
            <th>Delete</th>
            <th>Late Fee</th>
        </tr>
        <?php
        // Construct SQL query based on filters
        $sql = 'SELECT * FROM exam_fee_master WHERE dropdown_show IS NOT NULL ORDER BY ABS(dropdown_show) ASC';
        
        if (isset($_GET['filterType']) && $_GET['filterType'] != '') {
            $sql .= ' AND type = "'.$_GET['filterType'].'"';
        }
        if (isset($_GET['filterClass']) && $_GET['filterClass'] != '') {
            $sql .= ' AND class_id = "'.$_GET['filterClass'].'"';
        }
        
        $result = execute_query($db, $sql);
        $i = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $sql_class = 'SELECT * FROM class_detail WHERE sno = "'.$row['class_id'].'"';
            $result_class = execute_query($db, $sql_class);
            $class = mysqli_num_rows($result_class) != 0 ? mysqli_fetch_assoc($result_class)['class_description'] : '';
            
            echo '<tr align="center">
            <td>'.$i++.'</td>
            <td>'.$row['type'].'</td>
            <td>'.$class.'</td>';
			if($row['late_fee_status']=="1"){
				echo '<td style="background-color:green; color:white; font-weight:bolder;">'.$row['late_fee'].'</td>';
			}else{
				echo '<td style="background-color:red; color:white; font-weight:bolder;">'.$row['late_fee'].'</td>';
			}
            
            echo '<td>'.$row['exam_fee'].'</td>
            <td>'.$row['forwording_fee'].'</td>
            <td>'.$row['marksheet_fee'].'</td>
            <td>'.$row['enrolment_fee'].'</td>
            <td>'.$row['game_fee'].'</td>
            <td>'.$row['total_fee'].'</td>
            <td><a href="exam_fee_master.php?edit='.$row['sno'].'" onClick="return confirm(\'Are you sure? \');" " class="btn btn-primary">Edit</a></td>
            <td><a href="exam_fee_master.php?del='.$row['sno'].'" onClick="return confirm(\'Are you sure? \');"  class="btn btn-danger">Delete</a></td>
			<td>
				<input type="checkbox" class="lateFeeCheckbox" data-sno="'.$row['sno'].'" 
					name="late_fee_status" style="height:25px;" '.($row['late_fee_status'] == 1 ? 'checked' : '').'>
			</td>

            </tr>';
        }
        ?>
    </table>
</div>

    </div>
	
	
	<script>
    // Get references to the input fields
    var examFeeInput = document.getElementById("exam_fee");
    var forwordingFeeInput = document.getElementById("forwording_fee");
    var marksheetFeeInput = document.getElementById("marksheet_fee");
    var enrolmentFeeInput = document.getElementById("enrolment_fee");
    var gameFeeInput = document.getElementById("game_fee");
    var totalFeeInput = document.getElementById("total_fee");

    // Add an input event listener to update the Total Fee
    examFeeInput.addEventListener("input", updateTotalFee);
    forwordingFeeInput.addEventListener("input", updateTotalFee);
    marksheetFeeInput.addEventListener("input", updateTotalFee);
    enrolmentFeeInput.addEventListener("input", updateTotalFee);
    gameFeeInput.addEventListener("input", updateTotalFee);

    function updateTotalFee() {
        // Calculate the sum of all fee components
        var examFee = parseFloat(examFeeInput.value) || 0;
        var forwordingFee = parseFloat(forwordingFeeInput.value) || 0;
        var marksheetFee = parseFloat(marksheetFeeInput.value) || 0;
        var enrolmentFee = parseFloat(enrolmentFeeInput.value) || 0;
        var gameFee = parseFloat(gameFeeInput.value) || 0;
        var totalFee = examFee + forwordingFee + marksheetFee + enrolmentFee + gameFee;

        // Update the Total Fee input field
        totalFeeInput.value = totalFee;
    }
	
	
	$(document).ready(function() {
		$('.lateFeeCheckbox').change(function() {
			let sno = $(this).data('sno'); // Get the sno from data attribute
			let status = $(this).prop('checked') ? 1 : 0; // Get checkbox status (1 or 0)

			$.ajax({
				url: 'update_late_fee.php',
				type: 'POST',
				data: { sno: sno, late_fee_status: status },
				success: function(response) {
					alert(response); // Show success or error message
				},
				error: function() {
					alert('Error updating late fee status.');
				}
			});
		});
	});

</script>
<?php
page_footer_start();
page_footer_end();


?>	
