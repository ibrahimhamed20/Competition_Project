<?php

	/*
	================================================
	== Manage Members Page
	== You Can Add | Edit | Delete Members From Here
	================================================
	*/

	ob_start(); // Output Buffering Start

	session_start();

	$pageTitle = 'المتسابقون';

	if (isset($_SESSION['Username'])) {

		include 'init.php';

		$do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

		// Start Manage Page

		if ($do == 'Manage') { // Manage Members Page

			$query = '';

			// if (isset($_GET['page']) && $_GET['page'] == 'Pending') {

			// 	$query = 'AND RegStatus = 0';

			// }

			// Select All Users Except Admin 

			$stmt = $con->prepare("SELECT users.*, groups.GroupName  FROM users LEFT OUTER JOIN groups ON users.GroupId = groups.GroupId WHERE users.RegStatus = 2 ORDER BY UserId ASC");

			// Execute The Statement

			$stmt->execute();

			// Assign To Variable 

			$rows = $stmt->fetchAll();

			if (! empty($rows)) {

			?>

			<h1 class="text-center">إدارة المتسابقون</h1>
			<div class="container text-right">
				<a href="members.php?do=Add" class="btn btn-primary mb-2">
					<i class="fa fa-plus"></i> عضو جديد
				</a>
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
				
			</div>

			<?php } else {

				echo '<div class="container">';
					echo '<div class="nice-message">لا يوجد متسابقون لعرضهم</div>';
					echo '<a href="members.php?do=Add" class="btn btn-primary">
							<i class="fa fa-plus"></i> عضو جديد
						</a>';
				echo '</div>';

			} ?>

		<?php 

		} elseif ($do == 'Add') { // Add Page ?>

			<h1 class="text-center">إضافة عضو جديد</h1>
			<div class="container">
				<form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="text" name="username" class="form-control" autocomplete="off" required="required" placeholder="اسم المستخدم للانضام لفريق مسابقه" />
						</div>
						<label class="col-sm-2 control-label">اسم المستخدم</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<i class="show-pass fa fa-eye fa-2x"></i>
							<input type="password" name="password" class="password form-control" required="required" autocomplete="new-password" placeholder="كلمة المرور يجب ان تكون معقدة" />
						</div>
						<label class="col-sm-2 control-label">كلمة المرور</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="text" name="fullName" class="form-control" autocomplete="off" required="required" placeholder="اسم المتسابق بالكامل" />
						</div>
						<label class="col-sm-2 control-label">اسم المتسابق</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="email" name="email" class="form-control" autocomplete="off" required="required" placeholder="البريد الالكترونى للمتسابق" />
						</div>
						<label class="col-sm-2 control-label">البريد الالكترونى</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<select name="groupId">
								<option value="0">...</option>
								<?php
									$allGroups = getAllFrom("*", "groups", "", "", "GroupId");
									foreach ($allGroups as $group) {
										echo "<option value='" . $group['GroupId'] . "'>" . $group['GroupName'] . "</option>";
									}
								?>
							</select>
						</div>
						<label class="col-sm-2 control-label">المجموعه</label>
					</div>
					<!-- Start Submit Field -->
					<div class="form-group form-group-lg">
						<div class="text-center col-sm-10 col-sm-offset-2">
							<input type="submit" value="إضافة عضو" class="btn btn-primary btn-lg" />
						</div>
					</div>
					<!-- End Submit Field -->
				</form>
			</div>

		<?php 

		} elseif ($do == 'Insert') {

			// Insert Member Page

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

				echo "<h1 class='text-center'>إضافة متسابق جديد</h1>";
				echo "<div class='container'>";

				// Get Variables From The Form

				$user 	= $_POST['username'];
				$pass 	= $_POST['password'];
				$email 	= $_POST['email'];
				$fullName = $_POST['fullName'];
				$groupId 	= $_POST['groupId'];

				$hashPass = sha1($_POST['password']);

				// Validate The Form

				$formErrors = array();

				if (strlen($user) < 4) {
					$formErrors[] = 'اسم المستخدم لا يمكن ان يقل عن 4 احرف';
				}

				if (strlen($user) > 20) {
					$formErrors[] = 'اسم المستخدم لا يمكن ان يزيد عن 20 حرف';
				}

				if (empty($user)) {
					$formErrors[] = 'لا يمكن ترك اسم المستخدم فارغ';
				}

				if (empty($pass)) {
					$formErrors[] = 'لا يمكن ترك الباسورد فارغ';
				}

				if (empty($groupId)) {
					$formErrors[] = 'لا يمكن ترك حقل المجموعه فارغ';
				}
				

				// Loop Into Errors Array And Echo It

				foreach($formErrors as $error) {
					echo '<div class="alert alert-danger">' . $error . '</div>';
				}

				// Check If There's No Error Proceed The Update Operation

				if (empty($formErrors)) {
				
					// Check If User Exist in Database

					$check = checkItem("Username", "users", $user);

					if ($check == 1) {

						$theMsg = '<div class="alert alert-danger">عذرا هذا المستخدم غير موجود</div>';

						redirectHome($theMsg, 'back');

					} else {

						// Insert Userinfo In Database

						$stmt = $con->prepare("INSERT INTO users(Username, Password, FullName, Email, GroupId) VALUES(:zuser, :zpass, :zfullName, :zemail, :zgroupId)");
						$stmt->execute(array(

							'zuser' 		=> $user,
							'zpass' 		=> $hashPass,
							'zfullName' 	=> $fullName,
							'zemail' 		=> $email,
							'zgroupId' 	=> $groupId,

						));

						// Echo Success Message

						$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' عضو جديد تم اضافته</div>';

						redirectHome($theMsg, 'back');

					}
					

				}

			} else {

				echo "<div class='container'>";

				$theMsg = '<div class="alert alert-danger">عفوا لايمكنك  التصفح لهذا المسار</div>';

				redirectHome($theMsg);

				echo "</div>";

			}

			echo "</div>";

		} elseif ($do == 'Edit') {

			// Check If Get Request userid Is Numeric & Get Its Integer Value

			$userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;

			// Select All Data Depend On This ID

			$stmt = $con->prepare("SELECT * FROM users WHERE UserId = ? LIMIT 1");

			// Execute Query

			$stmt->execute(array($userid));

			// Fetch The Data

			$row = $stmt->fetch();

			// The Row Count

			$count = $stmt->rowCount();

			// If There's Such ID Show The Form

			if ($count > 0) { ?>

				<h1 class="text-center">تعديل عضو</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=Update" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="userid" value="<?php echo $userid ?>" />
						<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="text" name="username" class="form-control" autocomplete="off" required="required" value="<?php echo $row['Username'] ?>" />
						</div>
						<label class="col-sm-2 control-label">اسم المستخدم</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="hidden" name="oldpassword" value="<?php echo $row['Password'] ?>" />
							<input type="password" name="newpassword" class="form-control" autocomplete="new-password" placeholder="اتركه فارغا ان لم تكن تريد تغييره" />
						</div>
						<label class="col-sm-2 control-label">كلمة المرور</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="text" name="fullName" class="form-control" autocomplete="off" required="required" value="<?php echo $row['FullName'] ?>" />
						</div>
						<label class="col-sm-2 control-label">اسم المتسابق</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<input type="email" name="email" class="form-control" autocomplete="off" required="required" value="<?php echo $row['Email'] ?>" />
						</div>
						<label class="col-sm-2 control-label">البريد الالكترونى</label>
					</div>
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-6 col-md-offset-4">
							<select name="GroupId">
								<?php
									$allGroups = getAllFrom("*", "groups", "", "", "GroupId");
									foreach ($allGroups as $group) {
										echo "<option value='" . $group['GroupId'] . "'"; 
										if ($row['GroupId'] == $group['GroupId']) { echo 'selected'; } 
										echo ">" . $group['GroupName'] . "</option>";
									}
								?>
							</select>
						</div>
						<label class="col-sm-2 control-label">المجموعه</label>
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

				echo "<div class='container'>";

				$theMsg = '<div class="alert alert-danger">رقم المعرف غير موجود</div>';

				redirectHome($theMsg);

				echo "</div>";

			}

		} elseif ($do == 'Update') { // Update Page

			echo "<h1 class='text-center'>تعديل عضو</h1>";
			echo "<div class='container'>";

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

				// Get Variables From The Form

				$id 		= $_POST['userid'];
				$user 	= $_POST['username'];
				$email 	= $_POST['email'];
				$fullName = $_POST['fullName'];
				$GroupId 	= $_POST['GroupId'];
                                
				// Password Trick

				$pass = empty($_POST['newpassword']) ? $_POST['oldpassword'] : sha1($_POST['newpassword']);

				// Validate The Form

				$formErrors = array();

				if (strlen($user) < 4) {
					$formErrors[] = 'اسم المستخدم لا يمكن ان يقل عن 4 احرف';
				}

				if (strlen($user) > 20) {
					$formErrors[] = 'اسم المستخدم لا يمكن ان يزيد عن 20 حرف';
				}

				if (empty($user)) {
					$formErrors[] = 'لا يمكن ترك اسم المستخدم فارغ';
				}

				if (empty($GroupId)) {
					$formErrors[] = 'لا يمكن ترك حقل المجموعه فارغ';
				}
				// Loop Into Errors Array And Echo It

				foreach($formErrors as $error) {
					echo '<div class="alert alert-danger">' . $error . '</div>';
				}

				// Check If There's No Error Proceed The Update Operation

				if (empty($formErrors)) {

					$stmt2 = $con->prepare("SELECT * FROM users WHERE Username = ? AND UserId != ?");

					$stmt2->execute(array($user, $id));

					$count = $stmt2->rowCount();

					if ($count == 1) {

						$theMsg = '<div class="alert alert-danger">عفوا هذا المستخدم غير موجود</div>';

						redirectHome($theMsg, 'back');

					}
						// Update The Database With This Info

						$stmt = $con->prepare("UPDATE users SET Username = ?, Password = ?, FullName = ?, Email = ?, GroupId = ? WHERE UserId = ?");

						$stmt->execute(array($user, $pass, $fullName, $email, $GroupId, $id));

						// Echo Success Message

						$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' عضو تم تعديل بياناته بنجاح</div>';

						redirectHome($theMsg, 'back');

					}

				} else {

				$theMsg = '<div class="alert alert-danger">عفوا لا يمكنك تصفح هذا المسار</div>';

				redirectHome($theMsg);

			}

			echo "</div>";

		} elseif ($do == 'Delete') { // Delete Member Page

			echo "<h1 class='text-center'>حذف عضو</h1>";
			echo "<div class='container'>";

				// Check If Get Request userid Is Numeric & Get The Integer Value Of It

				$userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;

				// Select All Data Depend On This ID

				$check = checkItem('userid', 'users', $userid);

				// If There's Such ID Show The Form

				if ($check > 0) {

					$stmt = $con->prepare("DELETE FROM users WHERE UserId = :zuser");

					$stmt->bindParam(":zuser", $userid);

					$stmt->execute();

					$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' عضو تم حذفه بنجاح</div>';

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