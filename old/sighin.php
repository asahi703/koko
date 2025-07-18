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
    <link rel="stylesheet" href="css/styleSighin.css">
</head>

<body class="bg-light">
    <!--モバイル時ヘッダー-->
    <header class="d-flex d-md-none w-100 navbar navbar-expand-md align-items-center py-4 fixed-top shadow-sm">
        <nav class="container d-flex justify-content-between">
            <!-- ブランドロゴとタイトル -->
            <a class="navbar-brand d-flex align-items-center position-absolute top-50 start-50 translate-middle"
                href="#">
                <img src="../../img/headerImg/logo.png" class="hd-img d-inline-block align-top img-fluid logo-img"
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
                <img src="../../img/headerImg/logo.png" class="hd-img d-inline-block align-top img-fluid logo-img"
                    alt="">
                <img src="../../img/headerImg/account.png"
                    class="hd-img d-inline-block align-top img-fluid ms-4 account-img" alt="">
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

    <!-- メインコンテンツ -->
    <div class="container d-flex flex-column align-items-center justify-content-center vw-100">
        <div class="d-flex flex-column justify-content-center align-items-center w-100 mt-md-3">
            <div class="mb-3">
                <h2>新規登録</h2>
            </div>
            <form action="sighin.html" method="post" class="border rounded shadow p-4 w-100 login-form">
                <input type="hidden" name="func" value="" />
                <input type="hidden" name="param" value="" />
                <div class="form-group my-2 px-2 w-100">
                    <label>ユーザーネーム <span class="text-danger fw-bold">*</span></label>
                    <input type="text" name="user_name" class="form-control" placeholder="ユーザーネーム" required>
                </div>
                <div class="form-group my-2 px-2 w-100">
                    <label>メールアドレス <span class="text-danger fw-bold">*</span></label>
                    <input type="email" name="user_mailaddress" class="form-control" placeholder="メールアドレス" required>
                </div>
                <div class="form-group my-2 px-2 w-100">
                    <label>パスワード <span class="text-danger fw-bold">*</span></label>
                    <input type="password" name="user_password" class="form-control" placeholder="パスワード" required>
                </div>
                <div class="text-center">
                    <p>利用規約は <a href="#">こちらから</a></p>
                </div>
                <div class="form-group d-flex justify-content-center my-2 px-2 w-100">
                    <input type="submit" class="btn btn-primary w-100" value="新規登録">
                </div>
                <div class="form-group d-flex justify-content-center my-2 px-2 w-100">
                    <a href="">ログインはこちら</a>
                </div>
            </form>
        </div>
    </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
    crossorigin="anonymous"></script>
<script src="../../index.js"></script>

</html>