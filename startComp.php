<?php
ob_start();
session_start();
$pageTitle = 'تعليمات المسابقة';
if (isset($_SESSION['user'])) {
    include 'init.php';
    $do = isset($_GET['do']) ? $_GET['do'] : 'Add';
    if ($do == 'Add') { // Add Page
        //$GroupAnswers = getAllFrom("*", "group_questions_answer", "WHERE GroupId = ".$_SESSION['groupid']."", "", "QuestionID");
        //$Question = getAllFrom("*", "questions", "WHERE CompID = ".$_SESSION['compid']."", "", "Ordering LIMIT 1");
        $stmt = $con->prepare("  SELECT
                                        *
                                   FROM
                                        questions
                                   WHERE
                                        questions.QuestionID NOT IN(
                                        SELECT
                                             QuestionID
                                        FROM
                                             group_questions_answer
                                        WHERE
                                             group_questions_answer.GroupId = " . $_SESSION['groupid'] . "
                                   ) AND questions.CompID = " . $_SESSION['compid'] . "
                              ");

        // Execute The Statement

        $stmt->execute();

        // Assign To Variable

        $rows = $stmt->fetchAll();

        if (!empty($rows)) {

            $TopQuestion = $rows[0]; //getAllFrom("*", "questions", "WHERE QuestionID NOT IN( SELECT QuestionID FROM group_questions_answer WHERE QuestionID IN( SELECT QuestionID FROM questions WHERE CompID = ".$_SESSION['compid']." ))", "", "Ordering LIMIT 1");
            $Answers = getAllFrom("*", "mcqsolutions", "WHERE QuestionID = " . $TopQuestion['QuestionID'] . "", "", "SolutionID")

            ?>
<div class="container">
     <div class="row countdown text-center">
          <h3 style="color:#764ABC">الوقت المتبقي</h3>
          <span><strong class="hr"> 00 </strong><i>ساعة</i></span>
          <span><strong class="min"> <?php echo $TopQuestion['Minutes']; ?> </strong><i>دقيقة</i></span>
          <span><strong class="sec"> 00 </strong><i> ثانية</i></span>
     </div>
     <div class="row question">
          <form class="register-form text-right" action="?do=Insert" method="POST" enctype="multipart/form-data">
          <div class="col-sm-4 position-example">
               <?php if ($TopQuestion['MediaFlag'] == 1) {?>
               <img class="img-responsive img-thumbnail" src="admin/img/service/<?php echo $TopQuestion['avatar']; ?>">
               <?php } elseif ($TopQuestion['MediaFlag'] == 2) {?>
               <video class="" width="320" height="240" controls autoplay>
                    <source src="admin/img/service/<?php echo $TopQuestion['avatar']; ?>" type="video/mp4">
                    <source src="admin/img/service/<?php echo $TopQuestion['avatar']; ?>" type="video/ogg">
                    Your browser does not support the video tag.
               </video>
               <?php } else {?>
               <audio class="" controls autoplay>
                    <source src="admin/img/service/<?php echo $TopQuestion['avatar']; ?>" type="audio/ogg">
                    <source src="admin/img/service/<?php echo $TopQuestion['avatar']; ?>" type="audio/mpeg">
                    Your browser does not support the audio element.
               </audio>
               <?php }?>
          </div>
          <div class="col-sm-8 position-test">
               <div class="title-line text-right">
                    <h3>السؤال رقم <?php echo $TopQuestion['Ordering']; ?> </h3>
               </div>
               <div class="form-group">
                    <label><?php echo $TopQuestion['QuestionDesc']; ?></label>
                    <input type="hidden" name="questionID" value="<?php echo $TopQuestion['QuestionID'] ?>" />
               </div>
               <br>
               <?php
               if ($TopQuestion['QuestionType'] == 1) {
                    foreach ($Answers as $answer) {
                         echo '<div class="form-group">';
                         echo '<input type="radio" name="solutionID" value="' . $answer['SolutionID'] . '">' . $answer['SolutionTitle'];
                         echo '</div>';
                    }
               } else {
                    echo '<div class="form-group">';
                    echo '<textarea name="answer" rows="5" class="form-control" style="width: 50%;"></textarea>';
                    echo '</div>';
               }
            ?>
               <div class="form-group" >
                    <button class="btn btn-primary btn-lg" type="submit" type="button">التالى</button>
               </div>
          </div>
          
          </form>
     </div>

</div>

<?php
} else {
            echo '<a class="btn btn-primary btn-lg" href="results.php">الحصول على النتيجة</a>';
        }
    } elseif ($do == 'Insert') {
        // Insert Member Page

          if ($_SERVER['REQUEST_METHOD'] == 'POST') {

               echo "<h1 class='text-center'>اجابة السؤال</h1>";
               echo "<div class='container'>";

               // Get Variables From The Form
               $groupId = $_SESSION['groupid'];
               $questionID = intval($_POST['questionID']);

               // get right choice and result --> if solution not empty --> set soltion with post soltion
               $point = getAllFrom('McqSolution, Result', 'questions', 'WHERE QuestionID = ' . $questionID, '', 'QuestionID', ' LIMIT 1');
               
               // if (empty($solutionID)) {
               //      $resultPoint = 0;
               // } else {
               //      // if posted solution id == mcq solution in questions ==> set result points with result of question
               // }
               $solutionID = 0;
               if(!empty($_POST['solutionID'])){
                    if($_POST['solutionID'] == $point[0]['McqSolution']){
                         $solutionID = intval($_POST['solutionID']);
                         $resultPoint = intval($point[0]['Result']);
                    } else {
                         $solutionID = 0;
                         $resultPoint = 0;
                    }
               } else {
                    $solutionID = 0;
               }

               // get right answer and set result and answer if not empty
               $sol = getAllFrom('TextSolution, Result', 'questions', 'WHERE QuestionID = ' . $questionID, '', 'QuestionID', ' LIMIT 1');
               // if (empty($answer)) {
               //      $answer = '';
               //      $resultPoint = 0;
               // } else {
                    
               // }
               $answer = '';
               if(!empty($_POST['answer'])) {
                    if ($_POST['answer'] == $sol[0]['TextSolution']) {
                         $answer = $_POST['answer'];
                         $resultPoint = intval($sol[0]['Result']);
                    } else {
                         $answer = '';
                         $resultPoint = 0;
                    }
               } else {
                    $answer = '';
               }

            // Validate The Form

            $formErrors = array();

            // if (empty($solutionID)) {
            //      $formErrors[] = 'لا يمكن تخطي السؤال بدون حله';
            // }

            // Loop Into Errors Array And Echo It

            foreach ($formErrors as $error) {
                echo '<div class="alert alert-danger">' . $error . '</div>';
            }

            // Check If There's No Error Proceed The Update Operation

            if (empty($formErrors)) {

                // Check If User Exist in Database

                $check = checkItem("SolutionID", "mcqsolutions", $solutionID);
                if ($check > 1) {

                    $theMsg = '<div class="alert alert-danger">عذرا هذه الاجابة غير موجوده</div>';

                    redirectHome($theMsg, 'back');

                } else {

                    // Insert Userinfo In Database
                    $stmt = $con->prepare("INSERT INTO group_questions_answer(QuestionID, SolutionID, Answer, GroupId, Result) VALUES(:zquestionID, :zsolutionID, :zanswer, :zgroupId, :zresultPoint)");
                    $stmt->execute(array(
                        'zquestionID' => $questionID,
                        'zsolutionID' => $solutionID,
                        'zanswer' => $answer,
                        'zgroupId' => $groupId,
                        'zresultPoint' => $resultPoint,
                    ));

                    // Echo Success Message

                    $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' اجابة جديدة تم اضافتها</div>';

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

    }

    //echo '<a class="btn btn-primary btn-lg" href="results.php">الحصول على النتيجة</a>';
    include $tpl . 'footer.php';
} else {

    header('Location: index.php');

    exit();
}

ob_end_flush(); // Release The Output
?>