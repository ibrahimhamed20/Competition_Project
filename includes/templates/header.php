<!DOCTYPE html>
<html lang="ar">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="" />
				<meta name="keywords" content="responsive, template, BootCamp, html, css, javascript" />
        <title><?php getTitle() ?></title>
        <link href="<?php echo $css ?>bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo $css ?>font-awesome.min.css" rel="stylesheet">
        <link href="<?php echo $css ?>font.css" rel="stylesheet">
        <link href="<?php echo $css ?>style.css" rel="stylesheet">
				<link href="./src/img/logo.png" rel="icon">
    </head>
    <body dir="rtl">
        <section class="container">
            <div class="header-main text-center">
                <div class="pattern"></div>
                <h1>برنامج المسابقة</h1><br>
                <?php
                        if (isset($_SESSION['groupname'])) {?>
                            <!-- <h3>مرحبا <!?php echo $sessionUser ?></h3> -->
                            <h3>فريق <strong><?php echo $_SESSION['groupname']; ?></strong></h3>
                <?php
                    }
                ?>