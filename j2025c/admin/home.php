<?php
/*!
@file home.php
@brief 管理者ホーム画面
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルード
require_once("../main/common/libs.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;

//--------------------------------------------------------------------------------------
///	本体ノード
//--------------------------------------------------------------------------------------
class cmain_node extends cnode {
	public $admin;
	public $db;
	public $stats;
	
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
		$this->admin = null;
		$this->db = null;
		$this->stats = array();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
		// 管理者セッション確認
		if(!is_admin_logged_in()){
			cutil::redirect_exit('index.php');
		}
		$this->admin = get_admin_user();
		
		// DB接続
		require_once(__DIR__ . '/../main/common/dbmanager.php');
		$this->db = new cdb();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  本体実行（表示前処理）
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function execute(){
		global $err_array;
		global $err_flag;
		global $page_obj;
		
		if(is_null($page_obj)){
			echo 'ページが無効です';
			exit();
		}
		
		// 統計情報の取得
		$this->get_statistics();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	統計情報取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_statistics(){
		try {
			// ユーザー数
			$stmt = $this->db->prepare('SELECT COUNT(*) as count FROM users');
			$stmt->execute();
			$this->stats['users'] = $stmt->fetch()['count'];
			
			// 管理者数
			$stmt = $this->db->prepare('SELECT COUNT(*) as count FROM administers');
			$stmt->execute();
			$this->stats['admins'] = $stmt->fetch()['count'];
			
			// コミュニティ数
			$stmt = $this->db->prepare('SELECT COUNT(*) as count FROM communities');
			$stmt->execute();
			$this->stats['communities'] = $stmt->fetch()['count'];
			
		} catch (Exception $e) {
			$this->stats = array(
				'users' => 0,
				'admins' => 0,
				'communities' => 0
			);
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	統計カード表示の取得
	@return	統計カード表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_stats_cards(){
		$users_count = $this->stats['users'] ?? 0;
		$admins_count = $this->stats['admins'] ?? 0;
		$communities_count = $this->stats['communities'] ?? 0;
		
		return <<<END_BLOCK
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-header">
                <h5 class="card-title">ユーザー数</h5>
            </div>
            <div class="card-body">
                <h2>{$users_count}</h2>
                <p class="card-text">登録されているユーザーの総数</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-success">
            <div class="card-header">
                <h5 class="card-title">管理者数</h5>
            </div>
            <div class="card-body">
                <h2>{$admins_count}</h2>
                <p class="card-text">登録されている管理者の総数</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-info">
            <div class="card-header">
                <h5 class="card-title">コミュニティ数</h5>
            </div>
            <div class="card-body">
                <h2>{$communities_count}</h2>
                <p class="card-text">作成されているコミュニティの総数</p>
            </div>
        </div>
    </div>
</div>
END_BLOCK;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
//PHPブロック終了
?>
<!-- コンテンツ　-->
<head>
    <title>管理者ページ ホーム</title>
    <link rel="stylesheet" href="css/Global.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    <main class="main-content-wrapper">
        <div class="container bg-white rounded shadow p-4">
            <h1 class="mb-4">管理者ダッシュボード</h1>
            <p class="text-muted mb-4">システムの現在の状況を確認できます。</p>
            
            <?= $this->get_stats_cards(); ?>
            
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">管理メニュー</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <a href="user_management.php" class="btn btn-outline-primary w-100 py-3">
                                        <h6>ユーザー管理</h6>
                                        <small class="text-muted">ユーザーの追加・削除・編集</small>
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="admin_management.php" class="btn btn-outline-success w-100 py-3">
                                        <h6>管理者管理</h6>
                                        <small class="text-muted">管理者の追加・削除・編集</small>
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="community_management.php" class="btn btn-outline-info w-100 py-3">
                                        <h6>コミュニティ管理</h6>
                                        <small class="text-muted">コミュニティの追加・削除・編集</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php 
//PHPブロック再開
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	デストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//親クラスのデストラクタを呼ぶ
		parent::__destruct();
	}
}

//ページを作成
$page_obj = new cnode();
//本体追加
$page_obj->add_child($main_obj = cutil::create('cmain_node'));
//構築時処理
$page_obj->create();
//本体実行（表示前処理）
$main_obj->execute();
//ページ全体を表示
$page_obj->display();

?>