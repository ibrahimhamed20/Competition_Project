<?php
ob_start();
session_start();
$pageTitle = 'تسجيل الدخول للمسابقة';
if (isset($_SESSION['user'])) {
    header('Location: index.php');
}
include 'init.php';

// Check If User Coming From HTTP Post Request

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['login'])) {

        $user = $_POST['username'];
        $pass = $_POST['password'];
        $hashedPass = sha1($pass);

        // Check If The User Exist In Database

        $stmt = $con->prepare("SELECT users.UserID,
	   						   users.Username,
							   users.PASSWORD,
							   users.GroupId,
							   groups.GroupName,
							   competitions.CompID,
							   competitions.CompName
    						FROM
	   						users
    						LEFT OUTER JOIN groups ON users.GroupId = groups.GroupId
    						LEFT OUTER JOIN competitions ON competitions.CompID = groups.CompID
						WHERE
							Username = ?
						AND
                            Password = ?
                        AND RegStatus != 1");

        $stmt->execute(array($user, $hashedPass));

        $get = $stmt->fetch();

        $count = $stmt->rowCount();

        // If Count > 0 This Mean The Database Contain Record About This Username

        if ($count > 0) {

            $_SESSION['user'] = $user; // Register Session Name

		  $_SESSION['uid'] = $get['UserID']; // Register User ID in Session
		  
		  $_SESSION['groupid'] = $get['GroupId']; // Register Group ID in Session
		  $_SESSION['groupname'] = $get['GroupName']; // Register Group Name in Session
		  $_SESSION['compid'] = $get['CompID']; // Register Comp ID in Session
		  $_SESSION['compname'] = $get['CompName']; // Register Comp ID in Session

            header('Location: index.php'); // Redirect To Dashboard Page

            exit();
        }

    }
}

?>

<div class="container login-page">
	<h3 class="text-center">
		<span class="selected" data-class="login">تسجيل الدخول للمسابقة</span>
	</h3>
	<!-- Start Login Form -->
	<section class="container">
            <div class="header-main text-center">
                <div class="pattern"></div>
                <div class="row">
                    <form class="register-form" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-8">
                            <div class="title-line text-right">
                                <h3>يرجي إدخال بياناتك</h3>
                            </div>
                            <div class="form-group">
                                <label>اسم المستخدم</label>
                                <input type="text" name="username" class="form-control" placeholder="اسم المستخدم" required>
                            </div>
                            <div class="form-group">
                                <label>كلمة المرور</label>
                                <input type="password" name="password" class="form-control" placeholder="كلمة المرور" required>
                            </div>

                            <div class="form-group" >
                                <button class="btn btn-primary btn-lg" role="button" name="login" type="submit" >تسجيل الدخول</button>
                            </div>
                        </div>
                        <div class="col-sm-2"></div>
                    </form>
                </div>
            </div>
        </section>
	<!-- End Login Form -->
	<div class="the-errors text-center">
		<?php

if (!empty($formErrors)) {

    foreach ($formErrors as $error) {

        echo '<div class="msg error">' . $error . '</div>';

    }

}

if (isset($succesMsg)) {

    echo '<div class="msg success">' . $succesMsg . '</div>';

}

?>
	</div>
</div>

<?php
include $tpl . 'footer.php';
ob_end_flush();
?>