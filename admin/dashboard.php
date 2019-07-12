<?php

	ob_start(); // Output Buffering Start

	session_start();

	if (isset($_SESSION['Username'])) {

		$pageTitle = 'لوحة التحكم - تطبيق مسابقاتي';

		include 'init.php';

		/* Start Dashboard Page */

		$numUsers = 6; // Number Of Latest Users

		$latestUsers = getLatest("*", "users", "UserId", $numUsers); // Latest Users Array

		$numComps = 6; // Number Of Latest Items

		$latestComps = getLatest("*", 'competitions', 'CompID', $numComps); // Latest Items Array

		?>

		<div class="home-stats">
			<div class="container text-center">
				<h1>لوحة التحكم</h1>
				<div class="row">
					<div class="col-md-6">
						<div class="stat st-members">
							<i class="fa fa-users"></i>
							<div class="info">
								كل الاعضاء
								<span>
									<a href="members.php"><?php echo countItems('UserId', 'users') ?></a>
								</span>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="stat st-items">
							<i class="fa fa-tag"></i>
							<div class="info">
								كل المسابقات
								<span>
									<a href="competitions.php"><?php echo countItems('CompID', 'competitions') ?></a>
								</span>
							</div>
						</div>
					</div>
			</div>
		</div>

		<div class="latest">
			<div class="container">
				<div class="row">
					<div class="col-sm-6">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-users"></i> 
								اخر <?php echo $numUsers ?> اعضاء مسجلين 
								<span class="toggle-info pull-right">
									<i class="fa fa-plus fa-lg"></i>
								</span>
							</div>
							<div class="panel-body">
								<ul class="list-unstyled latest-users">
								<?php
									if (! empty($latestUsers)) {
										foreach ($latestUsers as $user) {
											echo '<li>';
												echo $user['Username'];
												echo '<a href="members.php?do=Edit&userid=' . $user['UserId'] . '">';
													echo '<span class="btn btn-success pull-right">';
														echo '<i class="fa fa-edit"></i> تعديل';
													echo '</span>';
												echo '</a>';
											echo '</li>';
										}
									} else {
										echo 'لا يوجد اعضاء لعرضهم';
									}
								?>
								</ul>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-tag"></i> اخر <?php echo $numComps ?> مسابقات 
								<span class="toggle-info pull-right">
									<i class="fa fa-plus fa-lg"></i>
								</span>
							</div>
							<div class="panel-body">
								<ul class="list-unstyled latest-users">
									<?php
										if (! empty($latestComps)) {
											foreach ($latestComps as $comp) {
												echo '<li>';
													echo $comp['CompName'];
													echo '<a href="competitions.php?do=Edit&compid=' . $comp['CompID'] . '">';
														echo '<span class="btn btn-success pull-right">';
															echo '<i class="fa fa-edit"></i> تعديل';
															
														echo '</span>';
													echo '</a>';
												echo '</li>';
											}
										} else {
											echo 'لا يوجد مسابقات لعرضها';
										}
									?>
								</ul>
							</div>
						</div>
					</div>
				</div>
				
				
			</div>
		</div>

		<?php

		/* End Dashboard Page */

		include $tpl . 'footer.php';

	} else {

		header('Location: index.php');

		exit();
	}

	ob_end_flush(); // Release The Output

?>