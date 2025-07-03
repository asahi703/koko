<?php
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<head>
    <title>クラスメンバー追加</title>
    <link rel="stylesheet" href="../main/css/class_add.css">
</head>
<div class="main-content-wrapper">
    <?php include 'includes/class_sidebar.php'?>

    <!-- メインコンテンツ -->
    <main class="class-main-content col-12 col-md-9 col-lg-10 p-5"
          style="min-height: 100vh; margin-left: 320px; width: calc(100% - 320px);">
        <div class="container" style="background-color: #f5f5f5;">
            <h2 class="mb-5">メンバー追加</h2>
            <button class="btn btn-primary update-button" style="top: 150px;">更新</button>
            <ul class="list-unstyled">

                <li class="border-top">
                    <div class="user-checkbox-row d-flex align-items-center gap-3 px-3 py-2">
                        <img src="../main/img/headerImg/account.png" style="width: 30px"
                             class="hd-img d-inline-block align-top img-fluid" alt="ユーザーアイコン">
                        <span class="user-name"
                              style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                  ユーザー名
                </span>
                        <div class="form-check ms-auto">
                            <input type="checkbox" class="form-check-input custom-checkbox-lg" value="" id="checkDefault" checked>
                        </div>
                    </div>
                </li>

                <li class="border-top">
                    <div class="user-checkbox-row d-flex align-items-center gap-3 px-3 py-2">
                        <img src="../main/img/headerImg/account.png" style="width: 30px"
                             class="hd-img d-inline-block align-top img-fluid" alt="ユーザーアイコン">
                        <span class="user-name"
                              style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                  ユーザー名
                </span>
                        <div class="form-check ms-auto">
                            <input type="checkbox" class="form-check-input custom-checkbox-lg" value="" id="checkDefault">
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </main>
</div>

