<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Home | Boostack - a full stack web layer for PHP | getboostack.com</title>
    <meta name="description" content="Improve your development and build your ideas">
    <meta name="author" content="stefano spagnolo">
    <meta content="boostack, php, framework, website, productive, simplicity, seo, secure, mysql, open-source" name="Keywords" />
    <meta content="INDEX, FOLLOW" name="ROBOTS" />
    <link rel="shortcut icon" href="../assets/img/favicon.ico" />
    <link rel="image_src" href="img/boostack_logo_x210.png" />
    <link href="../assets/css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="custom.css" rel="stylesheet">
</head>

<body class="setup">
    <section class="disclaimer sectionTitle">
        <div class="line">
            <div class="container">
                <div class="row">
                    <a href="index.php" class="ps-0 text-decoration-none">
                        <h1 class="">Boostack Setup</h1>
                    </a>
                    <p class="lead ps-0">This script checks if your server and PHP configuration meets the requirements for running Boostack.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="checker">
        <div class="container mt-5">
            <div class="row w-75 mx-auto pt-5">
                <div class="col-md-5 mx-auto">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h4 class="card-title">Boostack on Docker</h4>
                            <p class="card-text">Automatically configure Boostack if you have installed it in the Docker environment.</p>
                            <a href="index_docker.php" class="btn btn-dark w-100 text-white">Start</a>
                        </div>

                    </div>
                </div>
                <div class="col-md-5 mx-auto ">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h4 class="card-title">Custom Webserver</h4>
                            <p class="card-text">Manually configure the Boostack environment to install it on your own webserver, such as Apache.</p>
                            <a href="index_custom.php" class="btn btn-dark w-100 text-white">Start</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script type="text/javascript" src="../assets/js/lib/jquery.min.js"></script>
    <script type="text/javascript" src="../assets/js/lib/bootstrap.min.js"></script>

    <script type="text/javascript" src="setup.js"></script>
</body>

</html>