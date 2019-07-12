<?php

	/*
	================================================
	== Manage Competitions Page
	== You Can Add | Edit | Delete Competitions From Here
	================================================
	*/

	ob_start(); // Output Buffering Start

	session_start();

	$pageTitle = 'المسابقات';

	if (isset($_SESSION['Username'])) {

		include 'init.php';

		$do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

		// Start Manage Page

		if ($do == 'Manage') { // Manage Competitions Page

			$query = '';

			// Select All Users Except Admin 
			$stmt = $con->prepare("SELECT competitions.*, groups.GroupName, groups.FinalResult AS WinnerResult FROM competitions LEFT OUTER JOIN groups ON groups.GroupId = competitions.WinnerGroupId ORDER BY competitions.CompID ASC");

			// Execute The Statement

			$stmt->execute();

			// Assign To Variable 

			$rows = $stmt->fetchAll();

			if (! empty($rows)) {

			?>

			<h1 class="text-center">إدارة المسابقات</h1>
			<div class="container text-right">
				<a href="competitions.php?do=Add" class="btn btn-primary mb-2">
					<i class="fa fa-plus"></i> مسابقة جديدة
				</a>
				<div class="table-responsive">
					<table class="main-table manage-Groups text-center table table-bordered">
						<tr>
							<td>التحكم</td>
                                   <td>مجموع نقاط الفائز</td>
							<td>المجموعه الفائزة</td>
							<td>اسم المسابقة</td>
							<td>الرقم التسلسلى</td>
						</tr>
						<?php
							foreach($rows as $row) {
								echo "<tr>";
									echo "<td>
										<a href='competitions.php?do=Edit&compid=" . $row['CompID'] . "' class='btn btn-success'><i class='fa fa-edit'></i> تعديل</a>
										<a href='competitions.php?do=Delete&compid=" . $row['CompID'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> حذف </a>
										<a href='competitions.php?do=Display&compid=" . $row['CompID'] . "' class='btn btn-default mt-2'><i class='fa fa-television'></i> عرض الفرق المشاركة </a>
										<a href='competitions.php?do=Details&compid=" . $row['CompID'] . "' class='btn btn-warning mt-2'><i class='fa fa-file-o'></i> عرض الاسئلة </a>
										<a href='competitions.php?do=Results&compid=" . $row['CompID'] . "' class='btn btn-info mt-2'><i class='fa fa-graduation-cap'></i> نتيجة المسابقة </a>";
                                             echo "</td>";
                                             echo "<td>" . $row['WinnerResult'] . "</td>";
									echo "<td>" . $row['GroupName'] . "</td>";
									echo "<td>" . $row['CompName'] . "</td>";
									echo "<td>" . $row['CompID'] . "</td>";
								echo "</tr>";
							}
						?>
						<tr>
					</table>
				</div>
				
			</div>

			<?php } else {

				echo '<div class="container">';
					echo '<div class="nice-message">لا يوجد مسابقات لعرضهم</div>';
					echo '<a href="competitions.php?do=Add" class="btn btn-primary">
							<i class="fa fa-plus"></i> مسابقة جديدة
						</a>';
				echo '</div>';

			} ?>

		<?php 

		} elseif ($do == 'Add') { // Add Page ?>

			<h1 class="text-center">إضافة مسابقة جديدة</h1>
			<div class="container">
				<form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="text" name="compName" class="form-control" autocomplete="off" required="required" placeholder="اسم المسابقة" />
						</div>
						<label class="col-sm-2 control-label">اسم المسابقة</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<textarea name="description" class="form-control" rows="5" placeholder="وصف المسابقة"></textarea>
						</div>
						<label class="col-sm-2 control-label">وصف المسابقة</label>
					</div>
					<!-- Start Submit Field -->
					<div class="form-group form-group-lg">
						<div class="text-center col-sm-10 col-sm-offset-2">
							<input type="submit" value="إضافة مسابقة" class="btn btn-primary btn-lg" />
						</div>
					</div>
					<!-- End Submit Field -->
				</form>
			</div>

		<?php 

		} elseif ($do == 'Insert') {

			// Insert Member Page

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

				echo "<h1 class='text-center'>إضافة مسابقة جديدة</h1>";
				echo "<div class='container'>";

				// Get Variables From The Form

				$compName 	= $_POST['compName'];
				$description 	= $_POST['description'];

				// Validate The Form

				$formErrors = array();

				if (strlen($compName) < 5) {
					$formErrors[] = 'اسم المسابقة لا يمكن ان يقل عن 5 احرف';
				}

				if (strlen($compName) > 256) {
					$formErrors[] = 'اسم المسابقة لا يمكن ان يزيد عن 250 حرف';
				}

				if (empty($compName)) {
					$formErrors[] = 'لا يمكن ترك اسم المسابقة فارغ';
				}
				

				// Loop Into Errors Array And Echo It

				foreach($formErrors as $error) {
					echo '<div class="alert alert-danger">' . $error . '</div>';
				}

				// Check If There's No Error Proceed The Update Operation

				if (empty($formErrors)) {
				
					// Check If User Exist in Database

					$check = checkItem("CompName", "competitions", $compName);

					if ($check == 1) {

						$theMsg = '<div class="alert alert-danger">عذرا هذه المسابقة غير موجوده</div>';

						redirectHome($theMsg, 'back');

					} else {

						// Insert Userinfo In Database

						$stmt = $con->prepare("INSERT INTO competitions(CompName, Description) VALUES(:zcompName, :zdescription)");
						$stmt->execute(array(

							'zcompName' 		=> $compName,
							'zdescription' 		=> $description,

						));

						// Echo Success Message

						$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' مسابقة جديدة تم اضافتها</div>';

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

			// Check If Get Request compid Is Numeric & Get Its Integer Value

			$compid = isset($_GET['compid']) && is_numeric($_GET['compid']) ? intval($_GET['compid']) : 0;

			// Select All Data Depend On This ID

			$stmt = $con->prepare("SELECT * FROM competitions WHERE CompID = ? LIMIT 1");

			// Execute Query

			$stmt->execute(array($compid));

			// Fetch The Data

			$row = $stmt->fetch();

			// The Row Count

			$count = $stmt->rowCount();

			// If There's Such ID Show The Form

			if ($count > 0) { ?>

				<h1 class="text-center">تعديل مسابقة</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=Update" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="compid" value="<?php echo $compid ?>" />
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="text" name="groupName" class="form-control" autocomplete="off" required="required" value="<?php echo $row['CompName'] ?>" placeholder="اسم المجموعه" />
						</div>
						<label class="col-sm-2 control-label">اسم المسابقة</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
                                   <textarea name="description" class="form-control" rows="5" placeholder="وصف المسابقة"><?php echo $row['Description'] ?></textarea>
						</div>
						<label class="col-sm-2 control-label">وصف المسابقة</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<select name="winnerGroupId">
								<?php
									$allGroups = getAllFrom("*", "groups", "WHERE CompID = ".$row['CompID']."", "", "GroupId");
									foreach ($allGroups as $group) {
										echo "<option value='" . $group['GroupId'] . "'"; 
										if ($row['WinnerGroupId'] == $group['GroupId']) { echo 'selected'; } 
										echo ">" . $group['GroupName'] . "</option>";
									}
								?>
							</select>
						</div>
						<label class="col-sm-2 control-label">المجموعة الفائزة</label>
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

			echo "<h1 class='text-center'>تعديل المسابقة</h1>";
			echo "<div class='container text-right'>";

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

				// Get Variables From The Form

				$compid 		= $_POST['compid'];
				$compName 	= $_POST['compName'];
				$description 	= $_POST['description'];

				// Validate The Form

				$formErrors = array();

				if (strlen($compName) < 5) {
					$formErrors[] = 'اسم المسابقة لا يمكن ان يقل عن 5 احرف';
				}

				if (strlen($compName) > 256) {
					$formErrors[] = 'اسم المسابقة لا يمكن ان يزيد عن 250 حرف';
				}

				if (empty($compName)) {
					$formErrors[] = 'لا يمكن ترك اسم المسابقة فارغ';
				}
				// Loop Into Errors Array And Echo It

				foreach($formErrors as $error) {
					echo '<div class="alert alert-danger">' . $error . '</div>';
				}

				// Check If There's No Error Proceed The Update Operation

				if (empty($formErrors)) {

					$stmt2 = $con->prepare("SELECT * FROM competitions WHERE CompName = ? AND CompID != ?");

					$stmt2->execute(array($compName, $compid));

					$count = $stmt2->rowCount();

					if ($count == 1) {

						$theMsg = '<div class="alert alert-danger">عفوا هذه المسابقة غير موجود</div>';

						redirectHome($theMsg, 'back');

					}
						// Update The Database With This Info

						$stmt = $con->prepare("UPDATE competitions SET CompName = ?, Description = ? WHERE CompID = ?");

						$stmt->execute(array($compName, $description, $compid));

						// Echo Success Message

						$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' مسابقة تم تعديل بياناتها بنجاح</div>';

						redirectHome($theMsg, 'back');

					}

				} else {

				$theMsg = '<div class="alert alert-danger">عفوا لا يمكنك تصفح هذا المسار</div>';

				redirectHome($theMsg);

			}

			echo "</div>";

		} elseif ($do == 'Delete') { // Delete Member Page

			echo "<h1 class='text-center'>حذف مسابقة</h1>";
			echo "<div class='container'>";

				// Check If Get Request compid Is Numeric & Get The Integer Value Of It

				$compid = isset($_GET['compid']) && is_numeric($_GET['compid']) ? intval($_GET['compid']) : 0;

				// Select All Data Depend On This ID

				$check = checkItem('CompID', 'competitions', $compid);

				// If There's Such ID Show The Form

				if ($check > 0) {

					$stmt = $con->prepare("DELETE FROM competitions WHERE CompID = :zcompid");

					$stmt->bindParam(":zcompid", $compid);

					$stmt->execute();

					$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' مسابقة تم حذفها بنجاح</div>';

					redirectHome($theMsg, 'back');

				} else {

					$theMsg = '<div class="alert alert-danger">رقم المعرف هذا غير موجود</div>';

					redirectHome($theMsg);

				}

			echo '</div>';
                        
		} elseif ($do == 'Display') { // Display Groups Page

			// Check If Get Request compid Is Numeric & Get The Integer Value Of It
			$compid = isset($_GET['compid']) && is_numeric($_GET['compid']) ? intval($_GET['compid']) : 0;
			
			$Competitions = getAllFrom('*', 'competitions','WHERE CompID = '.$compid.'', '','CompID', 'LIMIT 1');
			echo "<h1 class='text-center'>الفرق المشاركة فى ".$Competitions[0]['CompName']."</h1>";
			echo "<div class='container'>";

			// get all groups using comp id
			$stmt = $con->prepare("	SELECT
									groups.*,
									users.Username,
									competitions.CompName
								FROM
									groups
								LEFT OUTER JOIN users ON users.UserId = groups.TeamLeaderId
								LEFT OUTER JOIN competitions ON competitions.CompID = groups.CompID
								WHERE
									groups.CompID = ".$compid."
								ORDER BY
									groups.GroupId ASC");
			$stmt->execute();
			$rows = $stmt->fetchAll();

			if (! empty($rows)) {
			?>
			<div class="table-responsive">
					<table class="main-table manage-Groups text-center table table-bordered">
						<tr>
							<td>التحكم</td>
							<td>قائد المجموعه</td>
							<td>النتيجة النهائية</td>
							<td>عدد الاعضاء</td>
							<td>المسابقة</td>
							<td>اسم المجموعه</td>
							<td>الرقم التسلسلى</td>
						</tr>
						<?php
							foreach($rows as $row) {
								echo "<tr>";
									echo "<td>
										<a href='groups.php?do=Edit&groupid=" . $row['GroupId'] . "' class='btn btn-success'><i class='fa fa-edit'></i> تعديل</a>
										<a href='groups.php?do=Delete&groupid=" . $row['GroupId'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> حذف </a>
										<a href='groups.php?do=Display&groupid=" . $row['GroupId'] . "' class='btn btn-info'><i class='fa fa-television'></i> عرض اعضاء الفريق </a>";
									echo "</td>";
									echo "<td><b>" . $row['Username'] . "</b></td>";
									echo "<td>" . $row['FinalResult'] . "</td>";
									echo "<td>" . $row['MembersNo'] . "</td>";
									echo "<td>" . $row['CompName'] . "</td>";
									echo "<td><b>" . $row['GroupName'] . "</b></td>";
									echo "<td>" . $row['GroupId'] . "</td>";
								echo "</tr>";
							}
						?>
						<tr>
					</table>
				</div>
			<?php
			}
			echo '</div>';
                        
		} elseif ($do == 'Details') { // Display Questions Page

			// Check If Get Request compid Is Numeric & Get The Integer Value Of It
			$compid = isset($_GET['compid']) && is_numeric($_GET['compid']) ? intval($_GET['compid']) : 0;
			
			
			// get all groups using comp id
			$xyz = $con->prepare("	SELECT
									questions.*,
									competitions.CompName AS CompNameAr
								FROM
									questions
								LEFT OUTER JOIN competitions ON questions.CompID = competitions.CompID
								Where 
									questions.CompID = ".$compid."
								ORDER BY
								questions.QuestionID
								DESC");
			$xyz->execute();
			$rows = $xyz->fetchAll();
								
			if (! empty($rows)) {
				echo "<h1 class='text-center'>أسئلة ".$rows[0]['CompNameAr']."</h1>";
				echo "<div class='container'>";
			?>
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
						foreach($rows as $row) {
							echo "<tr>";
								echo "<td>
									<a href='questions.php?do=Edit&questionid=" . $row['QuestionID'] . "' class='btn btn-success'><i class='fa fa-edit'></i> تعديل</a>
									<a href='questions.php?do=Delete&questionid=" . $row['QuestionID'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> حذف </a>
									<a href='questions.php?do=Display&questionid=" . $row['QuestionID'] . "' class='btn btn-info'><i class='fa fa-television'></i> عرض الإجابات </a>";
								echo "</td>";
								echo "<td dir='rtl'><b>" . $row['Minutes'] . "</b> دقائق</td>";
								echo "<td>" . $row['Result'] . "</td>";
								if($row['QuestionType'] == 1) {
									echo "<td>اختيار من متعدد</td>";
								} else {
									echo "<td>سؤال نصي</td>";
								}
								echo "<td>" . $row['CompNameAr'] . "</td>";
								echo "<td>" . $row['QuestionDesc'] . "</td>";
								echo "<td><img src='img/service/" . $row['avatar'] . "'alt='' /></td>";
								echo "<td>" . $row['QuestionID'] . "</td>";
							echo "</tr>";
						}
					?>
					<tr>
				</table>
			</div>
			<?php
			}
			echo '</div>';
                        
		} elseif ($do == 'Results') { // Display Questions Page

			// Check If Get Request compid Is Numeric & Get The Integer Value Of It
			$compid = isset($_GET['compid']) && is_numeric($_GET['compid']) ? intval($_GET['compid']) : 0;
			
			
			// get all groups using comp id
			$win = $con->prepare("	SELECT
									groups.*,
									competitions.CompName AS CompNameAr
								FROM
									groups
								LEFT OUTER JOIN competitions ON competitions.CompID = groups.CompID
								WHERE
									groups.CompID = ".$compid."
								ORDER BY
									groups.FinalResult
								DESC
							");
			$win->execute();
			$rows = $win->fetchAll();
								
			if (! empty($rows)) {
				$WinnerGroup = $rows[0];
				$x = 0; $y = 0;
				if($WinnerGroup['FinalResult'] == $rows[1]['FinalResult']) {
					$first = $con->prepare("SELECT
					TIMEDIFF(
						(SELECT TIME(MAX(AnswerTime)) from group_questions_answer where GroupId = ".$WinnerGroup['GroupId']."),
					    	(SELECT TIME(MIN(AnswerTime)) from group_questions_answer where GroupId = ".$WinnerGroup['GroupId'].")
					) AS DiffTime");
					$first->execute();
					$FirstGroup = $first->fetchAll();
					
					$second = $con->prepare("SELECT
										TIMEDIFF(
											(SELECT TIME(MAX(AnswerTime)) from group_questions_answer where GroupId = ".$rows[1]['GroupId']."),
											(SELECT TIME(MIN(AnswerTime)) from group_questions_answer where GroupId = ".$rows[1]['GroupId'].")
										) AS DiffTime");
					$second->execute();
					$SecondGroup = $second->fetchAll();
					
					
					$x = $FirstGroup[0]['DiffTime'];
					$y = $SecondGroup[0]['DiffTime'];
					if($x > $y){
						$WinnerGroup = $rows[1];
					} else {
						$WinnerGroup = $rows[0];
					}
				}

               	$winStmt = $con->prepare("UPDATE competitions SET WinnerGroupId = ? WHERE CompID = ?");
				$winStmt->execute(array($WinnerGroup['GroupId'], $compid));
				
				echo "<h1 class='text-center'>نتائج الفائزين فى ".$rows[0]['CompNameAr']."</h1>";
				echo "<div class='container'>";
			?>
			<div class="panel-group text-center" style="margin-top: 30px;">
				<div class="panel panel-success panel-shadow">
					<div class="panel-heading" style="font-weight: bold;letter-spacing: 1px;">الفريق الفائز بالمسابقة</div>
					<div class="panel-body text-success" style="letter-spacing: 1px;"><b><?php echo $WinnerGroup['GroupName'];?></b> ورقم التعريف الزمنى لديه <?php echo $y; ?></div></div>
				</div>
				<hr />
				<?php 
					$index = 0;
					foreach ($rows as $group) {
						$index++;
						if($group['GroupId'] != $WinnerGroup['GroupId']){
							$xcv = $con->prepare("SELECT
										TIMEDIFF(
											(SELECT TIME(MAX(AnswerTime)) from group_questions_answer where GroupId = ".$group['GroupId']."),
											(SELECT TIME(MIN(AnswerTime)) from group_questions_answer where GroupId = ".$group['GroupId'].")
										) AS DiffTime");
							$xcv->execute();
							$groupxy = $xcv->fetchAll();
							echo '<h3 class="text-center" style="margin-bottom:20px;">ترتيب الفرق بعد الفريق الفائز</h3>';
							echo '<div class="panel panel-warning panel-shadow text-center">';
								echo '<div class="panel-heading" style="font-weight: bold;letter-spacing: 1px;">';
									echo 'المرتبة رقم '.$index;
								echo '</div>';
								echo '<div class="panel-body text-warning" style="letter-spacing: 1px;"><b>'.$group['GroupName'].'</b> ورقم التعريف الزمنى لديه '.$groupxy[0]['DiffTime'].'</div>';
							echo '</div>';
						}
					}
				?>

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