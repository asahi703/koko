<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/styleAddclass.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <title>Title</title>
</head>

<body class="bg-light" style="padding-top: 80px;">
  <!--モバイル時ヘッダー-->
  <header class="d-flex d-md-none w-100 navbar navbar-expand-md align-items-center py-md-4 py-2 fixed-top shadow-sm">
    <nav class="container d-flex justify-content-between">
      <!-- ブランドロゴとタイトル -->
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="../../img/headerImg/logo.png" style="width: 50px" class="hd-img d-inline-block align-top img-fluid"
          alt="">
      </a>
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="../../img/headerImg/account.png" style="width: 50px"
          class="hd-img d-inline-block align-top img-fluid ms-4" alt="">
      </a>
  </header>


  <!--PC時ヘッダー-->
  <header class="d-none d-md-flex w-100 navbar navbar-expand-md align-items-center py-md-2 fixed-top shadow-sm">
    <nav class="container d-flex flex-column flex-md-row justify-content-between align-items-center-md">
      <!-- ブランドロゴとタイトル -->
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="../../img/headerImg/logo.png" style="width: 50px" class="hd-img d-inline-block align-top img-fluid"
          alt="">
      </a>
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="../../img/headerImg/account.png" style="width: 50px"
          class="hd-img d-inline-block align-top img-fluid ms-4" alt="">
      </a>
    </nav>
  </header>

  <!-- サイドバーとメインコンテンツ -->
  <div class="container-fluid">
    <div class="row">
      <!-- サイドバー（PCのみ表示） -->
      <nav class="top-sidebar d-none d-md-flex flex-column align-items-center p-0" style="width: 100px;">
        <ul class="nav flex-column sidebar-content w-100">
          <li class="nav-item mb-4 text-center">
            <a class="nav-link p-0 d-flex flex-column align-items-center text-white hover-bg-dark" href="#"
              style="user-select: none;">
              <img src="../../img/sidebarImg/notifications.png" class="icon-img img-fluid" width="50" alt="...">
              <span class="nav-label">通知</span>
            </a>
          </li>
          <li class="nav-item mb-4 text-center">
            <a class="nav-link p-0 d-flex flex-column align-items-center text-white hover-bg-dark" href="#"
              style="user-select: none;">
              <img src="../../img/sidebarImg/chat.png" class="icon-img img-fluid" width="50" alt="...">
              <span class="nav-label">チャット</span>
            </a>
          </li>
          <li class="nav-item mb-4 text-center">
            <a class="nav-link p-0 d-flex flex-column align-items-center text-white hover-bg-dark" href="#"
              style="user-select: none;">
              <img src="../../img/sidebarImg/community.png" class="icon-img img-fluid" width="50" alt="...">
              <span class="nav-label">コミュニティ</span>
            </a>
          </li>
          <li class="nav-item text-center">
            <a class="nav-link p-0 d-flex flex-column align-items-center text-white hover-bg-dark" href="#"
              style="user-select: none;">
              <img src="../../img/sidebarImg/FAQ.png" class="icon-img img-fluid" width="50" alt="...">
              <span class="nav-label">よくある質問</span>
            </a>
          </li>
        </ul>
      </nav>

      <!-- サイドバー（スマホ用ボトムナビ） -->
      <nav class="d-md-none fixed-bottom bg-white border-top shadow-sm">
        <ul class="nav justify-content-around py-1 mb-0">
          <li class="nav-item">
            <a class="nav-link text-center p-0" href="#">
              <img src="../../img/sidebarImg/notifications.png" class="icon-img img-fluid" width="30" alt="...">
              <div class="small">通知</div>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-center p-0" href="#">
              <img src="../../img/sidebarImg/chat.png" class="icon-img img-fluid" width="30" alt="...">
              <div class="small">チャット</div>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-center p-0" href="#">
              <img src="../../img/sidebarImg/community.png" class="icon-img img-fluid" width="30" alt="...">
              <div class="small">コミュニティ</div>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-center p-0" href="#">
              <img src="../../img/sidebarImg/FAQ.png" class="icon-img img-fluid" width="30" alt="...">
              <div class="small">FAQ</div>
            </a>
          </li>
        </ul>
      </nav>

      <!-- クラスサイドバー -->
      <nav id="class-sidebar" class="class-sdb d-none d-md-block flex-column px-0">
        <div class="d-flex flex-row justify-content-start align-items-center ms-2 py-md-2 border-bottom text-nowrap">
          <a class="nav-link text-dark d-flex align-items-center" href="#">
            <img src="../../img/sidebarImg/arrow_back_25dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt="" class="me-2"
              style="width: 24px; height: 24px;">
            <span class="fs-5">すべてのクラス</span>
          </a>
        </div>

        <div class="d-flex flex-row justify-content-between align-items-center ms-2 py-4 border-bottom">
          <a class="text-dark text-decoration-none" href="#">
            <span class="fs-5 fw-bold small">クラス名を表示</span>
          </a>
          <div class="dropdown">
            <a class="cu-pt" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="../../img/sidebarImg/more_vert_25dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt="">
            </a>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
              <li><a class="dropdown-item" href="#">メンバー一覧</a></li>
              <li><a class="dropdown-item" href="#">メンバー追加</a></li>
            </ul>
          </div>
        </div>

        <div class="d-flex flex-column justify-content-start ms-4 align-items-center">
          <ul class="list-unstyled w-100">
            <li>
              <a class="nav-link text-dark py-3 text-nowrap custom-hover d-block px-3" href="#">
                行事予定表
              </a>
            </li>
            <li>
              <a class="nav-link text-dark py-3 text-nowrap custom-hover d-block px-3" href="#">
                出席確認
              </a>
            </li>
          </ul>
        </div>
      </nav>

      <!-- スマホ用(ヘッダーの直下に置く) -->
      <!-- スマホ用ナビゲーションバー -->
      <div
        class="d-flex d-md-none bg-white border-top border-bottom py-2 px-3 justify-content-between align-items-center sticky-top"
        style="top: 80px; z-index: 1020;">

        <!-- 左：戻る -->
        <a href="#" class="text-dark text-decoration-none d-flex align-items-center">
          <img src="../../img/sidebarImg/arrow_back_25dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt="戻る"
            style="width: 24px; height: 24px;">
        </a>

        <!-- 中央：クラス名 -->
        <div class="flex-grow-1 text-center">
          <a class="text-dark text-decoration-none" href="#">
            <span class="fw-bold small">クラス名を表示</span>
          </a>
        </div>

        <!-- 右：メニュー（ドロップダウン） -->
        <div class="dropdown">
          <a href="#" class="text-dark" role="button" id="dropdownMenuButtonMobile" data-bs-toggle="dropdown"
            aria-expanded="false">
            <span class="material-icons"><img
                src="../../img/sidebarImg/more_vert_25dp_999999_FILL0_wght400_GRAD0_opsz24.svg"></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButtonMobile">
            <li><a class="dropdown-item" href="#">行事予定表</a></li>
            <li><a class="dropdown-item" href="#">出席確認</a></li>
            <li><a class="dropdown-item" href="#">メンバー確認</a></li>
            <li><a class="dropdown-item" href="#">メンバー追加</a></li>
          </ul>
        </div>
      </div>


      <!-- メインコンテンツ -->
      <main class="class-main-content col-12 col-md-9 col-lg-10 p-5"
        style="min-height: 100vh; margin-left: 320px; width: calc(100% - 320px);">
        <div class="container" style="background-color: #f5f5f5;">
          <h2 class="mb-5">メンバー追加</h2>
          <button class="btn btn-primary update-button" style="top: 150px;">更新</button>
          <ul class="list-unstyled">

            <li class="border-top">
              <div class="user-checkbox-row d-flex align-items-center gap-3 px-3 py-2">
                <img src="../../img/headerImg/account.png" style="width: 30px"
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
                <img src="../../img/headerImg/account.png" style="width: 30px"
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
  </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="../../index.js"></script>

</html>