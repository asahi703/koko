<?php
class user_page_template extends cnode {
    protected function render_sidebar($username, $user_id, $profile_image, $bio, $stats) {
        // 現在のファイル名を取得
        $current = basename($_SERVER['SCRIPT_NAME']);

        ?>
        <div class="col-md-4 mb-4">
            <div class="card text-center">
                <img src="<?= htmlspecialchars($profile_image, ENT_QUOTES, 'UTF-8') ?>" class="rounded-circle mx-auto mt-4" style="width:80px;height:80px;" alt="プロフィール画像">
                <div class="card-body">
                    <h5 class="card-title mb-1"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></h5>
                    <p class="text-muted mb-2">@<?= htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="mb-2"><?= htmlspecialchars($bio, ENT_QUOTES, 'UTF-8') ?></p>
                    <div class="d-flex justify-content-around mt-3">
                        <?php foreach ($stats as $stat): ?>
                            <div>
                                <div class="fw-bold"><?= $stat['count'] ?></div>
                                <small><?= $stat['label'] ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="list-group mt-4">
                <?php if ($current === 'favorites.php'): ?>
                    <a href="mypage.php" class="list-group-item list-group-item-action text-center fw-bold">
                        マイページ
                    </a>
                <?php else: ?>
                    <a href="favorites.php" class="list-group-item list-group-item-action text-center fw-bold">
                        いいねした投稿
                    </a>
                <?php endif; ?>
                <a href="order_history.php" class="list-group-item list-group-item-action text-center fw-bold">
                    購入履歴ページ
                </a>
            </div>
            <form method="post" class="mt-3">
                <button type="submit" name="logout" class="btn btn-danger w-100 fw-bold">ログアウト</button>
            </form>
        </div>
        <?php
    }

    protected function render_post_list($posts, $empty_message, $show_author = false) {
        if (empty($posts)): ?>
            <div class="alert alert-info"><?= $empty_message ?></div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <a href="#" class="list-group-item list-group-item-action mb-3 border rounded-3 p-0 overflow-hidden">
                    <?php if (!empty($post['image_url'])): ?>
                        <img src="<?= htmlspecialchars($post['image_url'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="<?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>"
                             class="card-img-top"
                             style="height: 15rem; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body p-3">
                        <?php if ($show_author && isset($post['author'])): ?>
                            <div class="d-flex align-items-center mb-2">
                                <img src="<?= htmlspecialchars($post['author_image'], ENT_QUOTES, 'UTF-8') ?>" class="rounded-circle me-2" style="width:32px;height:32px;" alt="著者">
                                <span class="fw-bold"><?= htmlspecialchars($post['author'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        <?php endif; ?>
                        <h5 class="card-title"><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?></h5>
                        <p class="card-text text-truncate"><?= htmlspecialchars($post['description'], ENT_QUOTES, 'UTF-8') ?></p>
                        <small class="text-muted"><?= htmlspecialchars($post['date'], ENT_QUOTES, 'UTF-8') ?></small>
                        <div class="mt-2">
                            <?php foreach ($post['tags'] as $tag): ?>
                                <span class="badge text-bg-success text-light"><?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif;
    }
}
?>

