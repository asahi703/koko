<?php
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<head>
    <title>クラス</title>
    <link rel="stylesheet" href="../main/css/class.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<div class="main-content-wrapper">
    <?php include 'includes/class_sidebar.php'?>
    <!-- メインコンテンツ -->
    <main class="class-main-content col-12 col-md-9 col-lg-10 p-5"
          style="min-height: 100vh; margin-left: 320px; width: calc(100% - 320px);">

        <!--投稿-->
        <div class="rounded border border-2 mb-3 shadow-sm">
            <div class="border-bottom d-flex align-items-center justify-content-between" style="max-height: 50px;">
                <a class="nav-link text-dark py-1 text-nowrap custom-hover d-flex align-items-center px-3" href="#">
                    <img src="../main/img/headerImg/account.png" style="width: 40px"
                         class="hd-img d-inline-block align-top img-fluid" alt="">
                    <span class="ms-2 d-flex align-items-center fs-5"
                          style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">ユーザー名</span>
                </a>
                <span class="opacity-75 me-3" style="font-size: 0.9em;">yyyy/mm/dd/hh/mm</span>
            </div>
            <div class="post-word p-3">
                本文です改行もできる
            </div>
        </div>

        <div class="rounded border border-2 mb-3 shadow-sm">
            <div class="border-bottom d-flex align-items-center justify-content-between" style="max-height: 50px;">
                <a class="nav-link text-dark py-1 text-nowrap custom-hover d-flex align-items-center px-3" href="#">
                    <img src="../main/img/headerImg/account.png" style="width: 40px"
                         class="hd-img d-inline-block align-top img-fluid" alt="">
                    <span class="ms-2 d-flex align-items-center fs-5"
                          style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">ユーザー名</span>
                </a>
                <span class="opacity-75 me-3" style="font-size: 0.9em;">yyyy/mm/dd/hh/mm</span>
            </div>
            <div class="post-word p-3">
                本文です改行もできる
            </div>
        </div>


        <!-- メッセージ入力フォーム -->
        <div class="row justify-content-center w-100 mt-4">
            <div class="col-12 col-md-6">
                <form class="chat-input">
                    <div class="input-group bg-white rounded-pill border px-3 py-1 align-items-end"
                         style="min-height: 45px;">
                        <!-- ユーザーアイコン -->
                        <div class="dropdown d-flex justify-content-center align-items-center me-2" style="height: 38px;">
                            <a class="cu-pt d-flex align-items-center h-100" id="userProfileDropdown"
                               data-bs-toggle="dropdown" aria-expanded="false" style="height: 38px;">
                                <img src="../main/img/headerImg/account.png" alt="プロフィール"
                                     style="display: block; height: 32px; width: 32px; border-radius: 50%; margin: auto 0; position: relative;">
                            </a>
                            <!-- ミニマイページドロップダウン -->
                            <ul class="dropdown-menu dropdown-menu-start p-3" aria-labelledby="userProfileDropdown" style="min-width: 250px;">
                                <li class="d-flex align-items-center mb-3">
                                    <img src="../main/img/headerImg/account.png" style="width: 50px; height: 50px; border-radius: 50%;" alt="プロフィール画像">
                                    <div class="ms-3">
                                        <h6 class="mb-1">ユーザー名</h6>
                                        <small class="text-muted">user@example.com</small>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item d-flex align-items-center" href="mypage.php">
                                    <i class="bi bi-person me-2"></i>マイページ
                                </a></li>
                                <li><a class="dropdown-item d-flex align-items-center" href="#">
                                    <i class="bi bi-gear me-2"></i>設定
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item d-flex align-items-center text-danger" href="#">
                                    <i class="bi bi-box-arrow-right me-2"></i>ログアウト
                                </a></li>
                            </ul>
                        </div>

                        <textarea class="form-control rounded"
                                  style="border: none; box-shadow: none; resize: none; min-height: 38px; max-height: 150px; overflow-y: auto; background: transparent;"
                                  placeholder="メッセージを入力" rows="1"
                                  oninput="this.style.height='38px';this.style.height=(this.scrollHeight)+'px';"></textarea>

                        <div class="dropdown d-flex justify-content-center align-items-center me-2"
                             style="height: 38px;">
                            <a class="cu-pt d-flex align-items-center h-100" id="templateDropdownAdd"
                               data-bs-toggle="dropdown" aria-expanded="false" style="height: 38px;">
                                <img src="../main/img/add_24dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt=""
                                     style="display: block; height: 24px; width: 24px; margin: auto 0; position: relative;">
                            </a>
                        </div>

                        <!-- ファイル添付+ -->
                        <div class="dropdown d-flex justify-content-center align-items-center"
                             style="height: 38px;">
                            <a class="cu-pt d-flex align-items-center h-100" id="templateDropdownAdd"
                               data-bs-toggle="dropdown" aria-expanded="false" style="height: 38px;">
                                <img src="../main/img/apps_24dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt=""
                                     style="display: block; height: 24px; width: 24px; margin: auto 0; position: relative;">
                            </a>

                            <!-- メインドロップダウン -->
                            <ul class="dropdown-menu" aria-labelledby="templateDropdownAdd">
                                <li><a class="dropdown-item" href="#">テンプレート１</a></li>
                                <li><a class="dropdown-item" href="#">テンプレート2</a></li>
                            </ul>
                        </div>

                        <button type="submit"
                                class="btn btn-primary rounded-pill ms-2 d-flex align-items-center justify-content-center"
                                style="height: 38px; padding: 0 16px; border: none; background: none;">
                            <img src="../main/img/send_24dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt="送信"
                                 style="width: 24px; height: 24px;">
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
