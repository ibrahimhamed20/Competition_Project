<?php
	ob_start();
	session_start();
	$pageTitle = 'تعليمات المسابقة';
	include 'init.php';
?>
<section class="container">
     <div class="header-main text-center">
          <h4 class="text-right">تعليمات المسابقة :-</h4>
          <ol class="list-group text-right">
               <li class="list-group-item list-group-item-default">عند بدأ الاسئلة يجب مراعاة الدقة فى الاجاية وعدم التسرع فى الاجابة.</li>
               <li class="list-group-item list-group-item-default">لكل سؤال من الاسئلة الموجوده فى المسابقة وقت محدد يغلق بعدها السؤال.</li>
               <li class="list-group-item list-group-item-default">عليك الاجابة على السؤال قبل تخطي الوقت المحدد له والا تحصل على صفر فى هذا السؤال.</li>
               <li class="list-group-item list-group-item-default">بالتوفيق فى المسابقة لكل الفريق......</li>
          </ol>
          <p><a class="btn btn-primary btn-lg" href="startComp.php" role="button">إلى المسابقة</a></p>
     </div>
     </section>
<?php
	include $tpl . 'footer.php'; 
	ob_end_flush();
?>