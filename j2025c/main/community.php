<?php
require_once('common/session.php');
require_once('common/dbmanager.php');

$user = get_login_user();
$error = '';
$success = '';

// POST処理・リダイレクトは出力より前に
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['community_name'])) {
        // コミュニティ作成処理
        if (!$user) {
            $error = 'ログインしてください。';
        } elseif (empty($_POST['community_name'])) {
            $error = 'コミュニティ名を入力してください。';
        } elseif (empty($user['user_is_teacher'])) {
            // 先生でなければ作成不可
            $error = 'コミュニティ作成権限がありません。';
        } else {
            try {
                $db = new cdb();
                $stmt = $db->prepare('INSERT INTO communities (community_name, community_description, community_owner) VALUES (?, ?, ?)');
                
                // ユーザーIDを適切に取得
                $user_id = $user['user_id'] ?? $user['uuid'] ?? null;
                
                $stmt->execute([
                    $_POST['community_name'],
                    $_POST['community_description'] ?? '',
                    $user_id
                ]);
                // リダイレクトは出力前に
                header("Location: community.php?created=1");
                exit;
            } catch (PDOException $e) {
                $error = 'コミュニティ作成に失敗しました。';
            }
        }
    } elseif (isset($_POST['invite_code'])) {
        // コミュニティ参加処理
        $invite_code = trim($_POST['invite_code']);
        if (!$user) {
            $error = 'ログインしてください。';
        } elseif ($invite_code === '') {
            $error = '招待コードを入力してください。';
        } else {
            $db = new cdb();
            // コードが有効か確認
            $stmt = $db->prepare('SELECT community_id FROM community_invite_codes WHERE invite_code = ?');
            $stmt->execute([$invite_code]);
            $row = $stmt->fetch();
            if ($row) {
                $community_id = $row['community_id'];
                
                // ユーザーIDを適切に取得
                $user_id = $user['user_id'] ?? $user['uuid'] ?? null;
                
                // 既に参加していないか確認
                $stmt2 = $db->prepare('SELECT * FROM community_users WHERE user_id = ? AND community_id = ?');
                $stmt2->execute([$user_id, $community_id]);
                if (!$stmt2->fetch()) {
                    // 参加処理
                    $stmt3 = $db->prepare('INSERT INTO community_users (user_id, community_id) VALUES (?, ?)');
                    $stmt3->execute([$user_id, $community_id]);
                    header("Location: community.php?joined=1"); // 参加後のリダイレクト
                    exit;
                } else {
                    $error = 'すでにこのコミュニティに参加しています。';
                }
            } else {
                $error = '招待コードが無効です。';
            }
        }
    }
}

if (isset($_GET['created'])) {
    $success = 'コミュニティを作成しました。';
}
if (isset($_GET['joined'])) {
    $success = 'コミュニティに参加しました。';
}

// 参加している or オーナーのコミュニティ一覧取得
$communities = [];
if ($user) {
    try {
        $db = new cdb();
        
        // ユーザーIDを適切に取得
        $user_id = $user['user_id'] ?? $user['uuid'] ?? null;
        
        $stmt = $db->prepare(
            'SELECT DISTINCT c.*
             FROM communities c
             LEFT JOIN community_users cu ON c.community_id = cu.community_id
             WHERE cu.user_id = ? OR c.community_owner = ?
             ORDER BY c.community_id DESC'
        );
        $stmt->execute([$user_id, $user_id]);
        $communities = $stmt->fetchAll();
        
        // 各コミュニティのメンバー数を取得
        foreach ($communities as &$community) {
            $stmt = $db->prepare(
                'SELECT COUNT(*) as member_count
                 FROM community_users cu
                 WHERE cu.community_id = ?'
            );
            $stmt->execute([$community['community_id']]);
            $result = $stmt->fetch();
            $community['member_count'] = $result['member_count'] + 1; // オーナーも含める
        }
    } catch (PDOException $e) {
        $error = 'コミュニティ一覧の取得に失敗しました。';
    }
}

