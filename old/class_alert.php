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
  <link rel="stylesheet" href="css/styleClass_alert.css">
</head>

<!--ヘッダー-->
<?php include 'common/header.html'; ?>

<body class="bg-light" style="padding-top: 80px;">

<!-- サイドバー（PCのみ表示） -->
<?php include 'common/sidebar.php'; ?>

      <!-- メインコンテンツ -->
      <main class="class-main-content col-12 col-md-9 col-lg-10 px-3 px-md-5 py-4 py-md-5 mx-auto"
        style="min-height: 100vh;">

        <!--投稿-->
        <div class="rounded border border-2 mb-3 shadow-sm">
          <div class="border-bottom d-flex align-items-center justify-content-between" style="max-height: 50px;">
            <a class="nav-link text-dark py-1 text-nowrap custom-hover d-flex align-items-center px-3" href="#">
              <img src="../../img/headerImg/account.png" style="width: 40px"
                class="hd-img d-inline-block align-top img-fluid" alt="">
              <span class="ms-2 d-flex align-items-center fs-5"
                style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">ユーザー名</span>
            </a>
            <span class="opacity-75 me-3" style="font-size: 0.9em;">yyyy/mm/dd/hh/mm</span>
          </div>
          <div class="post-word p-3">
            本文です改行もできｇりうえｓぐれぎｒｓｈｇねｒｈｇんれしへｇりうれいすがえうがえ
          </div>
        </div>

        <!--投稿-->
        <div class="rounded border border-2 mb-3 shadow-sm">
          <div class="border-bottom d-flex align-items-center justify-content-between" style="max-height: 50px;">
            <a class="nav-link text-dark py-1 text-nowrap custom-hover d-flex align-items-center px-3" href="#">
              <img src="../../img/headerImg/account.png" style="width: 40px"
                class="hd-img d-inline-block align-top img-fluid" alt="">
              <span class="ms-2 d-flex align-items-center fs-5"
                style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">ユーザー名</span>
            </a>
            <span class="opacity-75 me-3" style="font-size: 0.9em;">yyyy/mm/dd/hh/mm</span>
          </div>
          <div class="post-word p-3">
            本文です改行もできｇりうえｓぐれぎｒｓｈｇねｒｈｇんれしへｇりうれいすがえうがえ
          </div>
        </div>

      </main>

    </div>
  </div>
  </div>
  </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="../../index.js"></script>

</html>