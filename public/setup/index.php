<!DOCTYPE html><html lang="en"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Home | Boostack - a full stack web layer for PHP | getboostack.com</title>
    <meta name="description" content="Improve your development and build a modern website in minutes">
    <meta name="author" content="stefano spagnolo"><meta content="boostack, php, framework, website, productive, simplicity, seo, secure, mysql, open-source" name="Keywords" />
    <meta content="INDEX, FOLLOW" name="ROBOTS" />
    <link rel="shortcut icon" href="../assets/img/favicon.ico" /><link rel="image_src" href="img/boostack_logo_x210.png" />
    <link href="../assets/css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/lib/animate.min.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
</head>
<body class="setup">
<section class="disclaimer sectionTitle">
    <div class="line">
        <div class="container">
            <div class="row">
                <h1 class="w-100">Boostack Setup</h1>
                <p>This script checks if your server and PHP configuration meets the requirements for running Boostack.</p>
            </div>
        </div>
    </div>
</section>
<section class="checker">
        <div class="container">
            <div class="row">
                <?php
                if(!empty($_POST)) {
                    require_once "setup-save.php";
                } else {
                    require_once "setup.php";
                }
                ?>
            </div>
        </div>
</section>
<script type="text/javascript" src="../assets/js/lib/jquery.min.js"></script>
<script type="text/javascript" src="../assets/js/lib/popper.min.js"></script>
<script type="text/javascript" src="../assets/js/lib/bootstrap.min.js"></script>

<script type="text/javascript" src="../assets/js/custom.js"></script>
<!--[if lt IE 9]>        <script type="text/javascript" src="../assets/js/html5shiv.js"></script>
        <script type="text/javascript" src="../assets/js/respond.min.js"></script>
        -->
<script type="text/javascript" src="setup.js"></script>
</body></html>