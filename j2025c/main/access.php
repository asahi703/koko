<?php
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content-wrapper">
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
