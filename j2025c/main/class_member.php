<?php
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<head>
    <title>クラスメンバー</title>
    <link rel="stylesheet" href="../main/css/class_member.css">
</head>
<div class="main-content-wrapper">
    <?php include 'includes/class_sidebar.php'?>

    <!-- メインコンテンツ -->
    <main class="class-main-content col-12 col-md-9 col-lg-10 p-5" style="min-height: 100vh; margin-left: 320px; width: calc(100% - 320px);">
        <div class="container">
            <h2 class="mb-5">メンバー確認</h2>
            <ul class="list-unstyled">
                <li class="border-top">
                    <div>
                        <a class="nav-link text-dark py-1 text-nowrap custom-hover d-flex align-items-center px-3"
                           href="#">
                            <img src="../main/img/headerImg/account.png" style="width: 30px"
                                 class="hd-img d-inline-block align-top img-fluid" alt="">
                            <span class="ms-2 d-flex align-items-center"
                                  style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">ユーザー名</span>
                        </a>
                    </div>
                </li>
                <li class="border-top">
                    <div>
                        <a class="nav-link text-dark py-1 text-nowrap custom-hover d-flex align-items-center px-3"
                           href="#">
                            <img src="../main/img/headerImg/account.png" style="width: 30px"
                                 class="hd-img d-inline-block align-top img-fluid" alt="">
                            <span class="ms-2 d-flex align-items-center"
                                  style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">ユーザー名</span>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </main>
</div>
