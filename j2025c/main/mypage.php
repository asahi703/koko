<?php
require_once('common/session.php');
require_once('common/dbmanager.php');

$user = get_login_user();
if (!$user) {
    header('Location: index.php'); // ログインしていない場合はログインページへ
    exit;
}

$error = '';
$success = '';

// POST処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ユーザー情報更新処理
    $name = $_POST['name'] ?? '';
    $mail = $_POST['mail'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // 画像アップロード処理
    $icon_filename = $user['user_icon'] ?? '';
    if (isset($_FILES['user_icon']) && $_FILES['user_icon']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['user_icon']['tmp_name'];
        $orig_name = $_FILES['user_icon']['name'];
        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
        
        // ファイルサイズチェック（2MB制限）
        if ($_FILES['user_icon']['size'] > 2 * 1024 * 1024) {
            $error = '画像ファイルのサイズは2MB以下にしてください。';
        } else if (empty($ext)) {
            $error = '画像ファイルの拡張子が取得できません。jpg, jpeg, png, gif 形式でアップロードしてください。';
        } else if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            // 保存先ディレクトリのパス設定
            $icon_dir_relative = 'img/user_icons/';
            $icon_dir_absolute = __DIR__ . '/../' . $icon_dir_relative;
            
            // ディレクトリ存在確認と作成
            if (!is_dir($icon_dir_absolute)) {
                if (!mkdir($icon_dir_absolute, 0755, true)) {
                    $error = 'アップロード用ディレクトリを作成できませんでした。管理者にお問い合わせください。';
                }
            }
            
            if (empty($error)) {
                // user_idを使ってファイル名を生成
                $filename = ($user['user_id'] ?? '') . '.' . $ext;
                $icon_filename = $icon_dir_relative . $filename;
                $save_path = $icon_dir_absolute . $filename;
                
                // 既存画像の削除処理
                foreach (['jpg', 'jpeg', 'png', 'gif'] as $old_ext) {
                    $old_file = $icon_dir_absolute . ($user['user_id'] ?? '') . '.' . $old_ext;
                    if (file_exists($old_file)) {
                        @unlink($old_file);
                    }
                }
                
                // ファイルアップロード
                if (move_uploaded_file($tmp_name, $save_path)) {
                    // 成功したらパーミッション設定
                    chmod($save_path, 0644);
                    
                    // アップロード確認
                    if (!file_exists($save_path)) {
                        $error = "ファイルの保存に失敗しました。";
                    }
                } else {
                    $error = '画像の保存に失敗しました。管理者にお問い合わせください。';
                }
            }
        } else {
            $error = '画像は jpg, jpeg, png, gif のみ対応です。';
        }
    } else if (isset($_FILES['user_icon']) && $_FILES['user_icon']['error'] !== UPLOAD_ERR_NO_FILE) {
        // アップロードエラーの詳細
        switch ($_FILES['user_icon']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error = 'ファイルサイズが大きすぎます。';
                break;
            case UPLOAD_ERR_PARTIAL:
                $error = 'ファイルのアップロードが中断されました。';
                break;
            default:
                $error = 'ファイルのアップロードに失敗しました。';
        }
    }

    if (empty($name) || empty($mail)) {
        $error = '名前とメールアドレスは必須です。';
    } else if (empty($error)) {
        try {
            $db = new cdb();
            $sql = 'UPDATE users SET user_name = ?, user_mailaddress = ?, user_login = ?';
            $params = [$name, $mail, $mail];

            if (!empty($password)) {
                if ($password !== $password_confirm) {
                    $error = 'パスワードが一致しません。';
                } else {
                    $sql .= ', user_password = ?';
                    $params[] = sha1($password);
                }
            }
            // アイコン更新（ファイルがアップロードされた場合のみ）
            if (!empty($icon_filename) && $icon_filename !== ($user['user_icon'] ?? '')) {
                $sql .= ', user_icon = ?';
                $params[] = $icon_filename;
            }

            if (empty($error)) {
                $sql .= ' WHERE user_id = ?';
                $params[] = $user['user_id'] ?? 0;

                $stmt = $db->prepare($sql);
                $stmt->execute($params);

                // セッション情報も更新
                $_SESSION['user']['user_name'] = $name;
                $_SESSION['user']['user_mailaddress'] = $mail;
                if (!empty($icon_filename) && $icon_filename !== ($user['user_icon'] ?? '')) {
                    $_SESSION['user']['user_icon'] = $icon_filename;
                }

                $success = 'アカウント情報を更新しました。';
                $user = get_login_user();
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                 $error = 'このメールアドレスは既に使用されています。';
            } else {
                 $error = '更新に失敗しました。';
            }
        }
    }
}

