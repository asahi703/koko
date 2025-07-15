<?php
/*!
@file notification.php
@brief 通知一覧
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
	public $notifications;
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
		$this->notifications = array();
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
		
		// 通知一覧を取得
		$this->get_notifications();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	通知一覧取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_notifications(){
		// 現在はサンプル通知を生成
		// 実際のデータベースから取得する場合はここで処理
		$this->notifications = array(
			array(
				'notification_id' => 1,
				'content' => 'コミュニティ「プログラミング学習」に新しい投稿があります。',
				'created_date' => '2024/01/15 14:30'
			),
			array(
				'notification_id' => 2,
				'content' => 'クラス「Java基礎」のメンバーに追加されました。',
				'created_date' => '2024/01/14 10:15'
			)
		);
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
	@brief	通知一覧の取得
	@return	通知一覧文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_notification_list(){
		if(count($this->notifications) == 0){
			return '<div class="alert alert-info">新しい通知はありません。</div>';
		}
		
		$list_str = '';
		foreach($this->notifications as $notification){
			$content = display($notification['content']);
			$created_date = display($notification['created_date']);
			
			$list_str .= <<<END_BLOCK
<div class="rounded border border-2 mb-3 shadow-sm">
    <div class="border-bottom d-flex align-items-center justify-content-between" style="max-height: 50px;">
        <div class="d-flex align-items-center px-3 py-2">
            <img src="img/headerImg/account.png" style="width: 40px"
                 class="hd-img d-inline-block align-top img-fluid" alt="">
            <span class="ms-2 d-flex align-items-center fs-5">通知</span>
        </div>
        <span class="opacity-75 me-3" style="font-size: 0.9em;">{$created_date}</span>
    </div>
    <div class="post-word p-3">
        {$content}
    </div>
</div>
END_BLOCK;
		}
		return $list_str;
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
    <title>通知</title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <main class="class-main-content col-12 col-md-9 col-lg-10 px-3 px-md-5 py-4 py-md-5 mx-auto"
          style="min-height: 100vh;">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">通知</h2>
        </div>

        <?= $this->get_notification_list(); ?>

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
