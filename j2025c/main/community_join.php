<?php
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content-wrapper">
    <main class="col-md-9 offset-md-2" style="padding-top: 150px;">
        <div class="text-center mb-4">
            <h2 class="fs-5 fw-bold">コミュニティに参加</h2>
        </div>
        <div class="card bg-secondary-subtle mx-auto w-100" style="max-width: 800px;">
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label for="communityName" class="form-label">コミュニティコード<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="communityName" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-5">参加</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
