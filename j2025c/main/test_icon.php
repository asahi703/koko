<?php
require_once('common/dbmanager.php');
require_once('common/session.php');

$user = get_login_user();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>アイコンテスト</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .icon-test { margin: 20px 0; padding: 10px; border: 1px solid #ccc; }
        img { margin: 10px; }
    </style>
</head>
<body>
    <h1>アイコン表示テスト</h1>
    
    <div class="icon-test">
        <h3>セッション情報:</h3>
        <pre><?php print_r($user); ?></pre>
    </div>
    
    <div class="icon-test">
        <h3>アイコン表示テスト:</h3>
        <?php if ($user): ?>
            <p>ログイン中のユーザー: <?php echo htmlspecialchars($user['user_name'] ?? 'なし'); ?></p>
            <p>アイコンパス: <?php echo htmlspecialchars($user['user_icon'] ?? 'なし'); ?></p>
            
            <?php if (!empty($user['user_icon'])): ?>
                <p>アイコンあり:</p>
                <img src="<?php echo htmlspecialchars($user['user_icon']); ?>" 
                     style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #667eea;"
                     alt="ユーザーアイコン"
                     onerror="this.src='main/img/headerImg/account.png'; this.onerror=null;">
            <?php else: ?>
                <p>アイコンなし:</p>
                <img src="main/img/headerImg/account.png"
                     style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #667eea;"
                     alt="デフォルトアイコン">
            <?php endif; ?>
        <?php else: ?>
            <p style="color: red;">ログインしていません</p>
            <a href="index.php">ログインページへ</a>
        <?php endif; ?>
    </div>
    
    <div class="icon-test">
        <h3>データベース内のユーザー一覧:</h3>
        <?php
        try {
            $db = new cdb();
            $stmt = $db->prepare("SELECT user_id, user_name, user_icon FROM users WHERE user_icon IS NOT NULL");
            $stmt->execute();
            $users = $stmt->fetchAll();
            
            foreach ($users as $user_db):
        ?>
            <div style="margin: 10px 0; padding: 10px; background: #f0f0f0;">
                <p>ID: <?php echo $user_db['user_id']; ?>, 名前: <?php echo htmlspecialchars($user_db['user_name']); ?></p>
                <p>アイコンパス: <?php echo htmlspecialchars($user_db['user_icon']); ?></p>
                <img src="<?php echo htmlspecialchars($user_db['user_icon']); ?>" 
                     style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #667eea;"
                     alt="ユーザーアイコン"
                     onerror="this.src='main/img/headerImg/account.png'; this.onerror=null;">
            </div>
        <?php 
            endforeach;
        } catch (PDOException $e) {
            echo "データベースエラー: " . $e->getMessage();
        }
        ?>
    </div>
</body>
</html>
