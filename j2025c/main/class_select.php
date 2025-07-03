<?php
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<head>
    <title>クラス選択</title>
    <link rel="stylesheet" href="../main/css/class_select.css">
</head>
<div class="main-content-wrapper">
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
