<?php

	/*
	================================================
	== Manage Groups Page
	== You Can Add | Edit | Delete Groups From Here
	================================================
	*/

	ob_start(); // Output Buffering Start

	session_start();

	$pageTitle = 'مجموعات المسابقات';

	if (isset($_SESSION['Username'])) {

		include 'init.php';

		$do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

		// Start Manage Page

		if ($do == 'Manage') { // Manage Groups Page

			$query = '';

			// Select All Users Except Admin 

			$stmt = $con->prepare("SELECT groups.*, users.Username, competitions.CompName FROM groups LEFT OUTER JOIN users ON users.UserId = groups.TeamLeaderId LEFT OUTER JOIN competitions ON competitions.CompID = groups.CompID ORDER BY groups.GroupId ASC");

			// Execute The Statement

			$stmt->execute();

			// Assign To Variable 

			$rows = $stmt->fetchAll();

			if (! empty($rows)) {

			?>

			<h1 class="text-center">إدارة المجموعات</h1>
			<div class="container text-right">
				<a href="groups.php?do=Add" class="btn btn-primary mb-2">
					<i class="fa fa-plus"></i> مجموعه جديدة
				</a>
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
				
			</div>

			<?php } else {

				echo '<div class="container">';
					echo '<div class="nice-message">لا يوجد مجموعات لعرضهم</div>';
					echo '<a href="Groups.php?do=Add" class="btn btn-primary">
							<i class="fa fa-plus"></i> مجموعه جديدة
						</a>';
				echo '</div>';

			} ?>

		<?php 

		} elseif ($do == 'Add') { // Add Page ?>

			<h1 class="text-center">إضافة مجموعه جديدة</h1>
			<div class="container">
				<form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="text" name="groupName" class="form-control" autocomplete="off" required="required" placeholder="اسم المجموعه" />
						</div>
						<label class="col-sm-2 control-label">اسم المجموعه</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="number" name="membersNo" class="form-control" autocomplete="new-password" placeholder="عدد الاعضاء" />
							<i class="show-pass fa fa-eye fa-2x"></i>
						</div>
						<label class="col-sm-2 control-label">عدد الاعضاء</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="text" name="finalResult" class="form-control" autocomplete="off" placeholder="نتيجة المجموعه النهائيةس" />
						</div>
						<label class="col-sm-2 control-label">النتيجة النهائية</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="text" disabled="true" name="finalResult" class="form-control" placeholder="يمكنك اختيار قائد للمجموعه بعد ادخال اعضاء المجموعه" />
						</div>
						<label class="col-sm-2 control-label">قائد المجموعه</label>
					</div>
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
						<label class="col-sm-2 control-label">المسابقة ؟</label>
					</div>
					<!-- Start Submit Field -->
					<div class="form-group form-group-lg">
						<div class="text-center col-sm-10 col-sm-offset-2">
							<input type="submit" value="إضافة مجموعه" class="btn btn-primary btn-lg" />
						</div>
					</div>
					<!-- End Submit Field -->
				</form>
			</div>

		<?php 

		} elseif ($do == 'Insert') {

			// Insert Member Page

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

				echo "<h1 class='text-center'>إضافة مجموعه جديدة</h1>";
				echo "<div class='container'>";

				// Get Variables From The Form

				$groupName 	= $_POST['groupName'];
				$membersNo 	= $_POST['membersNo'];
				$finalResult 	= $_POST['finalResult'];
				//$teamLeaderId 	= $_POST['teamLeaderId'];
				$compID 		= $_POST['compID'];

				// Validate The Form

				$formErrors = array();

				if (strlen($groupName) < 4) {
					$formErrors[] = 'اسم المجموعه لا يمكن ان يقل عن 4 احرف';
				}

				if (strlen($groupName) > 256) {
					$formErrors[] = 'اسم المجموعه لا يمكن ان يزيد عن 256 حرف';
				}

				if (empty($groupName)) {
					$formErrors[] = 'لا يمكن ترك اسم المجموعه فارغ';
				}
				

				// Loop Into Errors Array And Echo It

				foreach($formErrors as $error) {
					echo '<div class="alert alert-danger">' . $error . '</div>';
				}

				// Check If There's No Error Proceed The Update Operation

				if (empty($formErrors)) {
				
					// Check If User Exist in Database

					$check = checkItem("GroupName", "groups", $groupName);

					if ($check == 1) {

						$theMsg = '<div class="alert alert-danger">عذرا هذه المجموعه غير موجوده</div>';

						redirectHome($theMsg, 'back');

					} else {

						// Insert Userinfo In Database

						$stmt = $con->prepare("INSERT INTO groups(GroupName, MembersNo, FinalResult, CompID) VALUES(:zgroupName, :zmembersNo, :zfinalResult, :zcompID)");
						$stmt->execute(array(

							'zgroupName' 		=> $groupName,
							'zmembersNo' 		=> $membersNo,
							'zfinalResult' 	=> $finalResult,
							//'zteamLeaderId' 	=> $teamLeaderId,
							'zcompID' 		=> $compID,

						));

						// Echo Success Message

						$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' مجموعه جديدة تم اضافتها</div>';

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

			// Check If Get Request groupid Is Numeric & Get Its Integer Value

			$groupid = isset($_GET['groupid']) && is_numeric($_GET['groupid']) ? intval($_GET['groupid']) : 0;

			// Select All Data Depend On This ID

			$stmt = $con->prepare("SELECT * FROM groups WHERE GroupId = ? LIMIT 1");

			// Execute Query

			$stmt->execute(array($groupid));

			// Fetch The Data

			$row = $stmt->fetch();

			// The Row Count

			$count = $stmt->rowCount();

			// If There's Such ID Show The Form

			if ($count > 0) { ?>

				<h1 class="text-center">تعديل مجموعه</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=Update" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="groupid" value="<?php echo $groupid ?>" />
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="text" name="groupName" class="form-control" autocomplete="off" required="required" value="<?php echo $row['GroupName'] ?>" placeholder="اسم المجموعه" />
						</div>
						<label class="col-sm-2 control-label">اسم المجموعه</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="number" name="membersNo" class="form-control" autocomplete="off" value="<?php echo $row['MembersNo'] ?>" placeholder="عدد الاعضاء" />
							<i class="show-pass fa fa-eye fa-2x"></i>
						</div>
						<label class="col-sm-2 control-label">عدد الاعضاء</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="text" name="finalResult" class="form-control" autocomplete="off" value="<?php echo $row['FinalResult'] ?>" placeholder="اسم المتسابق بالكامل" />
						</div>
						<label class="col-sm-2 control-label">النتيجة النهائية</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<select name="teamLeaderId">
								<?php
									$allUsers = getAllFrom("*", "users", "WHERE GroupId = ".$row['GroupId']."", "", "UserId");
									foreach ($allUsers as $user) {
										echo "<option value='" . $user['UserId'] . "'"; 
										if ($row['TeamLeaderId'] == $user['UserId']) { echo 'selected'; } 
										echo ">" . $user['Username'] . "</option>";
									}
								?>
							</select>
						</div>
						<label class="col-sm-2 control-label">قائد المحموعه</label>
					</div>
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
						<label class="col-sm-2 control-label">المسابقة ؟</label>
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

			echo "<h1 class='text-center'>تعديل عضو</h1>";
			echo "<div class='container text-right'>";

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

				// Get Variables From The Form

				$groupid 		= $_POST['groupid'];
				$groupName 	= $_POST['groupName'];
				$membersNo 	= $_POST['membersNo'];
				$finalResult 	= $_POST['finalResult'];
				$teamLeaderId 	= $_POST['teamLeaderId'];
				$compID 		= $_POST['compID'];

				// Validate The Form

				$formErrors = array();

				if (strlen($groupName) < 4) {
					$formErrors[] = 'اسم المجموعه لا يمكن ان يقل عن 4 احرف';
				}

				if (strlen($groupName) > 256) {
					$formErrors[] = 'اسم المجموعه لا يمكن ان يزيد عن 256 حرف';
				}

				if (empty($groupName)) {
					$formErrors[] = 'لا يمكن ترك اسم المجموعه فارغ';
				}
				// Loop Into Errors Array And Echo It

				foreach($formErrors as $error) {
					echo '<div class="alert alert-danger">' . $error . '</div>';
				}

				// Check If There's No Error Proceed The Update Operation

				if (empty($formErrors)) {

					$stmt2 = $con->prepare("SELECT * FROM groups WHERE GroupName = ? AND GroupId != ?");

					$stmt2->execute(array($groupName, $groupid));

					$count = $stmt2->rowCount();

					if ($count == 1) {

						$theMsg = '<div class="alert alert-danger">عفوا هذه المجموعه غير موجود</div>';

						redirectHome($theMsg, 'back');

					}
						// Update The Database With This Info

						$stmt = $con->prepare("UPDATE groups SET GroupName = ?, MembersNo = ?, FinalResult = ?, TeamLeaderId = ?, CompID = ? WHERE GroupId = ?");

						$stmt->execute(array($groupName, $membersNo, $finalResult, $teamLeaderId, $compID, $groupid));

						// Echo Success Message

						$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' مجموعه تم تعديل بياناتها بنجاح</div>';

						redirectHome($theMsg, 'back');

					}

				} else {

				$theMsg = '<div class="alert alert-danger">عفوا لا يمكنك تصفح هذا المسار</div>';

				redirectHome($theMsg);

			}

			echo "</div>";

		} elseif ($do == 'Delete') { // Delete Groups Page

			echo "<h1 class='text-center'>حذف مجموعه</h1>";
			echo "<div class='container'>";

				// Check If Get Request groupid Is Numeric & Get The Integer Value Of It

				$groupid = isset($_GET['groupid']) && is_numeric($_GET['groupid']) ? intval($_GET['groupid']) : 0;

				// Select All Data Depend On This ID

				$check = checkItem('GroupId', 'groups', $groupid);

				// If There's Such ID Show The Form

				if ($check > 0) {

					$stmt = $con->prepare("DELETE FROM groups WHERE GroupId = :zgroupId");

					$stmt->bindParam(":zgroupId", $groupid);

					$stmt->execute();

					$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' مجموعه تم حذفها بنجاح</div>';

					redirectHome($theMsg, 'back');

				} else {

					$theMsg = '<div class="alert alert-danger">رقم المعرف هذا غير موجود</div>';

					redirectHome($theMsg);

				}

			echo '</div>';
                        
		}elseif ($do == 'Display') { // Display Members Page

			// Check If Get Request compid Is Numeric & Get The Integer Value Of It
			$groupid = isset($_GET['groupid']) && is_numeric($_GET['groupid']) ? intval($_GET['groupid']) : 0;
			
			$content = $con->prepare("SELECT
									users.*,
									groups.GroupName
								FROM
									users
								LEFT OUTER JOIN groups ON users.GroupId = groups.GroupId
								WHERE
									users.GroupId = ".$groupid."
								AND	
								users.RegStatus = 2
								ORDER BY
									UserId ASC");
			$content->execute();
			$rows = $content->fetchAll();

			if (!empty($rows)) {
				echo "<h1 class='text-center'>الفرق المشاركة فى ".$rows[0]['GroupName']."</h1>";
				echo "<div class='container'>";
			?>
			<div class="table-responsive">
				<table class="main-table manage-members text-center table table-bordered">
					<tr>
						<td>التحكم</td>
						<td>اسم المجموعه</td>
						<td>البريد الالكترونى</td>
						<td>اسم المتسابق</td>
						<td>اسم المستخدم</td>
						<td>الرقم التسلسلى</td>
					</tr>
					<?php
						foreach($rows as $row) {
							echo "<tr>";
								echo "<td>
									<a href='members.php?do=Edit&userid=" . $row['UserId'] . "' class='btn btn-success'><i class='fa fa-edit'></i> تعديل</a>
									<a href='members.php?do=Delete&userid=" . $row['UserId'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> حذف </a>";
								echo "</td>";
								echo "<td>" . $row['GroupName'] . "</td>";
								echo "<td>" . $row['Email'] . "</td>";
								echo "<td>" . $row['FullName'] . "</td>";
								echo "<td>" . $row['Username'] . "</td>";
								echo "<td>" . $row['UserId'] . "</td>";
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