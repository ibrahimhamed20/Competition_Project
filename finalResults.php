<?php
     ob_start();
     session_start();
     $pageTitle = 'نتيجة ' . $_SESSION['groupname'] . ' فى المسابقة';
     include 'init.php';

     $do = isset($_GET['do']) ? $_GET['do'] : 'Calculate';
     if ($do == 'Calculate') {
          // get
          $stat = $con->prepare("SELECT * FROM groups ORDER BY FinalResult DESC");
          $stat->execute();
          $AllGroupsGrades = $stat->fetchAll();

          if (!empty($AllGroupsGrades)) {
               $WinnerGroup = $AllGroupsGrades[0];

               $winStmt = $con->prepare("UPDATE competitions SET WinnerGroupId = ? WHERE CompID = ?");
               $winStmt->execute(array($WinnerGroup['GroupId'], $_SESSION['compid']));

?>
<div class="panel-group" style="margin-top: 30px;">
  <div class="panel panel-success panel-shadow">
  <div class="panel-heading" style="font-weight: bold;letter-spacing: 1px;">الفريق الفائز بالمسابقة</div>
    <div class="panel-body text-success" style="font-weight: bold;letter-spacing: 1px;"><?php echo $WinnerGroup['GroupName'];?></div>
  </div>
  <hr />
  <?php 
     $index = 0;
     foreach ($AllGroupsGrades as $group) {
          $index++;
          if($index > 1){
               echo '<div class="panel panel-warning panel-shadow">';
                    echo '<div class="panel-heading" style="font-weight: bold;letter-spacing: 1px;">';
                         echo 'المرتبة رقم '.$index;
                    echo '</div>';
                    echo '<div class="panel-body text-warning" style="font-weight: bold;letter-spacing: 1px;">'.$group['GroupName'].'</div>';
               echo '</div>';
          }
     }
  ?>

</div>
<a class="btn btn-primary btn-lg" href="index.php" role="button">الصفحة الرئيسية</a>
<a class="btn btn-primary btn-lg" href="logout.php" role="button">تسجيل الخروج</a>
<?php
}
    include $tpl . 'footer.php';
} else {
    header('Location: index.php');
    exit();
}
ob_end_flush();
?>