// 出力はここから
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<head>
    <title>コミュニティ選択</title>
    <link rel="stylesheet" href="../main/css/community.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<div class="main-content-wrapper">
    <main class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto main-content-styles">

        <div class="d-flex justify-content-center gap-3 position-fixed class-create-button" style="top: 150px; right: 20px;">
            <?php if ($user && !empty($user['user_is_teacher'])): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCommunityModal">
                コミュニティ作成
            </button>
            <?php endif; ?>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#joinCommunityModal">
                コミュニティに参加
            </button>
        </div>

        <?php if ($user && !empty($user['user_is_teacher'])): ?>
        <div class="modal fade" id="createCommunityModal" tabindex="-1" aria-labelledby="createCommunityModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createCommunityModalLabel">コミュニティ作成</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="community.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="communityName" class="form-label">コミュニティ名<span class="text-danger">*</span></label>
                                <input type="text" class="form-control shadow" id="communityName" name="community_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="communityDesc" class="form-label">説明</label>
                                <textarea class="form-control shadow" id="communityDesc" name="community_description"></textarea>
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
        <?php endif; ?>

        <div class="modal fade" id="joinCommunityModal" tabindex="-1" aria-labelledby="joinCommunityModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="joinCommunityModalLabel">コミュニティに参加</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="community.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="inviteCode" class="form-label">招待コード<span class="text-danger">*</span></label>
                                <input type="text" class="form-control shadow" id="inviteCode" name="invite_code" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                            <button type="submit" class="btn btn-success px-5">参加</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- メンバー表示モーダル -->
        <div class="modal fade" id="membersModal" tabindex="-1" aria-labelledby="membersModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="membersModalLabel">コミュニティメンバー</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="membersContent">
                            <!-- メンバー情報がここに動的に読み込まれます -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success mt-3"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="w-100 d-flex justify-content-start align-items-start mb-3 community-name-display">
            <p class="mb-0 fs-3">参加中またはオーナーのコミュニティ一覧</p>
        </div>

        <div class="container mt-4 class-card-container-styles">
            <div class="row">
                <?php if ($user && count($communities) > 0): ?>
                    <?php foreach ($communities as $community): ?>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                            <div class="class-card rounded p-3 shadow-sm w-100 class-card-style position-relative">
                                <a href="class_select.php?id=<?php echo $community['community_id']; ?>" class="nav-link stretched-link">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded me-3 class-card-image-placeholder"></div>
                                        <div class="flex-grow-1 text-start">
                                            <p class="mb-1 fw-bold"><?php echo htmlspecialchars($community['community_name']); ?></p>
                                            <small class="text-muted d-block"><?php echo htmlspecialchars($community['community_description']); ?></small>
                                            <div class="mt-2">
                                                <?php 
                                                $user_id = $user['user_id'] ?? $user['uuid'] ?? null;
                                                if ($community['community_owner'] == $user_id): 
                                                ?>
                                                    <span class="badge bg-primary">オーナー</span>
                                                <?php endif; ?>
                                                <small class="text-muted ms-1">
                                                    <i class="bi bi-people-fill"></i> <?php echo $community['member_count']; ?>人
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <button type="button" 
                                        class="btn btn-outline-primary btn-sm position-absolute bottom-0 end-0 m-2" 
                                        style="z-index: 10;"
                                        onclick="loadMembers(<?php echo $community['community_id']; ?>, '<?php echo htmlspecialchars($community['community_name']); ?>')"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#membersModal">
                                    <i class="bi bi-people"></i> メンバー
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php elseif ($user): ?>
                    <div class="col-12">
                        <div class="alert alert-info">参加中またはオーナーのコミュニティがありません。</div>
                    </div>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-warning">ログインしてください。</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<?php if (!empty($openModal)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = new bootstrap.Modal(document.getElementById('<?php echo $openModal; ?>'));
    modal.show();
});
</script>
<?php endif; ?>
<script>
// ページロード時にモーダルの背景やbodyクラスが残っていたら消す
document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.remove('modal-open');
    var backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(function(bd){ bd.parentNode.removeChild(bd); });
});

