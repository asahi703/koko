<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Title</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <link rel="stylesheet" href="css/styleMatometeokuru.css">
</head>

<body class="bg-light" style="padding-top: 80px;">
  <!--モバイル時ヘッダー-->
  <header class="d-flex d-md-none w-100 navbar navbar-expand-md align-items-center py-4 fixed-top shadow-sm">
    <nav class="container d-flex justify-content-between">
      <!-- ブランドロゴとタイトル -->
      <a class="navbar-brand d-flex align-items-center position-absolute top-50 start-50 translate-middle" href="#">
        <img src="../../img/headerImg/logo.png" style="width: 50px" class="hd-img d-inline-block align-top img-fluid"
          alt="">
        <h1 class="title ms-2 fs-md-3 mb-0"></h1>
      </a>
      <!-- ハンバーガーを右端に配置（スマホ表示） -->
      <button class="navbar-toggler position-absolute top-50 end-0 translate-middle-y" type="button"
        data-bs-toggle="offcanvas" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false"
        aria-label="メニューを切り替え">
        <span class="navbar-toggler-icon"></span>
      </button>
    </nav>
    <!-- オフキャンバス（モバイル時） -->
    <div class="offcanvas offcanvas-end d-md-none" tabindex="-1" id="navbarMenu">
      <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title text-light">メニュー</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
      </div>
      <div class="offcanvas-body ml-1 p-0">
        <nav class="d-flex flex-column mt-1" id="sidebar-mobile">
          <ul class="navbar-nav">
            <li>
              <a class="nav-link" href="#">
                <h4>チームに参加</h4>
              </a>
            </li>
            <li>
              <a class="nav-link" href="#">
                <h4>チームを作成</h4>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </header>

  <!--PC時ヘッダー-->
  <header class="d-none d-md-flex w-100 navbar navbar-expand-md align-items-center py-md-2 fixed-top shadow-sm">
    <nav class="container d-flex flex-column flex-md-row justify-content-between align-items-center-md">
      <!-- ブランドロゴとタイトル -->
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="../../img/headerImg/logo.png" style="width: 50px" class="hd-img d-inline-block align-top img-fluid"
          alt="">
        <img src="../../img/headerImg/account.png" style="width: 50px"
          class="hd-img d-inline-block align-top img-fluid ms-4" alt="">
      </a>
      <!-- メニュー -->
      <div class="d-flex flex-row fs-5">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link hover-text" href="#">チームに参加</a>
          </li>
          <li class="nav-item">
            <a class="nav-link hover-text" href="#">チームを作成</a>
          </li>
        </ul>
      </div>
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

      <!-- メインコンテンツ -->
      <main
        class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto"
        style="margin-top: 80px; margin-bottom: 60px; max-width: 1200px; height: calc(100vh - 140px); overflow-y: auto;">
        <div class="multi-title fs-3">複数に送る</div>

        <div class="chat-multiple-arrowback">
          <a href="">
            <img src="../../img/arrow_back_24dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt="">
          </a>
        </div>

        <div class="multi-box rounded">
          <div class="multi-list">
            <div class="multi-item">
              <input type="checkbox" class="multi-checkbox" id="user1">
              <label class="multi-label fs-5" for="user1">鈴木一太</label>
            </div>
            <div class="multi-item">
              <input type="checkbox" class="multi-checkbox" id="user2">
              <label class="multi-label fs-5" for="user2">近道夜</label>
            </div>
            <div class="multi-item">
              <input type="checkbox" class="multi-checkbox" id="user3">
              <label class="multi-label fs-5" for="user3">五十嵐小樹</label>
            </div>
            <div class="multi-item">
              <input type="checkbox" class="multi-checkbox" id="user3">
              <label class="multi-label fs-5" for="user3">五十嵐小樹</label>
            </div>
            <div class="multi-item">
              <input type="checkbox" class="multi-checkbox" id="user3">
              <label class="multi-label fs-5" for="user3">五十嵐小樹</label>
            </div>
            <div class="multi-item">
              <input type="checkbox" class="multi-checkbox" id="user3">
              <label class="multi-label fs-5" for="user3">五十嵐小樹</label>
            </div>
          </div>
        </div>

        <!-- チャット入力エリア -->
        <!-- メッセージ入力フォーム -->
        <div class="row justify-content-center w-100 mt-4">
          <div class="col-12 col-md-6">
            <form class="chat-input-multiple">
              <div class="input-group bg-white rounded-pill border px-3 py-1" style="height: 45px;">
                <input type="text" name="" class="form-control rounded-pill" style="border: none; box-shadow: none;"
                  placeholder="メッセージを入力">
                <button type="submit"
                  class="btn btn-primary rounded-pill ms-2 d-flex align-items-center justify-content-center"
                  style="height: 100%; padding: 0 16px; border: none; background: none;">
                  <img src="../../img/send_24dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt="送信"
                    style="width: 24px; height: 24px;">
                </button>
              </div>
            </form>
          </div>
        </div>

      </main>
    </div>
  </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="../../index.js"></script>

</html>