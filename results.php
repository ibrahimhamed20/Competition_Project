<?php
	ob_start();
	session_start();
	$pageTitle = 'نتيجة '.$_SESSION['groupname'].' فى المسابقة';
     include 'init.php';
     
     $do = isset($_GET['do']) ? $_GET['do'] : 'Calculate';
     if ($do == 'Calculate') { 
          $contAll = $con->prepare("SELECT COUNT(QuestionID) AS TotalQuestions, SUM(Result) AS TotalPoints FROM questions WHERE CompID = ".$_SESSION['compid']."");
          $contAll->execute();
          $AllQuestions = $contAll->fetchAll();
          
          $stmt = $con->prepare("  SELECT
                                        SUM(Result) AS TotalResult,
                                        IF(Result > 0, COUNT(Result),
                                        0) AS RightSolulionCount
                                   FROM
                                        group_questions_answer
                                   WHERE
                                        GroupId = ".$_SESSION['groupid']."
                              ");

          // Execute The Statement

          $stmt->execute();
          // Assign To Variable 
          $rows = $stmt->fetchAll();
          if(!empty($rows) && !empty($AllQuestions)) {
               $CountQuest = $AllQuestions[0];
               $CompResult = $rows[0];

               $rightQuest = $con->prepare("SELECT COUNT(Result) AS RightSolulionCount FROM group_questions_answer WHERE GroupId = ".$_SESSION['groupid']." AND Result <> 0 LIMIT 1");
               $rightQuest->execute();
               $RightCounts = $rightQuest->fetchAll();

               $stmt = $con->prepare("UPDATE groups SET FinalResult = ? WHERE GroupId = ?");
			$stmt->execute(array($CompResult['TotalResult'], $_SESSION['groupid']));
          
?>
<section class="container">
     <div class="row question">
          <div class="register-form">
               <div class="col-sm-12">
                    <div class="title-line text-right">
                         <h4 style="font-weight:bold">نتيجة المسابقة (الاختبار)</h4>
                    </div> 
                    <br>
                    <div class="results text-center">
                         <h1 class="result-head">العدد الكلى للاسئلة : <strong><?php echo $CountQuest['TotalQuestions']; ?></strong> سؤال</h1>
                         <h2>عدد الإجابات الصحيحة : <strong><?php echo $RightCounts[0]['RightSolulionCount']; ?></strong> أسئلة</h2>
                         <h2>عدد النقاط : <strong><?php echo $CountQuest['TotalPoints']; ?> / <?php echo $CompResult['TotalResult']; ?></strong></h2>
                    </div>
                    <a class="btn btn-primary btn-lg" href="index.php" role="button">الصفحة الرئيسية</a>
                    <!-- <a class="btn btn-primary btn-lg" href="finalResults.php" role="button">النتيجة النهائية للمسابقة</a> -->
               </div>
          </div>
     </div>
</section>
<?php
}

include $tpl . 'footer.php'; 
} else {
     header('Location: index.php');

     exit();
}
	ob_end_flush();
?>