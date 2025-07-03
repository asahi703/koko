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
  <link rel="stylesheet" href="css/styleTopsidebar.css">
  <link rel="stylesheet" href="css/Global.css">
</head>

<!--ヘッダー-->
<?php include 'common/header.html'; ?>

<body class="bg-light" style="padding-top: 80px;">

<!-- サイドバー（PCのみ表示） -->
<?php include 'common/sidebar.php'; ?>

<div class="main-content-wrapper">
      <!-- メインコンテンツ -->
      <main
        class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto"
        style="margin-top: 80px; margin-bottom: 60px; max-width: 1200px;">
        <h1 class="mt-4">メインコンテンツ</h1>
        <p>ここにページの内容が入ります。</p>


      </main>
    </div>
  </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="../../index.js"></script>

</html>