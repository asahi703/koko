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
    <link rel="stylesheet" href="css/styleaccess.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" />
    <!-- Font Awesome のCDNを head内に追加 -->
</head>

<body class="bg-light" style="padding-top: 80px;">
<?php include 'common/header.html'; ?>
<?php include 'common/sidebar.php'; ?>

    <!-- サイドバーとメインコンテンツ -->
    <div class="container-fluid">
        <div class="row">



            <main class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="access-title mb-0">権限付与画面</h2>
                </div>

                <div class="responsive-table-wrapper">
                    <div class="access-table-container bg-white rounded shadow-sm">
                        <div
                            class="access-table-header d-flex justify-content-between align-items-center py-2 px-3 border-bottom">
                            <div class="col-4">ユーザー名</div>
                            <div class="col-5">Eメールアドレス</div>
                            <div class="col-3 text-center">権限</div>
                        </div>
                        <div class="access-table-body">
                            <div
                                class="access-table-row d-flex justify-content-between align-items-center py-2 px-3 border-bottom">
                                <div class="col-4">ユーザーA</div>
                                <div class="col-5">userA@example.com</div>
                                <div class="col-3 d-flex justify-content-center align-items-center">
                                    <div class="form-check form-switch custom-toggle">
                                        <!--inputの最後の方にcheckedをつけることでチェック状態になる-->
                                        <input class="form-check-input" type="checkbox" role="switch" id="toggleSwitch1" >
                                        <label class="form-check-label" for="toggleSwitch1"></label>
                                    </div>
                                </div>
                            </div>
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