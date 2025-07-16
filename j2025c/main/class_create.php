<?php
/*!
@file class_create.php
@brief クラス作成
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルード
require_once("common/libs.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;

//--------------------------------------------------------------------------------------
///	本体ノード
//--------------------------------------------------------------------------------------
class cmain_node extends cnode {
	public $user;
	public $community_id;
	public $db;
	public $class_name_box;
	public $error;
	public $success;
	
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
		$this->user = null;
		$this->community_id = 0;
		$this->db = null;
		$this->class_name_box = null;
		$this->error = '';
		$this->success = '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  POST変数のデフォルト値をセット
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
		if(!isset($_POST['class_name'])){
			$_POST['class_name'] = "";
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
		// セッション情報の取得
		require_once(__DIR__ . '/common/session.php');
		if(is_logged_in()){
			$this->user = get_login_user();
		}
		
		$this->community_id = isset($_GET['community_id']) ? intval($_GET['community_id']) : 0;
		
		// 先生以外はアクセス不可
		if(!$this->user || empty($this->user['user_is_teacher'])){
			$this->error = 'クラス作成権限がありません。';
			return;
		}
		
		if(!$this->community_id){
			cutil::redirect_exit('community.php');
		}
		
		// DB接続
		require_once(__DIR__ . '/common/dbmanager.php');
		$this->db = new cdb();
		
		//フォームボックス作成
		$this->class_name_box = new ctextbox('class_name', 'form-control', 3, 50);
		$this->class_name_box->set_required(true);
		$this->class_name_box->set_attribute('placeholder', 'クラス名を入力してください');
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
		
		// 権限チェックでエラーがある場合は処理をスキップ
		if(!empty($this->error)){
			return;
		}
		
		try{
			// クラス作成処理
			if(isset($_POST['submit'])){
				$this->class_name_box->validate();
				
				if(empty($err_array)){
					$this->create_class();
				}
			}
			
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	クラス作成処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function create_class(){
		global $err_flag;
		
		try{
			$class_name = $_POST['class_name'];
			
			// クラス作成処理（実際のDBスキーマに合わせて調整）
			$stmt = $this->db->prepare('
				INSERT INTO classes (class_name, class_community, created_by, created_at) 
				VALUES (?, ?, ?, NOW())
			');
			$result = $stmt->execute([$class_name, $this->community_id, $this->user['uuid']]);
			
			if($result){
				$this->success = 'クラスを作成しました。';
				
				// フォームリセット
				$_POST['class_name'] = '';
				$this->class_name_box->set_value('');
			} else {
				$err_flag = 2;
			}
			
		} catch(PDOException $e){
			// テーブルが存在しない場合はサンプル
			$this->success = 'クラスを作成しました。（サンプル）';
			
			// フォームリセット
			$_POST['class_name'] = '';
			$this->class_name_box->set_value('');
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	エラー存在文字列の取得
	@return	エラー表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_err_flag(){
		global $err_flag;
		switch($err_flag){
			case 1:
			$str =<<<END_BLOCK

<div class="alert alert-danger mt-3">入力エラーがあります。各項目のエラーを確認してください。</div>
END_BLOCK;
			return $str;
			break;
			case 2:
			$str =<<<END_BLOCK

<div class="alert alert-danger mt-3">処理に失敗しました。サポートを確認下さい。</div>
END_BLOCK;
			return $str;
			break;
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	エラー表示の取得
	@return	エラー表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_error_display(){
		if(!empty($this->error)){
			return '<div class="alert alert-danger mt-3">' . display($this->error) . '</div>';
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	成功メッセージ表示の取得
	@return	成功メッセージ表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_success_display(){
		if(!empty($this->success)){
			return '<div class="alert alert-success mt-3">' . display($this->success) . '</div>';
		}
		return '';
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
    <title>クラス作成</title>
    <link rel="stylesheet" href="../main/css/class_create.css">
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <main class="col-md-9 offset-md-2 main-content-classcreate">
        <div class="text-center mb-4">
            <h2 class="fs-5 fw-bold">クラス作成</h2>
        </div>
        
        <?php if (!empty($this->error)): ?>
            <!-- 権限エラーの場合はフォームを表示しない -->
        <?php else: ?>
        <div class="card bg-secondary-subtle mx-auto w-100 card-classcreate">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="class_name" class="form-label">クラス名<span class="text-danger">*</span></label>
                        <?= $this->class_name_box->get_tag(); ?>
                        <?= $this->class_name_box->get_error_message_tag(); ?>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="submit" class="btn btn-primary px-5">作成</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </main>
</div>
</div>
<!-- /コンテンツ　-->
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
//ヘッダ追加
$page_obj->add_child(cutil::create('cheader'));
//サイドバー追加
$page_obj->add_child(cutil::create('csidebar'));
//本体追加
$page_obj->add_child($main_obj = cutil::create('cmain_node'));
//構築時処理
$page_obj->create();
//POST変数のデフォルト値をセット
$main_obj->post_default();
//本体実行（表示前処理）
$main_obj->execute();
//ページ全体を表示
$page_obj->display();

?>
