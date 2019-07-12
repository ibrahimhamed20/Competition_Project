<?php
	ob_start();
	session_start();
	$pageTitle = 'برنامج المسابقة';
	include 'init.php';
?>
	<h3 class="text-center">مسابقات إلكترونية ذات طابع نموذجي دراسي</h3>
	<br>
	<img src="layout/img/logo.png" alt="Logo" width="10%"/>
	<br><br>
	<?php if (!isset($_SESSION['user'])) { ?>
	<p>
		<a class="btn btn-primary btn-lg" href="login.php" role="button">تسجيل الدخول للمسابقة</a>
	</p>
	<?php } else { if(isset($_SESSION['compname'])){ ?>
		<p>
		<a class="btn btn-primary btn-lg" href="instructions.php" role="button">بدء <?php echo $_SESSION['compname']; ?></a>
		<!-- <a class="btn btn-primary btn-lg" href="finalResults.php" role="button">نتائج المسابقة</a> -->
		<a class="btn btn-primary btn-lg" href="logout.php" role="button">تسجيل الخروج</a>
	</p>
	<?php } } ?>
<?php
	include $tpl . 'footer.php'; 
	ob_end_flush();
?>