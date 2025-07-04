<?php
/*!
@file user_profile_template.php
@brief 他ユーザー用プロフィールテンプレート
*/

class user_profile_template extends cnode {
    public function __construct() { parent::__construct(); }
    public function create() {}

    /**
     * サイドバー表示
     */
    public function render_sidebar($username, $user_id, $profile_image, $bio, $stats) {
        ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="<?= htmlspecialchars($profile_image, ENT_QUOTES, 'UTF-8') ?>" alt="プロフィール画像" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                    <h4 class="card-title"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></h4>
                    <p class="text-muted">@<?= htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php if (!empty($bio)): ?>
                        <p class="card-text"><?= nl2br(htmlspecialchars($bio, ENT_QUOTES, 'UTF-8')) ?></p>
                    <?php endif; ?>
                    <div class="d-flex justify-content-center gap-3 mt-3">
                        <?php foreach ($stats as $stat): ?>
                            <div>
                                <span class="fw-bold"><?= $stat['count'] ?></span><br>
                                <span class="text-muted"><?= htmlspecialchars($stat['label']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function __destruct() { parent::__destruct(); }
}
