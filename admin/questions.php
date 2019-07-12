<?php
	ob_start(); // Output Buffering Start

	session_start();

	$pageTitle = 'أسئلة المسابقات';

	if (isset($_SESSION['Username'])) {

		include 'init.php';

		$do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

		// Start Manage Page

		if ($do == 'Manage') { // Manage Members Page

			$query = '';
			$stmt2 = $con->prepare("SELECT questions.*,competitions.CompName AS CompNameAr FROM questions LEFT OUTER JOIN competitions ON questions.CompID = competitions.CompID ORDER BY QuestionID DESC");

			$stmt2->execute();

			$rows2 = $stmt2->fetchAll();
			
				
			if (! empty($rows2)) {

			?>

			<h1 class="text-center">إدارة الأسئلة</h1>
			<div class="container text-right">
				<a href="questions.php?do=Add" class="btn btn-primary mb-2">
					<i class="fa fa-plus"></i> سؤال جديد
				</a>
				<div class="table-responsive">
					<table class="main-table text-center table table-bordered">
						<tr>
							<td>التحكم</td>
							<td>مدة العرض</td>
							<td>عدد النقاط</td>
							<td>نوع السؤال</td>
							<td>المسابقة</td>
							<td>عنوان السؤال</td>
							<td>صورة السؤال</td>
							<td>رقم المسلسل</td>
						</tr>
						<?php
							foreach($rows2 as $row2) {
								echo "<tr>";
									echo "<td>
										<a href='questions.php?do=Edit&questionid=" . $row2['QuestionID'] . "' class='btn btn-success'><i class='fa fa-edit'></i> تعديل</a>
										<a href='questions.php?do=Delete&questionid=" . $row2['QuestionID'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> حذف </a>
										<a href='questions.php?do=Display&questionid=" . $row2['QuestionID'] . "' class='btn btn-info mt-2'><i class='fa fa-television'></i> عرض الإجابات </a>";
									echo "</td>";
									echo "<td dir='rtl'><b>" . $row2['Minutes'] . "</b> دقائق</td>";
									echo "<td>" . $row2['Result'] . "</td>";
									if($row2['QuestionType'] == 1) {
										echo "<td>اختيار من متعدد</td>";
									} else {
										echo "<td>سؤال نصي</td>";
									}
									echo "<td>" . $row2['CompNameAr'] . "</td>";
									echo "<td>" . $row2['QuestionDesc'] . "</td>";
									echo "<td><img src='img/service/" . $row2['avatar'] . "'alt='' /></td>";
									echo "<td>" . $row2['QuestionID'] . "</td>";
								echo "</tr>";
							}
						?>
						<tr>
					</table>
				</div>
                    
			
			</div>

			<?php } else {

				echo '<div class="container">';
					echo '<div class="nice-message">لا يوجد اسئلة لعرضها</div>';
					echo '<a href="questions.php?do=Add" class="btn btn-primary">
							<i class="fa fa-plus"></i> سؤال جديد
						</a>';
				echo '</div>';

			} ?>

		<?php 

		} elseif ($do == 'Add') { // Add Page ?>

			<h1 class="text-center">إضافة سؤال جديد</h1>
			<div class="container">
				<form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">
					<!-- Start Username Field -->

					<input type="hidden" name="netname" value="<?php if(isset($_GET['net'])){

						$ex = $_GET['net'];
						echo $ex;

					} ?>" class="form-control" required="required" />

					<div class="form-group form-group-lg">
						
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<select name="compID">
								<option value="0">...</option>
								<?php
									$allComps = getAllFrom("*", "competitions", "", "", "CompID");
									foreach ($allComps as $comp) {
										echo "<option value='" . $comp['CompID'] . "'>" . $comp['CompName'] . "</option>";
									}
								?>
							</select>
						</div>
						<label class="col-sm-2 control-label">المسابقة</label>
					</div>

					<div class="form-group form-group-lg">
						
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="text" name="questionDesc" class="form-control" autocomplete="off" required="required" />
						</div>
						<label class="col-sm-2 control-label">عنوان السؤال</label>
					</div>

					<div class="form-group form-group-lg">
						
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<select name="questionType">
								<option value="0">....</option>
								<option value="1">اختيار من متعدد</option>
								<option value="2">له جواب نصي</option>
							</select>
						</div>
						<label class="col-sm-2 control-label">نوع السؤال</label>
					</div>

					<div class="form-group form-group-lg">
						
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="file" name="itemv" class="form-control" />
						</div>
						<label class="col-sm-2 control-label">صورة السؤال</label>
					</div>

					<div class="form-group form-group-lg">
						
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="number" name="result" class="form-control" autocomplete="off" required="required" />
						</div>
						<label class="col-sm-2 control-label">عدد النقاط</label>
					</div>

					<div class="form-group form-group-lg">
						
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="number" name="ordering" class="form-control" autocomplete="off" required="required" />
						</div>
						<label class="col-sm-2 control-label">ترتيب السؤال</label>
					</div>

					<div class="form-group form-group-lg">
						
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="number" name="minutes" class="form-control" autocomplete="off" required="required" />
						</div>
						<label class="col-sm-2 control-label">مدة العرض (بالدقائق)</label>
					</div>

					<div class="form-group form-group-lg">
						
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<select name="mediaFlag">
								<option value="0">....</option>
								<option value="1">صورة</option>
								<option value="2">فيديو</option>
								<option value="3">ملف صوتي</option>
							</select>
						</div>
						<label class="col-sm-2 control-label">نوع صيغة السؤال فى العرض</label>
					</div>

					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<select name="msqSoultion">
								<option value="0">...</option>
								<?php
									$allSolutions = getAllFrom("*", "mcqsolutions", "", "", "SolutionID");
									foreach ($allSolutions as $solution) {
										echo "<option value='" . $solution['SolutionID'] . "'>" . $solution['SolutionTitle'] . "</option>";
									}
								?>
							</select>
						</div>
						<label class="col-sm-2 control-label">الاجابة الصحيحة فى حالة الاختيار المتعدد</label>
					</div>

					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<textarea class="form-control" rows="5" name="textSolution"></textarea>
						</div>
						<label class="col-sm-2 control-label">اجابة السؤال اذا كان نصي</label>
					</div>
					
					<div class="form-group form-group-lg">
						<div class="text-center col-sm-10 col-sm-offset-2">
							<input type="submit" value="إضافة سؤال" class="btn btn-primary btn-lg" />
						</div>
					</div>
					<!-- End Submit Field -->
				</form>
			</div>

		<?php 

		} elseif ($do == 'Insert') {

			// Insert Member Page

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

				echo "<h1 class='text-center'>اضافة سؤال</h1>";
				echo "<div class='container'>";

				// Get Variables From The Form
				$avatarName = $_FILES['itemv']['name'];
				$avatarSize = $_FILES['itemv']['size'];
				$avatarTmp = $_FILES['itemv']['tmp_name'];
				$avatarType = $_FILES['itemv']['type'];
				

				$avatarAllowedExtension = array("jpeg", "jpg", "png", "gif", "pdf", "mp3", "mp4", "3gp", "mov", "avi", "wav", "wma","webm");

				
				$avatarNamee = explode(".", $avatarName);
                                $avatarExtension  = strtolower(array_pop($avatarNamee));
  
                                $fileName = array_shift($avatarNamee);


				
				$compID 		= $_POST['compID'];
				$questionDesc	= $_POST['questionDesc'];
				$questionType 	= $_POST['questionType'];
				$result 		= $_POST['result'];
				$msqSoultion 	= $_POST['msqSoultion'];
				$textSolution 	= $_POST['textSolution'];
				$ordering 	= $_POST['ordering'];
				$minutes		= $_POST['minutes'];
				$mediaFlag	= $_POST['mediaFlag'];
				
				

				$formErrors = array();

				
				
				if (empty($compID)) {
					$formErrors[] = 'لا يمكن ترك اخيار المسابقه فارغ';
				}
				if (empty($questionDesc)) {
					$formErrors[] = 'لا يمكن ترك عنوان السؤال فارغ';
				}
				if (empty($questionType)) {
					$formErrors[] = 'لا يمكن ترك عنوان نوع السؤال فارغ';
				}
				if (empty($result)) {
					$formErrors[] = 'لا يمكن ترك عنوان عدد النقاط فارغ';
				}
				
				
				foreach($formErrors as $error) {
					echo '<div class="alert alert-danger">' . $error . '</div>';
				}

				if (empty($formErrors)) {
					$avatar = rand(0, 100000) . '_' . $avatarName;
					move_uploaded_file($avatarTmp, "img/service/" . $avatar);

					if(empty($avatarName)){

					$avatar = 'default.png';
                                        }

						$stmt = $con->prepare("INSERT INTO questions(CompID, QuestionDesc, QuestionType, avatar, McqSolution, TextSolution, Result, Ordering, Minutes, MediaFlag)
							VALUES(:zCompID, :zQuestionDesc, :zQuestionType, :zavatar,:zMcqSolution, :zTextSolution, :zResult, :zOrdering, :zminutes, :zmediaFlag) ");
						$stmt->execute(array(

                                                    'zCompID' => $compID,
                                                    'zQuestionDesc' => $questionDesc,
                                                    'zQuestionType' => $questionType,
                                                    'zavatar' => $avatar,				
                                                    'zMcqSolution' => $msqSoultion,
                                                    'zTextSolution' => $textSolution,
                                                    'zResult' => $result,
                                                    'zOrdering' => $ordering,
										  'zminutes' => $minutes,
										  'zmediaFlag' => $mediaFlag
						));

						$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' سؤال جديد تم اضافته</div>';

						redirectHome($theMsg, 'back');
				}

			} else {

				echo "<div class='container'>";

				$theMsg = '<div class="alert alert-danger">عفوا لا يمكن تصفح هذا صفحات هذا المسار</div>';

				redirectHome($theMsg);

				echo "</div>";

			}

			echo "</div>";

		} elseif ($do == 'Edit') {

			// Check If Get Request questionid Is Numeric & Get Its Integer Value

			$questionid = isset($_GET['questionid']) && is_numeric($_GET['questionid']) ? intval($_GET['questionid']) : 0;

			// Select All Data Depend On This ID

			$stmt = $con->prepare("SELECT * FROM questions WHERE QuestionID = ? LIMIT 1");

			// Execute Query

			$stmt->execute(array($questionid));

			// Fetch The Data

			$row = $stmt->fetch();

			// The Row Count

			$count = $stmt->rowCount();

			// If There's Such ID Show The Form

			if ($count > 0) { ?>

				<h1 class="text-center">تعديل السؤال</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=Update" method="POST" enctype="multipart/form-data">
					<!-- Start Username Field -->

					<input type="hidden" name="questionid" value="<?php echo $questionid ?>" />

					<div class="form-group form-group-lg">
						
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<select name="compID">
								<?php
									$allComps = getAllFrom("*", "competitions", "", "", "CompID");
									foreach ($allComps as $comp) {
										echo "<option value='" . $comp['CompID'] . "'"; 
										if ($row['CompID'] == $comp['CompID']) { echo 'selected'; } 
										echo ">" . $comp['CompName'] . "</option>";
									}
								?>
							</select>
						</div>
						<label class="col-sm-2 control-label">المسابقة</label>
					</div>

					<div class="form-group form-group-lg">
						
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="text" name="questionDesc" value="<?php echo $row['QuestionDesc'] ?>" class="form-control" autocomplete="off" required="required" />
						</div>
						<label class="col-sm-2 control-label">عنوان السؤال</label>
					</div>

					<div class="form-group form-group-lg">
						
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<select name="questionType">
							<option value="1" <?php if ($row['QuestionType'] == 1) { echo 'selected'; } ?>>اختيار من متعدد</option>
							<option value="2" <?php if ($row['QuestionType'] == 2) { echo 'selected'; } ?>>له جواب نصي</option>
							</select>
						</div>
						<label class="col-sm-2 control-label">نوع السؤال</label>
					</div>

					<div class="form-group form-group-lg">
						
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="file" name="itemv" class="form-control" />
						</div>
						<label class="col-sm-2 control-label">صورة السؤال</label>
					</div>

					<div class="form-group form-group-lg">
						
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="number" name="result" value="<?php echo $row['Result'] ?>" class="form-control" autocomplete="off" required="required" />
						</div>
						<label class="col-sm-2 control-label">عدد النقاط</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="number" name="ordering" value="<?php echo $row['Ordering'] ?>" class="form-control" autocomplete="off" required="required" />
						</div>
						<label class="col-sm-2 control-label">ترتيب السؤال</label>
					</div>

					<div class="form-group form-group-lg">
						
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="number" name="minutes" class="form-control" value="<?php echo $row['Minutes'] ?>" autocomplete="off" required="required" />
						</div>
						<label class="col-sm-2 control-label">مدة العرض (بالدقائق)</label>
					</div>

					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<select name="mediaFlag">
							<option value="1" <?php if ($row['MediaFlag'] == 1) { echo 'selected'; } ?>>صورة</option>
							<option value="2" <?php if ($row['MediaFlag'] == 2) { echo 'selected'; } ?>>فيديو</option>
							<option value="3" <?php if ($row['MediaFlag'] == 3) { echo 'selected'; } ?>>صوت</option>
							</select>
						</div>
						<label class="col-sm-2 control-label">نوع صيغة السؤال</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<select name="msqSoultion">
									<?php
										$allSolutions = getAllFrom("*", "mcqsolutions", "", "", "SolutionID");
										foreach ($allSolutions as $Solution) {
											echo "<option value='" . $Solution['SolutionID'] . "'"; 
											if ($row['McqSolution'] == $Solution['SolutionID']) { echo 'selected'; } 
											echo ">" . $Solution['SolutionTitle'] . "</option>";
										}
									?>
							</select>
						</div>
						<label class="col-sm-2 control-label">الاجابة الصحيحة فى حالة الاختيار المتعدد</label>
					</div>

					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<textarea class="form-control" value="<?php echo $row['TextSolution'] ?>" rows="5" name="textSolution"></textarea>
						</div>
						<label class="col-sm-2 control-label">اجابة السؤال اذا كان نصي</label>
					</div>
					
					<div class="form-group form-group-lg">
						<div class="text-center col-sm-10 col-sm-offset-2">
							<input type="submit" value="إضافة سؤال" class="btn btn-primary btn-lg" />
						</div>
					</div>
					<!-- End Submit Field -->
				</form>
				</div>

			<?php

			

			} else {

				echo "<div class='container'>";

				$theMsg = '<div class="alert alert-danger">رقم المعرف هذا غير موجود</div>';

				redirectHome($theMsg);

				echo "</div>";

			}

		} elseif ($do == 'Update') { // Update Page

			echo "<h1 class='text-center'>تعديل السؤال</h1>";
			echo "<div class='container'>";

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

				// Get Variables From The Form

				$id 	= $_POST['questionid'];
				
				
				$compID 		= $_POST['compID'];
				$questionDesc	= $_POST['questionDesc'];
				$questionType 	= $_POST['questionType'];
				$result 		= $_POST['result'];
				$msqSoultion 	= $_POST['msqSoultion'];
				$textSolution 	= $_POST['textSolution'];
				$ordering 	= $_POST['ordering'];
				$minutes 		= $_POST['minutes'];
				$mediaFlag	= $_POST['mediaFlag'];
				
				$avatarName = $_FILES['itemv']['name'];
				$avatarSize = $_FILES['itemv']['size'];
				$avatarTmp = $_FILES['itemv']['tmp_name'];
				$avatarType = $_FILES['itemv']['type'];
				

				$avatarAllowedExtension = array("jpeg", "jpg", "png", "gif", "pdf", "mp3", "mp4", "3gp", "mov", "avi", "wav", "wma","webm");

				
				$avatarNamee 		= explode(".", $avatarName);
                    $avatarExtension  	= strtolower(array_pop($avatarNamee));
                    $fileName 		= array_shift($avatarNamee);
				

				$formErrors = array();

				
				if (empty($compID)) {
					$formErrors[] = 'لا يمكن ترك اخيار المسابقه فارغ';
				}
				if (empty($questionDesc)) {
					$formErrors[] = 'لا يمكن ترك عنوان السؤال فارغ';
				}
				if (empty($questionType)) {
					$formErrors[] = 'لا يمكن ترك عنوان نوع السؤال فارغ';
				}
				if (empty($result)) {
					$formErrors[] = 'لا يمكن ترك عنوان عدد النقاط فارغ';
				}
				

				foreach($formErrors as $error) {
					echo '<div class="alert alert-danger">' . $error . '</div>';
				}

				// Check If There's No Error Proceed The Update Operation

				if (empty($formErrors)) {
					$avatar = rand(0, 100000) . '_' . $avatarName;
					move_uploaded_file($avatarTmp, "img/service/" . $avatar);

					if(empty($avatarName)){
						$avatar = 'default.png';
					}

						$stmt = $con->prepare("UPDATE questions SET CompID = ?, QuestionDesc = ?, QuestionType = ?, avatar = ?, McqSolution = ?, TextSolution = ?, Result = ?, Ordering = ?, Minutes = ?, MediaFlag = ? WHERE QuestionID = ?");

						$stmt->execute(array($compID, $questionDesc,$questionType,$avatar, $msqSoultion,$textSolution,$result, $ordering, $minutes, $mediaFlag, $id));

						//================================================================

						$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' سؤال تم تعديله</div>';

						redirectHome($theMsg, 'back');

				}

			} else {

				$theMsg = '<div class="alert alert-danger">عفوا لا يمكنك الوصول لهذه الصفحة</div>';

				redirectHome($theMsg);

			}

			echo "</div>";

		} elseif ($do == 'Delete') { // Delete Member Page

			echo "<h1 class='text-center'>حذف سؤال</h1>";
			echo "<div class='container'>";

				// Check If Get Request questionid Is Numeric & Get The Integer Value Of It

				$questionid = isset($_GET['questionid']) && is_numeric($_GET['questionid']) ? intval($_GET['questionid']) : 0;

				// Select All Data Depend On This ID

				$check = checkItem('QuestionID', 'questions', $questionid);

				// If There's Such ID Show The Form

				if ($check > 0) {

					$stmt = $con->prepare("DELETE FROM questions WHERE QuestionID = :zquestId");

					$stmt->bindParam(":zquestId", $questionid);

					$stmt->execute();

					$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Deleted</div>';

					redirectHome($theMsg, 'back');

				} else {

					$theMsg = '<div class="alert alert-danger">رقم المعرف هذا غير موجود</div>';

					redirectHome($theMsg);

				}

			echo '</div>';

		}  elseif ($do == 'Display') { // Display Solutions Page

			// Check If Get Request compid Is Numeric & Get The Integer Value Of It
			$questionid = isset($_GET['questionid']) && is_numeric($_GET['questionid']) ? intval($_GET['questionid']) : 0;
			
			
			// get all groups using comp id
			$xyz = $con->prepare("	SELECT
									mcqsolutions.*,
									questions.QuestionDesc,
									questions.McqSolution
								FROM
									mcqsolutions
								LEFT OUTER JOIN questions ON questions.QuestionID = mcqsolutions.QuestionID
								WHERE 
									mcqsolutions.QuestionID = ".$questionid."
								ORDER BY
									mcqsolutions.SolutionID ASC");
			$xyz->execute();
			$rows = $xyz->fetchAll();
								
			if (! empty($rows)) {
				echo "<h1 class='text-center'>إجابات: ".$rows[0]['QuestionDesc']."</h1>";
				echo "<div class='container'>";
			?>
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
			<?php
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