<nav class="navbar navbar-inverse">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-nav" aria-expanded="false">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <!-- <a class="navbar-brand xyz" href="dashboard.php"><!?php echo lang('HOME_ADMIN') ?></a> -->
    </div>
    <div class="collapse navbar-collapse" id="app-nav">
      <ul class="nav navbar-nav navbar-right">
      <li><a href="mcq.php">الإجابات</a></li>
      <li><a href="questions.php">أسئلة المسابقة</a></li>
      <li><a href="groups.php">المجموعات</a></li>
        <li><a href="competitions.php">المسابقات</a></li>
        <li><a href="members.php"><?php echo lang('MEMBERS') ?></a></li>
        <li><a href="dashboard.php"><?php echo lang('HOME_ADMIN') ?></a></li>
      </ul>
      <ul class="nav navbar-nav">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="caret"></span> <?php echo $_SESSION['Username'];?></a>
          <ul class="dropdown-menu">
            <li><a href="../index.php">زيارة الموقع</a></li>
            <li><a href="members.php?do=Edit&userid=<?php echo $_SESSION['ID'] ?>">تعديل البروفايل</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="logout.php">تسجيل الخروج</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>