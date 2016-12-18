<!DOCTYPE html><html lang="en" xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Home | Boostack - a full stack web layer for PHP | Boostack.com</title>
    <meta name="description" content="Improve your development and build a modern website in minutes"><meta name="author" content="stefano spagnolo"><meta content="boostack, php, framework, website, productive, simplicity, seo, secure, mysql, open-source" name="Keywords" /><meta content="INDEX, FOLLOW" name="ROBOTS" />
    <link rel="shortcut icon" href="img/favicon.ico" /><link rel="image_src" href="img/boostack_logo_x210.png" />
    <link href="../assets/css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/lib/animate.min.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
</head>
<body class="setup">
<section class="disclaimer sectionTitle">
    <div class="line">
        <div class="container">
            <div class="row">
                <h1>Boostack Setup</h1>
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
<script type="text/javascript" src="../assets/js/lib/bootstrap.min.js"></script>
<script type="text/javascript" src="../assets/js/custom.js"></script>
<!--[if lt IE 9]>        <script type="text/javascript" src="../assets/js/html5shiv.js"></script>
        <script type="text/javascript" src="../assets/js/respond.min.js"></script>
        -->
<script type="text/javascript">
    $(document).ready(function(){
        $("input[name$='db-active']").click(function() {
            var dbItems = $("input[name^='db-']").parents(".form-group:not('.noHide')");
            if($(this).val()=="true"){
                dbItems.show();
                $("input[id$='db-session-true']").trigger("click");
                $("input[id$='db-cookie-true']").trigger("click");
                $("input[id$='db-log-true']").trigger("click");
            }
            else{
                dbItems.hide();
                $("input[id$='db-session-false']").trigger("click");
                $("input[id$='db-cookie-false']").trigger("click");
                $("input[id$='db-log-false']").trigger("click");
            }
        });
        $("input[name$='db-session-active']").click(function() {
            if($(this).val()=="false")
                $("input[id$='db-cookie-false']").trigger("click");
        });
        $("input[name$='db-cookie-active']").click(function() {
            var dbItems = $("input[name^='db-cookie']").parents(".form-group:not('.noHideCookie')");
            if($(this).val()=="true")
                dbItems.show();
            else
                dbItems.hide();
        });
    });
</script>
</body></html>