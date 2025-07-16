<?php
/*!
@file community_create.php
@brief コミュニティ作成
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
	public $db;
	public $name_box;
	public $description_box;
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
		$this->db = null;
		$this->name_box = null;
		$this->description_box = null;
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
		if(!isset($_POST['community_name'])){
			$_POST['community_name'] = "";
		}
		if(!isset($_POST['community_description'])){
			$_POST['community_description'] = "";
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
		
		// 先生以外はアクセス不可
		if(!$this->user || empty($this->user['user_is_teacher'])){
			$this->error = 'コミュニティ作成権限がありません。';
		}
		
		// DB接続
		require_once(__DIR__ . '/common/dbmanager.php');
		$this->db = new cdb();
		
		//フォームボックス作成
		$this->name_box = new ctextbox('community_name', 'form-control', 255);
		$this->name_box->set_required(true);
		
		$this->description_box = new ctextarea('community_description', 'form-control', 4);
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
		
		try{
			// 権限チェック
			if(!empty($this->error)){
				return;
			}
			
			// POSTデータの検証
			$this->name_box->validate();
			
			// 送信ボタンが押された場合
			if(isset($_POST['submit'])){
				if(empty($err_array)){
					$this->create_community();
				}
			}
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ作成処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function create_community(){
		global $err_flag;
		
		try{
			if(!$this->user){
				$this->error = 'ログインしてください。';
				return;
			}
			
			if(empty($this->user['user_is_teacher'])){
				$this->error = 'コミュニティ作成権限がありません。';
				return;
			}
			
			$name = $_POST['community_name'];
			$description = $_POST['community_description'];
			
			$stmt = $this->db->prepare('INSERT INTO communities (community_name, community_description, community_owner) VALUES (?, ?, ?)');
			$stmt->execute([$name, $description, $this->user['uuid']]);
			
			// 作成後は一覧ページにリダイレクト
			cutil::redirect_exit('community.php?created=1');
			
		} catch(PDOException $e){
			$this->error = 'コミュニティ作成に失敗しました。';
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
    <title>コミュニティ作成</title>
    <link rel="stylesheet" href="../main/css/community_create.css">
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <main class="col-md-9 offset-md-2 main-content-community">
        <div class="text-center mb-4">
            <h2 class="fs-5 fw-bold">コミュニティ作成</h2>
        </div>
        <?php if (empty($this->error)): ?>
        <div class="card bg-secondary-subtle mx-auto w-100 card-community-create">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="communityName" class="form-label">コミュニティ名<span class="text-danger">*</span></label>
                        <?= $this->name_box->get_tag(); ?>
                        <?= $this->name_box->get_error_message_tag(); ?>
                    </div>
                    <div class="mb-3">
                        <label for="communityDescription" class="form-label">説明</label>
                        <?= $this->description_box->get_tag(); ?>
                        <?= $this->description_box->get_error_message_tag(); ?>
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