// HTML出力
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<head>
    <title>マイページ</title>
</head>
<div class="main-content-wrapper" style="padding-top: 100px;">
    <main class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto main-content-styles">
        <div class="card w-100" style="max-width: 600px;">
            <div class="card-body p-4">
                <h3 class="card-title mb-3">マイページ</h3>
                <p class="card-text">アカウント情報を編集します。</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success mt-3"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="post" action="mypage.php" class="mt-4" enctype="multipart/form-data">
                    <div class="mb-3 text-center">
                        <label class="form-label">プロフィール画像</label><br>
                        <?php
                        $user_icon = $user['user_icon'] ?? '';
                        $icon_path = !empty($user_icon) && file_exists(__DIR__ . '/../' . $user_icon)
                            ? $user_icon
                            : 'main/img/headerImg/account.png';
                        ?>
                        <div class="mb-3">
                            <img src="<?php echo htmlspecialchars($icon_path); ?>" alt="プロフィール画像" 
                                 style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:2px solid #667eea; margin-bottom:10px;" 
                                 id="profilePreview">
                        </div>
                        <input type="file" name="user_icon" accept="image/*" class="form-control mt-2" 
                               style="max-width:300px; margin:auto;" id="iconInput">
                        <small class="form-text text-muted">jpg, jpeg, png, gif形式（2MB以下）</small>
                    </div>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const input = document.getElementById('iconInput');
                        const preview = document.getElementById('profilePreview');
                        
                        input.addEventListener('change', function(e) {
                            const file = e.target.files[0];
                            if (file) {
                                // ファイルサイズチェック（2MB制限）
                                if (file.size > 2 * 1024 * 1024) {
                                    alert('ファイルサイズは2MB以下にしてください。');
                                    this.value = '';
                                    return;
                                }
                                
                                // ファイル形式チェック
                                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                                if (!allowedTypes.includes(file.type)) {
                                    alert('jpg, jpeg, png, gif形式のファイルをアップロードしてください。');
                                    this.value = '';
                                    return;
                                }
                                
                                const reader = new FileReader();
                                reader.onload = function(ev) {
                                    preview.src = ev.target.result;
                                };
                                reader.readAsDataURL(file);
                            }
                        });
                    });
                    </script>
                    <div class="mb-3 text-start">
                        <label for="name" class="form-label">名前<span class="text-danger">*</span></label>
                        <input type="text" class="form-control shadow-sm" id="name" name="name" value="<?php echo htmlspecialchars($user['user_name'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="mail" class="form-label">メールアドレス<span class="text-danger">*</span></label>
                        <input type="email" class="form-control shadow-sm" id="mail" name="mail" value="<?php echo htmlspecialchars($user['user_mailaddress'] ?? ''); ?>" required>
                        <label for="password" class="form-label">新しいパスワード (変更する場合のみ)</label>
                        <input type="password" class="form-control shadow-sm" id="password" name="password">
                    </div>
                    <div class="mb-3 text-start">
                        <label for="password_confirm" class="form-label">新しいパスワード (確認)</label>
                        <input type="password" class="form-control shadow-sm" id="password_confirm" name="password_confirm">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary px-5 mt-3">更新</button>
                    </div>
                </form>
                
                <?php if (!empty($user['user_is_teacher'] ?? 0)): ?>
                <hr>
                <div class="mt-3">
                    <a href="create_teacher.php" class="btn btn-info">教師アカウント作成ページへ</a>
                    <a href="teacher_questions.php" class="btn btn-primary ms-2">質問管理</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<?php
// include 'includes/footer.php';
?>
