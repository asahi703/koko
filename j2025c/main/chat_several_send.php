<?php
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<head>
    <title>クラスメンバー追加</title>
    <link rel="stylesheet" href="../main/css/chat_several_send.css">
</head>
<div class="main-content-wrapper">
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
