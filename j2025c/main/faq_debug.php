<?php
require_once('common/session.php');
require_once('common/dbmanager.php');

// セキュリティのため、実際の運用環境では削除すること
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ デバッグツール</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>FAQデバッグツール</h1>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h2 class="h5 mb-0">ログインユーザー情報</h2>
            </div>
            <div class="card-body">
                <?php
                $user = get_login_user();
                if ($user): ?>
                    <table class="table">
                        <tr>
                            <th>UUID</th>
                            <td><?= htmlspecialchars($user['uuid'] ?? 'なし') ?></td>
                        </tr>
                        <tr>
                            <th>名前</th>
                            <td><?= htmlspecialchars($user['user_name'] ?? 'なし') ?></td>
                        </tr>
                        <tr>
                            <th>教師フラグ</th>
                            <td><?= ($user['user_is_teacher'] == 1) ? 'はい' : 'いいえ' ?></td>
                        </tr>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning">
                        ログインしていません
                        <a href="signin.php" class="btn btn-sm btn-primary">ログイン画面へ</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h2 class="h5 mb-0">FAQテーブル構造</h2>
            </div>
            <div class="card-body">
                <?php
                try {
                    $db = new cdb();
                    $stmt = $db->prepare("DESCRIBE faq");
                    $stmt->execute();
                    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if ($columns): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>フィールド名</th>
                                    <th>タイプ</th>
                                    <th>NULL許可</th>
                                    <th>キー</th>
                                    <th>デフォルト</th>
                                    <th>その他</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($columns as $column): ?>
                                <tr>
                                    <td><?= htmlspecialchars($column['Field']) ?></td>
                                    <td><?= htmlspecialchars($column['Type']) ?></td>
                                    <td><?= htmlspecialchars($column['Null']) ?></td>
                                    <td><?= htmlspecialchars($column['Key']) ?></td>
                                    <td><?= htmlspecialchars($column['Default'] ?? 'NULL') ?></td>
                                    <td><?= htmlspecialchars($column['Extra']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning">テーブル構造を取得できませんでした</div>
                    <?php endif;
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger">' . htmlspecialchars($e->getMessage()) . '</div>';
                }
                ?>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h2 class="h5 mb-0">FAQ一覧（最新5件）</h2>
            </div>
            <div class="card-body">
                <?php
                try {
                    $db = new cdb();
                    $stmt = $db->prepare("SELECT * FROM faq ORDER BY faq_created_at DESC LIMIT 5");
                    $stmt->execute();
                    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if ($faqs): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>タイトル</th>
                                    <th>ユーザーID</th>
                                    <th>回答有無</th>
                                    <th>作成日時</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($faqs as $faq): ?>
                                <tr>
                                    <td><?= htmlspecialchars($faq['faq_id']) ?></td>
                                    <td><?= htmlspecialchars($faq['faq_title'] ?? '(タイトルなし)') ?></td>
                                    <td><?= htmlspecialchars($faq['faq_user_id'] ?? 'NULL') ?></td>
                                    <td><?= !empty($faq['faq_answer']) ? '回答あり' : '回答なし' ?></td>
                                    <td><?= htmlspecialchars($faq['faq_created_at']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-danger test-delete" 
                                                data-id="<?= $faq['faq_id'] ?>"
                                                data-title="<?= htmlspecialchars($faq['faq_title'] ?? '質問') ?>">
                                            削除テスト
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning">FAQデータがありません</div>
                    <?php endif;
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger">' . htmlspecialchars($e->getMessage()) . '</div>';
                }
                ?>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h2 class="h5 mb-0">削除テスト（手動）</h2>
            </div>
            <div class="card-body">
                <form id="manualDeleteForm" class="row g-3">
                    <div class="col-md-6">
                        <label for="faq_id" class="form-label">FAQ ID</label>
                        <input type="number" class="form-control" id="faq_id" name="faq_id" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">削除実行</button>
                    </div>
                </form>
                
                <div class="mt-3">
                    <h3 class="h6">結果:</h3>
                    <pre id="resultContainer" class="p-3 bg-light"></pre>
                </div>
            </div>
        </div>
        
        <div class="mt-3 mb-5">
            <a href="faq.php" class="btn btn-secondary">FAQページに戻る</a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 削除テストボタン
        document.querySelectorAll('.test-delete').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');
                
                if (confirm(`「${title}」(ID: ${id})を削除しますか？`)) {
                    testDelete(id, this);
                }
            });
        });
        
        // 手動削除フォーム
        document.getElementById('manualDeleteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('faq_id').value;
            testDelete(id);
        });
        
        function testDelete(id, buttonElement) {
            const resultContainer = document.getElementById('resultContainer');
            resultContainer.textContent = '処理中...';
            
            if (buttonElement) {
                buttonElement.disabled = true;
                buttonElement.textContent = '処理中...';
            }
            
            const formData = new FormData();
            formData.append('faq_id', id);
            
            fetch('faq_delete.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                resultContainer.textContent = text;
                
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        alert('削除成功！');
                        if (buttonElement) {
                            buttonElement.closest('tr').style.display = 'none';
                        }
                    } else {
                        alert('削除失敗: ' + (data.message || '不明なエラー'));
                    }
                } catch (e) {
                    alert('応答の解析に失敗しました: ' + e.message);
                }
                
                if (buttonElement) {
                    buttonElement.disabled = false;
                    buttonElement.textContent = '削除テスト';
                }
            })
            .catch(error => {
                resultContainer.textContent = '通信エラー: ' + error.message;
                if (buttonElement) {
                    buttonElement.disabled = false;
                    buttonElement.textContent = '削除テスト';
                }
            });
        }
    });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