// メンバー情報を読み込む関数
function loadMembers(communityId, communityName) {
    // モーダルタイトルを更新
    document.getElementById('membersModalLabel').textContent = communityName + ' のメンバー';
    
    // ローディング表示
    document.getElementById('membersContent').innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">メンバー情報を読み込み中...</p>
        </div>
    `;
    
    // Ajax でメンバー情報を取得
    fetch('get_community_members.php?community_id=' + communityId)
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('API Response:', data); // デバッグ用
            
            if (data.success) {
                if (data.members && data.members.length > 0) {
                    let html = '<div class="row">';
                    data.members.forEach((member, index) => {
                        // アイコンパスの処理
                        let iconSrc;
                        if (member.user_icon && member.user_icon.trim() !== '') {
                            if (member.user_icon.startsWith('img/user_icons/')) {
                                iconSrc = '../' + member.user_icon;
                            } else {
                                iconSrc = '../img/user_icons/' + member.user_icon;
                            }
                        } else {
                            iconSrc = '../main/img/headerImg/account.png';
                        }
                        
                        html += `
                            <div class="col-12 col-md-6 mb-3">
                                <div class="d-flex align-items-center p-3 border rounded member-card">
                                    <!-- プロフィール画像（ドロップダウン付き） -->
                                    <div class="dropdown position-relative">
                                        <img src="${iconSrc}" 
                                             style="width: 50px; height: 50px; border-radius: 50%; cursor: pointer; object-fit: cover;" 
                                             alt="プロフィール画像"
                                             id="memberProfile${index}"
                                             data-bs-toggle="dropdown" 
                                             aria-expanded="false"
                                             class="member-profile-img">
                                        
                                        <!-- ミニプロフィールドロップダウン -->
                                        <ul class="dropdown-menu dropdown-menu-start p-3" 
                                            aria-labelledby="memberProfile${index}" 
                                            style="min-width: 280px;">
                                            <li class="d-flex align-items-center mb-3">
                                                <img src="${iconSrc}" 
                                                     style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;" 
                                                     alt="プロフィール画像">
                                                <div class="ms-3">
                                                    <h6 class="mb-1 fw-bold">${member.user_name || 'ユーザー名なし'}</h6>
                                                    <small class="text-muted">${member.user_email || 'メールアドレスなし'}</small>
                                                    <div class="mt-1">
                                                        <span class="badge ${member.user_is_teacher == '1' ? 'bg-success' : 'bg-primary'}">
                                                            ${member.user_is_teacher == '1' ? '先生' : '生徒'}
                                                        </span>
                                                        ${member.is_owner ? '<span class="badge bg-warning text-dark ms-1">オーナー</span>' : ''}
                                                    </div>
                                                </div>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li class="mb-2">
                                                <small class="text-muted">最近の活動</small>
                                                <div class="mt-1">
                                                    <small>最終ログイン: 2時間前</small><br>
                                                    <small>投稿数: 12件</small>
                                                </div>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item d-flex align-items-center" href="profile.php?user=${member.user_id}">
                                                <i class="bi bi-person me-2"></i>プロフィールを見る
                                            </a></li>
                                            <li><a class="dropdown-item d-flex align-items-center" href="chat.php?user=${member.user_id}&name=${encodeURIComponent(member.user_name || 'ユーザー')}">
                                                <i class="bi bi-chat-dots me-2"></i>メッセージを送る
                                            </a></li>
                                        </ul>
                                    </div>
                                    
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="mb-1">${member.user_name || 'ユーザー名なし'}</h6>
                                        <small class="text-muted d-block">${member.user_email || 'メールアドレスなし'}</small>
                                        <div class="mt-2 d-flex align-items-center justify-content-between">
                                            <div>
                                                <span class="badge ${member.user_is_teacher == '1' ? 'bg-success' : 'bg-primary'}">
                                                    ${member.user_is_teacher == '1' ? '先生' : '生徒'}
                                                </span>
                                                ${member.is_owner ? '<span class="badge bg-warning text-dark ms-1">オーナー</span>' : ''}
                                            </div>
                                            <div>
                                                <a href="chat.php?user=${member.user_id}&name=${encodeURIComponent(member.user_name || 'ユーザー')}" 
                                                   class="btn btn-outline-primary btn-sm me-1" 
                                                   title="チャット">
                                                    <i class="bi bi-chat-dots"></i>
                                                </a>
                                                <a href="profile.php?user=${member.user_id}" 
                                                   class="btn btn-outline-secondary btn-sm" 
                                                   title="プロフィール">
                                                    <i class="bi bi-person"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    document.getElementById('membersContent').innerHTML = html;
                } else {
                    document.getElementById('membersContent').innerHTML = `
                        <div class="alert alert-info">
                            このコミュニティにはメンバーがいません。
                        </div>
                    `;
                }
            } else {
                document.getElementById('membersContent').innerHTML = `
                    <div class="alert alert-danger">
                        エラー: ${data.error || 'メンバー情報の取得に失敗しました。'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('membersContent').innerHTML = `
                <div class="alert alert-danger">
                    ネットワークエラーが発生しました: ${error.message}
                </div>
            `;
        });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>