<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Title</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <link rel="stylesheet" href="css/styleclassselect.css">
</head>

<body class="bg-light">
  <header class="d-flex d-md-none w-100 navbar navbar-expand-md align-items-center py-4 fixed-top shadow-sm">
    <nav class="container d-flex justify-content-between">
      <a class="navbar-brand d-flex align-items-center position-absolute top-50 start-50 translate-middle" href="#">
        <img src="../../img/headerImg/logo.png" class="hd-img d-inline-block align-top img-fluid" alt="">
        <h1 class="title ms-2 fs-md-3 mb-0"></h1>
      </a>
      <button class="navbar-toggler position-absolute top-50 end-0 translate-middle-y" type="button"
        data-bs-toggle="offcanvas" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false"
        aria-label="メニューを切り替え">
        <span class="navbar-toggler-icon"></span>
      </button>
    </nav>
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

  <header class="d-none d-md-flex w-100 navbar navbar-expand-md align-items-center py-md-2 fixed-top shadow-sm">
    <nav class="container d-flex flex-column flex-md-row justify-content-between align-items-center-md">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="../../img/headerImg/logo.png" class="hd-img d-inline-block align-top img-fluid" alt="">
        <img src="../../img/headerImg/account.png" class="hd-img d-inline-block align-top img-fluid ms-4" alt="">
      </a>
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

  <div class="container-fluid">
    <div class="row">
      <nav class="col-2 col-md-2 col-lg-2 d-none d-md-flex flex-column align-items-center sidebar p-0">
        <ul class="nav flex-column sidebar-content w-100">
          <li class="nav-item mb-4 text-center">
            <a class="nav-link p-0 d-flex flex-column align-items-center text-white hover-bg-dark user-select-none-style"
              href="#">
              <img src="../../img/sidebarImg/notifications.png" class="icon-img img-fluid" width="50" alt="...">
              <span class="nav-label">通知</span>
            </a>
          </li>
          <li class="nav-item mb-4 text-center">
            <a class="nav-link p-0 d-flex flex-column align-items-center text-white hover-bg-dark user-select-none-style"
              href="#">
              <img src="../../img/sidebarImg/chat.png" class="icon-img img-fluid" width="50" alt="...">
              <span class="nav-label">チャット</span>
            </a>
          </li>
          <li class="nav-item mb-4 text-center">
            <a class="nav-link p-0 d-flex flex-column align-items-center text-white hover-bg-dark user-select-none-style"
              href="#">
              <img src="../../img/sidebarImg/community.png" class="icon-img img-fluid" width="50" alt="...">
              <span class="nav-label">コミュニティ</span>
            </a>
          </li>
          <li class="nav-item text-center">
            <a class="nav-link p-0 d-flex flex-column align-items-center text-white hover-bg-dark user-select-none-style"
              href="#">
              <img src="../../img/sidebarImg/FAQ.png" class="icon-img img-fluid" width="50" alt="...">
              <span class="nav-label">よくある質問</span>
            </a>
          </li>
        </ul>
      </nav>

      <div class="container-fluid">
        <div class="row">
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

          <main class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto main-content-styles">

            <button type="button" class="btn btn-primary position-fixed class-create-button" data-bs-toggle="modal"
              data-bs-target="#exampleModal">
              クラス作成
            </button>

            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
              aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">クラス作成</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label for="communityName" class="form-label">クラス名<span class="text-danger">*</span></label>
                        <input type="text" class="form-control shadow" id="communityName" required>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                      <button type="submit" class="btn btn-primary px-5">作成</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="w-100 d-flex justify-content-start align-items-start mb-3 community-name-display">
              <p class="mb-0 fs-3">選択されているコミュニティ名を表示</p>
            </div>

            <div class="row justify-content-center w-100">
              <div class="col-12 col-md-6">
                <form class="mt-4">
                  <div class="input-group bg-white rounded-pill border px-3 py-1 search-bar-styles">
                    <div class="d-flex justify-content-center align-items-center">🔍</div>
                    <input type="text" name="" class="form-control rounded-pill search-input-style" placeholder="検索">
                  </div>
                </form>
              </div>
            </div>

            <div class="container mt-4 class-card-container-styles">
              <div class="row">
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                  <a href="" class="nav-link">
                    <div class="class-card rounded d-flex align-items-center p-3 shadow-sm w-100 class-card-style">
                      <div class="rounded me-3 class-card-image-placeholder"></div>
                      <div class="flex-grow-1">
                        <p class="mb-0 fw-bold">一組</p>
                      </div>
                    </div>
                  </a>
                </div>

                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                  <a href="" class="nav-link">
                    <div class="class-card rounded d-flex align-items-center p-3 shadow-sm w-100 class-card-style">
                      <div class="rounded me-3 class-card-image-placeholder"></div>
                      <div class="flex-grow-1">
                        <p class="mb-0 fw-bold">二組</p>
                      </div>
                    </div>
                  </a>
                </div>

                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                  <a href="" class="nav-link">
                    <div class="class-card rounded d-flex align-items-center p-3 shadow-sm w-100 class-card-style">
                      <div class="rounded me-3 class-card-image-placeholder"></div>
                      <div class="flex-grow-1">
                        <p class="mb-0 fw-bold">三組</p>
                      </div>
                    </div>
                  </a>
                </div>

                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                  <a href="" class="nav-link">
                    <div class="class-card rounded d-flex align-items-center p-3 shadow-sm w-100 class-card-style">
                      <div class="rounded me-3 class-card-image-placeholder"></div>
                      <div class="flex-grow-1">
                        <p class="mb-0 fw-bold">四組</p>
                      </div>
                    </div>
                  </a>
                </div>

                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                  <a href="" class="nav-link">
                    <div class="class-card rounded d-flex align-items-center p-3 shadow-sm w-100 class-card-style">
                      <div class="rounded me-3 class-card-image-placeholder"></div>
                      <div class="flex-grow-1">
                        <p class="mb-0 fw-bold">五組</p>
                      </div>
                    </div>
                  </a>
                </div>

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