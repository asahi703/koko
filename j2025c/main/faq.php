<?php
/*!
@file faq.php
@brief よくある質問一覧
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
	public $faqs;
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
		$this->faqs = array();
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
		
		// FAQ一覧を取得
		$this->get_faqs();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	FAQ一覧取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_faqs(){
		// サンプルFAQデータ
		$this->faqs = array(
			array(
				'faq_id' => 1,
				'question' => '行事の予定がわかりません',
				'answer' => '行事の予定は「マイページ > 行事カレンダー」からご確認いただけます。'
			),
			array(
				'faq_id' => 2,
				'question' => '配布物がどこにあるか分からない',
				'answer' => '配布物は「お知らせ」タブにあるPDF一覧からダウンロードできます。'
			),
			array(
				'faq_id' => 3,
				'question' => 'チャットの通知が届かない',
				'answer' => '通知設定がOFFになっている可能性があります。アプリの設定をご確認ください。'
			),
			array(
				'faq_id' => 4,
				'question' => 'コミュニティの作成方法が知りたい',
				'answer' => '「コミュニティを作成」ボタンを押し、必要事項を入力して作成できます。'
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
	@brief	FAQアコーディオンの取得
	@return	FAQアコーディオン文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_faq_accordion(){
		$accordion_str = '';
		$first = true;
		
		foreach($this->faqs as $faq){
			$question = display($faq['question']);
			$answer = display($faq['answer']);
			$faq_id = $faq['faq_id'];
			$show_class = $first ? 'show' : '';
			$expanded = $first ? 'true' : 'false';
			$collapsed = $first ? '' : 'collapsed';
			
			$accordion_str .= <<<END_BLOCK
<div class="accordion-item">
    <h2 class="accordion-header" id="heading{$faq_id}">
        <button class="accordion-button {$collapsed}" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapse{$faq_id}" aria-expanded="{$expanded}" aria-controls="collapse{$faq_id}">
            {$question}
        </button>
    </h2>
    <div id="collapse{$faq_id}" class="accordion-collapse collapse {$show_class}" aria-labelledby="heading{$faq_id}"
         data-bs-parent="#faqAccordion">
        <div class="accordion-body">
            {$answer}
        </div>
    </div>
</div>
END_BLOCK;
			$first = false;
		}
		
		return $accordion_str;
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
    <title>よくある質問</title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <main class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto main-content-styles">
        <div class="mx-auto" style="max-width: 800px; padding-top: 80px;">

            <!-- タイトルとボタン（横並び） -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">よくある質問一覧</h2>
                <a href="faq_create.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> 質問をする
                </a>
            </div>

            <!-- FAQアコーディオン -->
            <div class="accordion shadow" id="faqAccordion">
                <?= $this->get_faq_accordion(); ?>
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
