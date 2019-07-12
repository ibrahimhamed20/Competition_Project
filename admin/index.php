<?php
	session_start();
	$noNavbar = '';
	$pageTitle = 'تسجيل الدخول - لوحة التحكم';

	if (isset($_SESSION['Username'])) {
		header('Location: dashboard.php'); // Redirect To Dashboard Page
	}

	include 'init.php';

	// Check If User Coming From HTTP Post Request

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$username = $_POST['user'];
		$password = $_POST['pass'];
		$hashedPass = sha1($password);

		// Check If The User Exist In Database

		$stmt = $con->prepare("SELECT UserID, Username, Password FROM users WHERE Username = ? AND Password = ? AND RegStatus = 1 LIMIT 1");

		$stmt->execute(array($username, $hashedPass));
		$row = $stmt->fetch();
		$count = $stmt->rowCount();

		// If Count > 0 This Mean The Database Contain Record About This Username

		if ($count > 0) {
			$_SESSION['Username'] = $username; // Register Session Name
			$_SESSION['ID'] = $row['UserID']; // Register Session ID
			header('Location: dashboard.php'); // Redirect To Dashboard Page
			exit();
		}

	}

?>


<link rel="stylesheet" type="text/css" href="css/style.css">
<form class="login" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
	<div class="login">
		<div class="login-screen">
			<div class="app-title">
				<h2>تسجيل الدخول</h2>
			</div>

			<div class="login-form">
				<div class="control-group">
				<input type="text" name="user" class="login-field" value="" placeholder="اسم المستخدم" id="login-name">
				<label class="login-field-icon fui-user" for="login-name"></label>
				</div>

				<div class="control-group">
				<input type="password" name="pass" class="login-field" value="" placeholder="كلمة المرور" id="login-pass">
				<label class="login-field-icon fui-lock" for="login-pass"></label>
				</div>

				<input type="submit" value="دخول" class="btn btn-primary btn-large btn-block" />
				
			</div>
		</div>
	</div>
	</form>

<?php include $tpl . 'footer.php'; ?>