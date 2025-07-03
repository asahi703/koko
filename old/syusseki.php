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
  <link rel="stylesheet" href="css/stylesyusseki.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    integrity="sha512-..." crossorigin="anonymous" />
  <!-- Font Awesome のCDNを head内に追加 -->
</head>

<body class="bg-light" style="padding-top: 80px;">
  <!--モバイル時ヘッダー-->



  <!-- メインラッパー -->
  <div class="container-fluid">
    <div class="row">
      <!-- トップサイドバー（PCのみ表示） -->
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

      <!-- メイン -->
      <main class="class-main-content col-12 col-md-9 col-lg-10 p-5"
        style="min-height: 100vh; margin-left: 320px; width: calc(100% - 320px);">
        <button class="btn btn-primary update-button" style="position: fixed; top: 120px;">更新</button>
        <h2 class="title">出席確認</h2>

        <div class="date-slider-container">
          <span id="selected-date">12</span>
          <!--minとmaxは月ごとに変わるということを留意して-->
          <input type="range" min="1" max="31" value="12" id="date-slider" class="form-range w-50" />
          <span>日</span>
        </div>

        <!-- 月選択ドロップダウンｐｃ用（サイドバーのすぐ横に表示／レスポンシブ対応） -->
        <div class="d-none d-md-block position-absolute" style="top: 200px; left: 350px; width: 120px;">
          <select class="form-select form-select-sm" style="width: 100%; height: 100%;">
            <option value="1">1月</option>
            <option value="2">2月</option>
            <option value="3">3月</option>
            <option value="4">4月</option>
            <option value="5">5月</option>
            <option value="6">6月</option>
            <option value="7">7月</option>
            <option value="8">8月</option>
            <option value="9">9月</option>
            <option value="10">10月</option>
            <option value="11">11月</option>
            <option value="12">12月</option>
          </select>
        </div>

        <!-- モバイル用 月選択（ヘッダー下、または必要な場所に） -->
        <div class="d-block d-md-none px-3 py-2">
          <select class="form-select form-select-sm">
            <option value="1">1月</option>
            <option value="2">2月</option>
            <option value="3">3月</option>
            <option value="4">4月</option>
            <option value="5">5月</option>
            <option value="6">6月</option>
            <option value="7">7月</option>
            <option value="8">8月</option>
            <option value="9">9月</option>
            <option value="10">10月</option>
            <option value="11">11月</option>
            <option value="12">12月</option>
          </select>
        </div>

        <!--メンバーリスト-->
        <div class="member-list">
          <div class="member">
            <img src="../../img/headerImg/account.png" style="width: 30px"
              class="hd-img d-inline-block align-top img-fluid" alt="">
            <span class="member-name">生徒名</span>
            <div class="status-lamp status-green"></div>
          </div>
          <div class="member">
            <img src="../../img/headerImg/account.png" style="width: 30px"
              class="hd-img d-inline-block align-top img-fluid" alt="">
            <span class="member-name">生徒名</span>
            <div class="status-lamp status-red"></div>
          </div>

        </div>
      </main>

      <!-- JavaScript -->
      <script>
        document.querySelectorAll('.status-lamp').forEach(lamp => {
          lamp.addEventListener('click', () => {
            if (lamp.classList.contains('status-green')) {
              lamp.classList.replace('status-green', 'status-yellow');
            } else if (lamp.classList.contains('status-yellow')) {
              lamp.classList.replace('status-yellow', 'status-red');
            } else {
              lamp.classList.replace('status-red', 'status-green');
            }
          });
        });

        const slider = document.getElementById('date-slider');
        const display = document.getElementById('selected-date');
        slider.addEventListener('input', () => {
          display.textContent = slider.value;
        });
      </script>

</body>

</html>