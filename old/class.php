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
    <link rel="stylesheet" href="css/styleclass.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    <!-- Font Awesome のCDNを head内に追加 -->
</head>
<body>
<!--モバイル時ヘッダー-->
<header class="d-flex d-md-none w-100 navbar navbar-expand-md align-items-center py-4">
    <nav class="container d-flex justify-content-between">
        <!-- ブランドロゴとタイトル -->
        <a class="navbar-brand d-flex align-items-center position-absolute top-50 start-50 translate-middle" href="#">
            <img src="../../img/headerImg/logo.png" style="width: 50px"
                 class="hd-img d-inline-block align-top img-fluid" alt="">
            <h1 class="title ms-2 fs-md-3 mb-0"></h1>
        </a>
        <!-- ハンバーガーを右端に配置（スマホ表示） -->
        <button class="navbar-toggler position-absolute top-50 end-0 translate-middle-y" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#navbarMenu" aria-controls="navbarMenu"
                aria-expanded="false" aria-label="メニューを切り替え">
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
                        <a class="nav-link" href="#"><h4>マイページ</h4></a>
                    </li>
                    <li>
                        <a class="nav-link" href="#"><h4>お問い合わせ</h4></a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>

<!--PC時ヘッダー-->
<header class="d-none d-md-flex w-100 navbar navbar-expand-md align-items-center py-md-2 fixed-top">
    <nav class="container d-flex flex-column flex-md-row justify-content-between align-items-center-md">
        <!-- ブランドロゴとタイトル -->
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="../../img/headerImg/logo.png" style="width: 50px"
                 class="hd-img d-inline-block align-top img-fluid" alt="">
            <img src="../../img/headerImg/account.png" style="width: 50px" class="hd-img d-inline-block align-top img-fluid ms-4" alt="">
        </a>
        <!-- メニュー -->
        <div class="d-flex flex-row fs-5">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link hover-text" href="#">コミュニティに参加</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link hover-text" href="#">コミュニティを作成</a>
                </li>
            </ul>
        </div>
    </nav>
</header>

<div class="container-fluid">
    <div class="row">
      <!-- サイドバー -->
    <nav class="d-none d-md-flex col-md-2 flex-column align-items-center sidebar position-fixed">
    <ul class="nav flex-column sidebar-content">
    <li class="nav-item mb-4 text-center">
      <a class="nav-link p-0 d-flex flex-column align-items-center text-white hover-bg-dark" href="#">
        <img src="../../img/sidebarImg/notifications.png" class="icon-img img-fluid" width="50" alt="...">
        <span class="nav-label">通知</span>
      </a>
    </li>
    <li class="nav-item mb-4 text-center">
      <a class="nav-link p-0 d-flex flex-column align-items-center text-white hover-bg-dark" href="#">
        <img src="../../img/sidebarImg/chat.png" class="icon-img img-fluid" width="50" alt="...">
        <span class="nav-label">チャット</span>
      </a>
    </li>
    <li class="nav-item mb-4 text-center">
      <a class="nav-link p-0 d-flex flex-column align-items-center text-white hover-bg-dark" href="#">
        <img src="../../img/sidebarImg/community.png" class="icon-img img-fluid" width="50" alt="...">
        <span class="nav-label">コミュニティ</span>
      </a>
    </li>
    <li class="nav-item text-center">
      <a class="nav-link p-0 d-flex flex-column align-items-center text-white hover-bg-dark" href="#">
        <img src="../../img/sidebarImg/FAQ.png" class="icon-img img-fluid" width="50" alt="...">
        <span class="nav-label">よくある質問</span>
      </a>
    </li>
  </ul>
</nav>

    <!-- サイドバー横のメインエリア -->
    <main class="offset-md-2 col-md-10 pt-5 mt-5">
      <div class="d-flex">
        <!-- 左メニュー -->
        <div class="bg-light p-3 me-3" style="width: 250px; min-height: 400px;">
          <div><a href="#" class="text-decoration-none text-dark">&lt; すべてのクラスに戻る</a></div>
          <div class="mt-2">クラス名を表示 <span class="text-muted">︙</span></div>
          <div class="mt-4"><a href="#" class="text-primary text-decoration-none">行事予定表</a></div>
          <div class="mt-2"><a href="#" class="text-primary text-decoration-none">出席確認</a></div>
        </div>

        <!-- チャットエリア -->
        <div class="flex-grow-1 d-flex flex-column justify-content-between" style="min-height: 400px;">
          <div class="mb-3 p-2 bg-white rounded shadow-sm">
            <div class="d-flex align-items-center">
              <div class="rounded-circle bg-secondary me-2" style="width: 32px; height: 32px;"></div>
              <div><strong>葉加瀬太郎</strong> <span class="text-muted">05/19</span></div>
            </div>
            <hr>
            <div class="ps-4"></div>
          </div>

          <!-- コメント入力欄 -->
          <div class="input-group mt-auto rounded-pill border px-3">
            <input type="text" class="form-control border-0" placeholder="コメントを入力">
            <button class="btn text-primary" type="button"><i class="fas fa-plus"></i></button>
            <button class="btn text-primary" type="button"><i class="fas fa-paper-plane"></i></button>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
        crossorigin="anonymous"></script>
<script src="../../index.js"></script>
</html>