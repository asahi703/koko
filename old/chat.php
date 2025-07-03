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
    <link rel="stylesheet" href="css/styeChat.css">
</head>

<body class="bg-light">
<?php include 'common/header.html'; ?>
<?php include 'common/sidebar.php'; ?>

<div class="main-content-wrapper">
    <div class="container-fluid h-100">
        <div class="row vh-100 gx-0">
            <!-- グループ選択サイドバー -->
            <nav class="col-12 col-md-3 col-lg-3 px-0 group-sidebar d-flex flex-column">
                <div class="p-3 border-bottom">
                    <h5 class="mb-3">グループ</h5>
                    <button class="btn btn-primary w-100 mb-2">＋ 新規グループ</button>
                </div>
                <ul class="list-group list-group-flush flex-grow-1 overflow-auto group-list-scroll">
                    <li class="list-group-item list-group-item-action active">開発チーム</li>
                    <li class="list-group-item list-group-item-action">営業部</li>
                    <li class="list-group-item list-group-item-action">総務連絡</li>
                    <li class="list-group-item list-group-item-action">雑談</li>
                    <!-- ...他のグループ... -->
                </ul>
            </nav>

            <!-- チャット画面 -->
            <main class="col-12 col-md-9 col-lg-9 px-0 d-flex flex-column chat-main-area position-relative">
                <!-- チャット履歴 -->
                <div class="flex-grow-1 overflow-auto chat-history p-4 chat-history-scroll">
                    <div class="d-flex justify-content-start mb-2">
                        <div class="chat-msg bg-white border rounded p-2">
                            <div>こんにちは！本日の進捗を共有します。</div>
                            <div class="opacity-50 small mt-1 text-end">10:32</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mb-2">
                        <div class="chat-msg chat-msg-sbm border rounded p-2">
                            <div>ありがとうございます！</div>
                            <div class="opacity-50 small mt-1 text-end">10:33</div>
                        </div>
                    </div>
                    <!-- ...他のメッセージ... -->
                </div>
                <!-- 入力欄（ページ下部に固定） -->
                <form class="chat-input-box border-top p-3 bg-white">
                    <div class="input-group">
                        <input type="text" class="form-control rounded-pill" placeholder="メッセージを入力">
                        <button type="submit" class="btn btn-primary rounded-pill ms-2">
                            送信
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="../../index.js"></script>
</body>
</html>