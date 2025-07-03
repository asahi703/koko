<?php
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content-wrapper">
    <main class="col-md-9 offset-md-2" style="padding-top: 40px;">
        <div class="text-center mb-4">
            <h2 class="fs-5 fw-bold">質問は何ですか？</h2>
        </div>
        <div class="card bg-secondary-subtle mx-auto w-100" style="max-width: 800px;">
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label for="communityName" class="form-label">タイトル<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="communityName" required>
                    </div>
                    <div class="mb-3">
                        <label for="communityDescription" class="form-label">質問内容</label>
                        <textarea class="form-control" id="communityDescription" rows="4"></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-5">送信</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
