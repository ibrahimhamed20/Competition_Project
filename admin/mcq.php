<?php

	/*
	================================================
	== Manage Competitions Page
	== You Can Add | Edit | Delete Competitions From Here
	================================================
	*/

	ob_start(); // Output Buffering Start

	session_start();

	$pageTitle = 'أسئلة MCQ';

	if (isset($_SESSION['Username'])) {

		include 'init.php';

		$do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

		// Start Manage Page

		if ($do == 'Manage') { // Manage Competitions Page

			$query = '';

			// Select All Users Except Admin 
			$stmt = $con->prepare("SELECT mcqsolutions.*, questions.QuestionDesc, questions.McqSolution FROM mcqsolutions LEFT OUTER JOIN questions ON questions.QuestionID = mcqsolutions.QuestionID ORDER BY mcqsolutions.SolutionID ASC");

			// Execute The Statement

			$stmt->execute();

			// Assign To Variable 

               $rows = $stmt->fetchAll();

			if (! empty($rows)) {

			?>

			<h1 class="text-center">اجابات الاسئلة المتنوعة</h1>
			<div class="container text-right">
				<a href="mcq.php?do=Add" class="btn btn-primary mb-2">
					<i class="fa fa-plus"></i> اجابة جديدة
				</a>
				<div class="table-responsive">
					<table class="main-table manage-Groups text-center table table-bordered">
						<tr>
							<td>التحكم</td>
                                   <td>الاجابة الصحيحة</td>
							<td>عنوان السؤال</td>
							<td>الاجابة</td>
							<td>الرقم التسلسلى</td>
						</tr>
						<?php
							foreach($rows as $row) {
								echo "<tr>";
									echo "<td>
										<a href='mcq.php?do=Edit&solutionid=" . $row['SolutionID'] . "' class='btn btn-success'><i class='fa fa-edit'></i> تعديل</a>
										<a href='mcq.php?do=Delete&solutionid=" . $row['SolutionID'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> حذف </a>";
                                             echo "</td>";
                                             if($row['McqSolution'] == $row['SolutionID']){
                                                  echo "<td><b>جواب صحيح</b></td>";
                                             } else {
                                                  echo "<td>جواب خاطئ</td>";
                                             }
									echo "<td>" . $row['QuestionDesc'] . "</td>";
									echo "<td>" . $row['SolutionTitle'] . "</td>";
									echo "<td>" . $row['SolutionID'] . "</td>";
								echo "</tr>";
							}
						?>
						<tr>
					</table>
				</div>
				
			</div>

			<?php } else {

				echo '<div class="container">';
					echo '<div class="nice-message">لا يوجد اجابات لعرضهم</div>';
					echo '<a href="mcq.php?do=Add" class="btn btn-primary">
							<i class="fa fa-plus"></i> اجابة جديدة
						</a>';
				echo '</div>';

			} ?>

		<?php 

		} elseif ($do == 'Add') { // Add Page ?>

			<h1 class="text-center">إضافة اجابة جديدة</h1>
			<div class="container">
				<form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
						     <input type="text" name="solutionTitle" class="form-control" autocomplete="off" required="required" placeholder="الاجابة" />
						</div>
						<label class="col-sm-2 control-label">الاجابة</label>
					</div>
					<div class="form-group form-group-lg">
                              <div class="col-sm-10 col-md-6 col-md-offset-4">
							<select name="questionID">
                                        <option value="0">...</option>
								<?php
									$allQuestions = getAllFrom("*", "questions", "", "", "QuestionID");
									foreach ($allQuestions as $question) {
										echo "<option value='" . $question['QuestionID'] . "'>" . $question['QuestionDesc'] . "</option>";
									}
								?>
							</select>
						</div>
						<label class="col-sm-2 control-label">ما السؤال ؟</label>
					</div>
					<!-- Start Submit Field -->
					<div class="form-group form-group-lg">
						<div class="text-center col-sm-10 col-sm-offset-2">
							<input type="submit" value="إضافة اجابة" class="btn btn-primary btn-lg" />
						</div>
					</div>
					<!-- End Submit Field -->
				</form>
			</div>

		<?php 

		} elseif ($do == 'Insert') {

			// Insert Member Page

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

				echo "<h1 class='text-center'>إضافة اجابة جديدة</h1>";
				echo "<div class='container'>";

				// Get Variables From The Form

				$solutionTitle = $_POST['solutionTitle'];
				$questionID 	= $_POST['questionID'];

				// Validate The Form

				$formErrors = array();

				if (strlen($solutionTitle) < 2) {
					$formErrors[] = 'الاجابة لا يمكن ان تقل عن 2 احرف';
				}

				if (strlen($solutionTitle) > 256) {
					$formErrors[] = 'الاجابة لا يمكن ان تزيد عن 250 حرف';
				}

				if (empty($solutionTitle)) {
					$formErrors[] = 'لا يمكن ترك حقل الاجابة فارغ';
                    }
                    if (empty($questionID)) {
					$formErrors[] = 'لا يمكن ترك السؤال فارغ';
				}
				

				// Loop Into Errors Array And Echo It

				foreach($formErrors as $error) {
					echo '<div class="alert alert-danger">' . $error . '</div>';
				}

				// Check If There's No Error Proceed The Update Operation

				if (empty($formErrors)) {
				
					// Check If User Exist in Database

					$check = checkItem("SolutionTitle", "mcqsolutions", $solutionTitle);

					if ($check == 1) {

						$theMsg = '<div class="alert alert-danger">عذرا هذه الاجابة غير موجوده</div>';

						redirectHome($theMsg, 'back');

					} else {

						// Insert Userinfo In Database

						$stmt = $con->prepare("INSERT INTO mcqsolutions(SolutionTitle, QuestionID) VALUES(:zsolutionTitle, :zquestionID)");
						$stmt->execute(array(
							'zsolutionTitle' 	=> $solutionTitle,
							'zquestionID' 	=> $questionID,
						));

						// Echo Success Message

						$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' اجابة جديدة تم اضافتها</div>';

						redirectHome($theMsg, 'back');

					}
				}

			} else {

				echo "<div class='container'>";

				$theMsg = '<div class="alert alert-danger">عفوا لايمكنك  الوصول لهذا المسار</div>';

				redirectHome($theMsg);

				echo "</div>";

			}

			echo "</div>";

		} elseif ($do == 'Edit') {

			// Check If Get Request SolutionID Is Numeric & Get Its Integer Value

			$solutionid = isset($_GET['solutionid']) && is_numeric($_GET['solutionid']) ? intval($_GET['solutionid']) : 0;

			// Select All Data Depend On This ID

			$stmt = $con->prepare("SELECT * FROM mcqsolutions WHERE SolutionID = ? LIMIT 1");

			// Execute Query

			$stmt->execute(array($solutionid));

			// Fetch The Data

			$row = $stmt->fetch();

			// The Row Count

			$count = $stmt->rowCount();

			// If There's Such ID Show The Form

			if ($count > 0) { ?>

				<h1 class="text-center">تعديل اجابة</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=Update" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="solutionid" value="<?php echo $solutionid ?>" />
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="text" name="solutionTitle" class="form-control" autocomplete="off" required="required" value="<?php echo $row['SolutionTitle'] ?>" placeholder="الاجابة" />
						</div>
						<label class="col-sm-2 control-label">الاجابة</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<select name="questionID">
								<?php
									$allQuestions = getAllFrom("*", "questions", "", "", "QuestionID");
									foreach ($allQuestions as $question) {
										echo "<option value='" . $question['QuestionID'] . "'"; 
										if ($row['QuestionID'] == $question['QuestionID']) { echo 'selected'; } 
										echo ">" . $question['QuestionDesc'] . "</option>";
									}
								?>
							</select>
						</div>
						<label class="col-sm-2 control-label">السؤال ؟</label>
					</div>
					<!-- Start Submit Field -->
					<div class="form-group form-group-lg">
						<div class="text-center col-sm-10 col-sm-offset-2">
							<input type="submit" value="حفظ البيانات" class="btn btn-primary btn-lg" />
						</div>
					</div>
						<!-- End Submit Field -->
					</form>
				</div>

			<?php

			// If There's No Such ID Show Error Message

			} else {

				echo "<div class='container text-right'>";

				$theMsg = '<div class="alert alert-danger">رقم المعرف غير موجود</div>';

				redirectHome($theMsg);

				echo "</div>";

			}

		} elseif ($do == 'Update') { // Update Page

			echo "<h1 class='text-center'>تعديل الاجابة</h1>";
			echo "<div class='container text-right'>";

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

				// Get Variables From The Form

				$solutionid 		= $_POST['solutionid'];
				$solutionTitle 	= $_POST['solutionTitle'];
				$questionID 	     = $_POST['questionID'];

				// Validate The Form

				$formErrors = array();

				if (strlen($solutionTitle) < 2) {
					$formErrors[] = 'الاجابة لا يمكن ان تقل عن 2 احرف';
				}

				if (strlen($solutionTitle) > 256) {
					$formErrors[] = 'الاجابة لا يمكن ان تزيد عن 250 حرف';
				}

				if (empty($solutionTitle)) {
					$formErrors[] = 'لا يمكن ترك حقل الاجابة فارغ';
                    }
                    if (empty($questionID)) {
					$formErrors[] = 'لا يمكن ترك السؤال فارغ';
				}
				// Loop Into Errors Array And Echo It

				foreach($formErrors as $error) {
					echo '<div class="alert alert-danger">' . $error . '</div>';
				}

				// Check If There's No Error Proceed The Update Operation

				if (empty($formErrors)) {

					$stmt2 = $con->prepare("SELECT * FROM mcqsolutions WHERE SolutionTitle = ? AND SolutionID != ?");

					$stmt2->execute(array($solutionTitle, $solutionid));

					$count = $stmt2->rowCount();

					if ($count == 1) {

						$theMsg = '<div class="alert alert-danger">عفوا هذه الاجابة غير موجود</div>';

						redirectHome($theMsg, 'back');

					}
						// Update The Database With This Info

						$stmt = $con->prepare("UPDATE mcqsolutions SET SolutionTitle = ?, QuestionID = ? WHERE SolutionID = ?");

						$stmt->execute(array($solutionTitle, $questionID, $solutionid));

						// Echo Success Message

						$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' اجابة تم تعديل بياناتها بنجاح</div>';

						redirectHome($theMsg, 'back');

					}

				} else {

				$theMsg = '<div class="alert alert-danger">عفوا لا يمكنك تصفح هذا المسار</div>';

				redirectHome($theMsg);

			}

			echo "</div>";

		} elseif ($do == 'Delete') { // Delete Member Page

			echo "<h1 class='text-center'>حذف اجابة</h1>";
			echo "<div class='container'>";

				// Check If Get Request solutionid Is Numeric & Get The Integer Value Of It

				$solutionid = isset($_GET['solutionid']) && is_numeric($_GET['solutionid']) ? intval($_GET['solutionid']) : 0;

				// Select All Data Depend On This ID

				$check = checkItem('SolutionID', 'mcqsolutions', $solutionid);

				// If There's Such ID Show The Form

				if ($check > 0) {

					$stmt = $con->prepare("DELETE FROM mcqsolutions WHERE SolutionID = :zsolutionid");

					$stmt->bindParam(":zsolutionid", $solutionid);

					$stmt->execute();

					$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' اجابة تم حذفها بنجاح</div>';

					redirectHome($theMsg, 'back');

				} else {

					$theMsg = '<div class="alert alert-danger">رقم المعرف هذا غير موجود</div>';

					redirectHome($theMsg);

				}

			echo '</div>';
                        
		}
                include $tpl . 'footer.php';
        } else {

		header('Location: index.php');

		exit();
	}

	ob_end_flush(); // Release The Output

?>