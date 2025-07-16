<?php
/*!
@file community_join.php
@brief コミュニティ参加
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
	public $invite_code_box;
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
		$this->invite_code_box = null;
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
		if(!isset($_POST['invite_code'])){
			$_POST['invite_code'] = "";
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
		
		if(!$this->user){
			cutil::redirect_exit('index.php');
		}
		
		// DB接続
		require_once(__DIR__ . '/common/dbmanager.php');
		$this->db = new cdb();
		
		//フォームボックス作成
		$this->invite_code_box = new ctextbox('invite_code', 'form-control', 255);
		$this->invite_code_box->set_required(true);
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
			// POSTデータの検証
			$this->invite_code_box->validate();
			
			// 送信ボタンが押された場合
			if(isset($_POST['submit'])){
				if(empty($err_array)){
					$this->join_community();
				}
			}
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ参加処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function join_community(){
		global $err_flag;
		
		try{
			$invite_code = trim($_POST['invite_code']);
			
			if(!$this->user){
				$this->error = 'ログインしてください。';
				return;
			}
			
			if($invite_code === ''){
				$this->error = '招待コードを入力してください。';
				return;
			}
			
			// コードが有効か確認
			$stmt = $this->db->prepare('SELECT community_id FROM community_invite_codes WHERE invite_code = ?');
			$stmt->execute([$invite_code]);
			$row = $stmt->fetch();
			
			if($row){
				$community_id = $row['community_id'];
				// 既に参加していないか確認
				$stmt2 = $this->db->prepare('SELECT * FROM community_users WHERE user_id = ? AND community_id = ?');
				$stmt2->execute([$this->user['uuid'], $community_id]);
				
				if(!$stmt2->fetch()){
					// 参加処理
					$stmt3 = $this->db->prepare('INSERT INTO community_users (user_id, community_id) VALUES (?, ?)');
					$stmt3->execute([$this->user['uuid'], $community_id]);
					$this->success = 'コミュニティに参加しました。';
					
					// フォームリセット
					$_POST['invite_code'] = '';
					$this->invite_code_box->set_value('');
				} else {
					$this->error = 'すでにこのコミュニティに参加しています。';
				}
			} else {
				$this->error = '招待コードが無効です。';
			}
			
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
    <title>コミュニティ参加</title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <main class="col-md-9 offset-md-2" style="padding-top: 150px;">
        <div class="text-center mb-4">
            <h2 class="fs-5 fw-bold">コミュニティに参加</h2>
        </div>
        <div class="card bg-secondary-subtle mx-auto w-100" style="max-width: 800px;">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="inviteCode" class="form-label">招待コード<span class="text-danger">*</span></label>
                        <?= $this->invite_code_box->get_tag(); ?>
                        <?= $this->invite_code_box->get_error_message_tag(); ?>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="submit" class="btn btn-primary px-5">参加</button>
                    </div>
                </form>
            </div>
        </div>
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
