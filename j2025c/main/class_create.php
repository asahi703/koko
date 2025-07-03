<?php
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<head>
    <title>クラス作成</title>
    <link rel="stylesheet" href="../main/css/class_create.css">
</head>
<div class="main-content-wrapper">
    <main class="col-md-9 offset-md-2 main-content-classcreate">
        <div class="text-center mb-4">
            <h2 class="fs-5 fw-bold">クラス作成</h2>
        </div>
        <div class="card bg-secondary-subtle mx-auto w-100 card-classcreate">
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label for="communityName" class="form-label">クラス名<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="communityName" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-5">作成</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
