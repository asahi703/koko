<?php
/*!
@file contents_db.php
@brief 
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

////////////////////////////////////
//以下、DBクラス使用例

//--------------------------------------------------------------------------------------
///	都道府県クラス
//--------------------------------------------------------------------------------------
class cprefecture extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	すべての個数を得る
	@param[in]	$debug	デバッグ出力をするかどうか
	@return	個数
	*/
	//--------------------------------------------------------------------------------------
	public function get_all_count($debug){
		//プレースホルダつき
		$query = <<< END_BLOCK
select
count(*)
from
prefecture
where
1
END_BLOCK;
		//空のデータ
		$prep_arr = array();
		//親クラスのselect_query()メンバ関数を呼ぶ
		$this->select_query(
			$debug,			//デバッグ表示するかどうか
			$query,			//プレースホルダつきSQL
			$prep_arr		//データの配列
		);
		if($row = $this->fetch_assoc()){
			//取得した個数を返す
			return $row['count(*)'];
		}
		else{
			return 0;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	指定された範囲の配列を得る
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$from	抽出開始行
	@param[in]	$limit	抽出数
	@return	配列（2次元配列になる）
	*/
	//--------------------------------------------------------------------------------------
	public function get_all($debug,$from,$limit){
		$arr = array();
		//プレースホルダつき
		$query = <<< END_BLOCK
select
*
from
prefecture
where
1
order by
prefecture_id asc
limit :from, :limit
END_BLOCK;
		$prep_arr = array(
				':from' => (int)$from,
				':limit' => (int)$limit);
		//親クラスのselect_query()メンバ関数を呼ぶ
		$this->select_query(
			$debug,			//デバッグ表示するかどうか
			$query,			//プレースホルダつきSQL
			$prep_arr		//データの配列
		);
		//順次取り出す
		while($row = $this->fetch_assoc()){
			$arr[] = $row;
		}
		//取得した配列を返す
		return $arr;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	指定されたIDの配列を得る
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$id		ID
	@return	配列（1次元配列になる）空の場合はfalse
	*/
	//--------------------------------------------------------------------------------------
	public function get_tgt($debug,$id){
		if(!cutil::is_number($id)
		||  $id < 1){
			//falseを返す
			return false;
		}
		//プレースホルダつき
		$query = <<< END_BLOCK
select
*
from
prefecture
where
prefecture_id = :prefecture_id
END_BLOCK;
		$prep_arr = array(
				':prefecture_id' => (int)$id
		);
		//親クラスのselect_query()メンバ関数を呼ぶ
		$this->select_query(
			$debug,			//デバッグ表示するかどうか
			$query,			//プレースホルダつきSQL
			$prep_arr		//データの配列
		);
		return $this->fetch_assoc();
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

//--------------------------------------------------------------------------------------
///	ユーザークラス
//--------------------------------------------------------------------------------------
class cusers extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ユーザー登録
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$data	ユーザーデータ
	@return	成功の場合true、失敗の場合false
	*/
	//--------------------------------------------------------------------------------------
	public function insert_user($debug, $data) {
		$query = <<< END_BLOCK
INSERT INTO users 
(user_name, user_mailaddress, user_password, user_is_teacher, user_text, user_login)
VALUES 
(:user_name, :user_mailaddress, :user_password, :user_is_teacher, :user_text, :user_login)
END_BLOCK;
		$prep_arr = array(
			':user_name' => $data['user_name'],
			':user_mailaddress' => $data['user_mailaddress'],
			':user_password' => $data['user_password'],
			':user_is_teacher' => $data['user_is_teacher'] ?? 0,
			':user_text' => $data['user_text'] ?? null,
			':user_login' => $data['user_login']
		);
		return $this->change_query($debug, $query, $prep_arr);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ユーザー認証
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$login	ログインID
	@param[in]	$password	パスワード
	@return	ユーザー情報配列、失敗の場合false
	*/
	//--------------------------------------------------------------------------------------
	public function authenticate_user($debug, $login, $password) {
		$query = <<< END_BLOCK
SELECT * FROM users 
WHERE user_login = :user_login 
AND user_password = :user_password
END_BLOCK;
		$prep_arr = array(
			':user_login' => $login,
			':user_password' => $password
		);
		$this->select_query($debug, $query, $prep_arr);
		return $this->fetch_assoc();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ユーザー情報取得
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$user_id	ユーザーID
	@return	ユーザー情報配列、失敗の場合false
	*/
	//--------------------------------------------------------------------------------------
	public function get_user($debug, $user_id) {
		$query = <<< END_BLOCK
SELECT * FROM users WHERE user_id = :user_id
END_BLOCK;
		$prep_arr = array(':user_id' => (int)$user_id);
		$this->select_query($debug, $query, $prep_arr);
		return $this->fetch_assoc();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	すべてのユーザー取得
	@param[in]	$debug	デバッグ出力をするかどうか
	@return	ユーザー配列
	*/
	//--------------------------------------------------------------------------------------
	public function get_all_users($debug) {
		$arr = array();
		$query = <<< END_BLOCK
SELECT * FROM users ORDER BY user_id ASC
END_BLOCK;
		$prep_arr = array();
		$this->select_query($debug, $query, $prep_arr);
		while($row = $this->fetch_assoc()){
			$arr[] = $row;
		}
		return $arr;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	指定ユーザー以外のすべてのユーザー取得
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$user_id	除外するユーザーID
	@return	ユーザー配列
	*/
	//--------------------------------------------------------------------------------------
	public function get_all_except_user($debug, $user_id) {
		$arr = array();
		$query = <<< END_BLOCK
SELECT * FROM users WHERE user_id != :user_id ORDER BY user_id ASC
END_BLOCK;
		$prep_arr = array(':user_id' => $user_id);
		$this->select_query($debug, $query, $prep_arr);
		while($row = $this->fetch_assoc()){
			$arr[] = $row;
		}
		return $arr;
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

//--------------------------------------------------------------------------------------
///	コミュニティクラス
//--------------------------------------------------------------------------------------
class ccommunities extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ作成
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$data	コミュニティデータ
	@return	成功の場合true、失敗の場合false
	*/
	//--------------------------------------------------------------------------------------
	public function insert_community($debug, $data) {
		$dataarr = array(
			'community_name' => $data['community_name'],
			'community_description' => $data['community_description'] ?? '',
			'community_owner' => $data['community_owner']
		);
		return $this->insert_core($debug, 'communities', $dataarr);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ユーザーのコミュニティ一覧取得
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$user_id	ユーザーID
	@return	コミュニティ配列
	*/
	//--------------------------------------------------------------------------------------
	public function get_user_communities($debug, $user_id) {
		$arr = array();
		$query = <<< END_BLOCK
SELECT c.* FROM communities c
LEFT JOIN community_users cu ON c.community_id = cu.community_id
WHERE c.community_owner = :user_id OR cu.user_id = :user_id2
ORDER BY c.community_id ASC
END_BLOCK;
		$prep_arr = array(
			':user_id' => $user_id,
			':user_id2' => $user_id
		);
		$this->select_query($debug, $query, $prep_arr);
		while($row = $this->fetch_assoc()){
			$arr[] = $row;
		}
		return $arr;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ情報取得
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$community_id	コミュニティID
	@return	コミュニティ情報配列、失敗の場合false
	*/
	//--------------------------------------------------------------------------------------
	public function get_community($debug, $community_id) {
		$query = <<< END_BLOCK
SELECT * FROM communities WHERE community_id = :community_id
END_BLOCK;
		$prep_arr = array(':community_id' => (int)$community_id);
		$this->select_query($debug, $query, $prep_arr);
		return $this->fetch_assoc();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	すべてのコミュニティ取得
	@param[in]	$debug	デバッグ出力をするかどうか
	@return	コミュニティ配列
	*/
	//--------------------------------------------------------------------------------------
	public function get_all_communities($debug) {
		$arr = array();
		$query = <<< END_BLOCK
SELECT * FROM communities ORDER BY community_id ASC
END_BLOCK;
		$prep_arr = array();
		$this->select_query($debug, $query, $prep_arr);
		while($row = $this->fetch_assoc()){
			$arr[] = $row;
		}
		return $arr;
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

//--------------------------------------------------------------------------------------
///	コミュニティユーザークラス
//--------------------------------------------------------------------------------------
class ccommunity_users extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ユーザーをコミュニティに追加
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$user_id	ユーザーID
	@param[in]	$community_id	コミュニティID
	@return	成功の場合true、失敗の場合false
	*/
	//--------------------------------------------------------------------------------------
	public function add_user($debug, $user_id, $community_id) {
		$dataarr = array(
			'user_id' => $user_id,
			'community_id' => $community_id
		);
		return $this->insert_core($debug, 'community_users', $dataarr);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ユーザーがコミュニティメンバーかチェック
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$user_id	ユーザーID
	@param[in]	$community_id	コミュニティID
	@return	メンバーの場合true、そうでなければfalse
	*/
	//--------------------------------------------------------------------------------------
	public function is_member($debug, $user_id, $community_id) {
		$query = <<< END_BLOCK
SELECT COUNT(*) as count FROM community_users 
WHERE user_id = :user_id AND community_id = :community_id
END_BLOCK;
		$prep_arr = array(
			':user_id' => $user_id,
			':community_id' => $community_id
		);
		$this->select_query($debug, $query, $prep_arr);
		$row = $this->fetch_assoc();
		return $row && $row['count'] > 0;
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

//--------------------------------------------------------------------------------------
///	コミュニティ招待コードクラス
//--------------------------------------------------------------------------------------
class ccommunity_invite_codes extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	招待コード作成
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$community_id	コミュニティID
	@param[in]	$invite_code	招待コード
	@return	成功の場合true、失敗の場合false
	*/
	//--------------------------------------------------------------------------------------
	public function create_invite_code($debug, $community_id, $invite_code) {
		$dataarr = array(
			'community_id' => $community_id,
			'invite_code' => $invite_code
		);
		return $this->insert_core($debug, 'community_invite_codes', $dataarr);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	招待コードからコミュニティID取得
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$invite_code	招待コード
	@return	コミュニティID、失敗の場合false
	*/
	//--------------------------------------------------------------------------------------
	public function get_community_by_code($debug, $invite_code) {
		$query = <<< END_BLOCK
SELECT community_id FROM community_invite_codes 
WHERE invite_code = :invite_code
END_BLOCK;
		$prep_arr = array(':invite_code' => $invite_code);
		$this->select_query($debug, $query, $prep_arr);
		$row = $this->fetch_assoc();
		return $row ? $row['community_id'] : false;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティIDから招待コード取得
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$community_id	コミュニティID
	@return	招待コード、失敗の場合false
	*/
	//--------------------------------------------------------------------------------------
	public function get_invite_code_by_community($debug, $community_id) {
		$query = <<< END_BLOCK
SELECT invite_code FROM community_invite_codes 
WHERE community_id = :community_id
END_BLOCK;
		$prep_arr = array(':community_id' => $community_id);
		$this->select_query($debug, $query, $prep_arr);
		$row = $this->fetch_assoc();
		return $row ? $row['invite_code'] : false;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	招待コード削除
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$community_id	コミュニティID
	@return	成功の場合true、失敗の場合false
	*/
	//--------------------------------------------------------------------------------------
	public function delete_invite_code($debug, $community_id) {
		$query = <<< END_BLOCK
DELETE FROM community_invite_codes 
WHERE community_id = :community_id
END_BLOCK;
		$prep_arr = array(':community_id' => $community_id);
		return $this->change_query($debug, $query, $prep_arr);
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

//--------------------------------------------------------------------------------------
///	クラスクラス
//--------------------------------------------------------------------------------------
class cclasses extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	クラス作成
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$data	クラスデータ
	@return	成功の場合true、失敗の場合false
	*/
	//--------------------------------------------------------------------------------------
	public function insert_class($debug, $data) {
		$query = <<< END_BLOCK
INSERT INTO classes (class_name, class_community)
VALUES (:class_name, :class_community)
END_BLOCK;
		$prep_arr = array(
			':class_name' => $data['class_name'],
			':class_community' => $data['class_community']
		);
		return $this->change_query($debug, $query, $prep_arr);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティのクラス一覧取得
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$community_id	コミュニティID
	@return	クラス配列
	*/
	//--------------------------------------------------------------------------------------
	public function get_community_classes($debug, $community_id) {
		$arr = array();
		$query = <<< END_BLOCK
SELECT * FROM classes 
WHERE class_community = :community_id
ORDER BY class_id ASC
END_BLOCK;
		$prep_arr = array(':community_id' => $community_id);
		$this->select_query($debug, $query, $prep_arr);
		while($row = $this->fetch_assoc()){
			$arr[] = $row;
		}
		return $arr;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	クラス情報取得
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$class_id	クラスID
	@return	クラス情報配列、失敗の場合false
	*/
	//--------------------------------------------------------------------------------------
	public function get_class($debug, $class_id) {
		$query = <<< END_BLOCK
SELECT c.*, cm.community_name 
FROM classes c
JOIN communities cm ON c.class_community = cm.community_id
WHERE c.class_id = :class_id
END_BLOCK;
		$prep_arr = array(':class_id' => (int)$class_id);
		$this->select_query($debug, $query, $prep_arr);
		return $this->fetch_assoc();
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

//--------------------------------------------------------------------------------------
///	クラスチャットクラス
//--------------------------------------------------------------------------------------
class cclass_chats extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	チャットメッセージ送信
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$data	チャットデータ
	@return	成功の場合true、失敗の場合false
	*/
	//--------------------------------------------------------------------------------------
	public function insert_message($debug, $data) {
		$query = <<< END_BLOCK
INSERT INTO class_chats (class_id, user_id, message, file_path)
VALUES (:class_id, :user_id, :message, :file_path)
END_BLOCK;
		$prep_arr = array(
			':class_id' => $data['class_id'],
			':user_id' => $data['user_id'],
			':message' => $data['message'],
			':file_path' => $data['file_path'] ?? null
		);
		return $this->change_query($debug, $query, $prep_arr);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	クラスのチャットメッセージ取得
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$class_id	クラスID
	@return	チャットメッセージ配列
	*/
	//--------------------------------------------------------------------------------------
	public function get_class_messages($debug, $class_id) {
		$arr = array();
		$query = <<< END_BLOCK
SELECT cc.*, u.user_name 
FROM class_chats cc
JOIN users u ON cc.user_id = u.user_id
WHERE cc.class_id = :class_id
ORDER BY cc.created_at ASC
END_BLOCK;
		$prep_arr = array(':class_id' => $class_id);
		$this->select_query($debug, $query, $prep_arr);
		while($row = $this->fetch_assoc()){
			$arr[] = $row;
		}
		return $arr;
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

//--------------------------------------------------------------------------------------
///	グループチャットクラス
//--------------------------------------------------------------------------------------
class cgroup_chats extends crecord {
	public function __construct() {
		parent::__construct();
	}
	public function get_all_groups($debug) {
		$arr = array();
		$query = <<<END_BLOCK
SELECT group_id, group_name FROM group_chats ORDER BY group_id DESC
END_BLOCK;
		$prep_arr = array();
		$this->select_query($debug, $query, $prep_arr);
		while($row = $this->fetch_assoc()){
			$arr[] = $row;
		}
		return $arr;
	}
	public function __destruct(){
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
///	グループチャットメッセージクラス
//--------------------------------------------------------------------------------------
class cgroup_chat_messages extends crecord {
	public function __construct() {
		parent::__construct();
	}
	public function get_group_messages($debug, $group_id) {
		$arr = array();
		$query = <<<END_BLOCK
SELECT m.*, u.user_name, u.user_id
FROM group_chat_messages m
JOIN users u ON m.user_id = u.user_id
WHERE m.group_id = :group_id
ORDER BY m.sent_at ASC
END_BLOCK;
		$prep_arr = array(':group_id' => $group_id);
		$this->select_query($debug, $query, $prep_arr);
		while($row = $this->fetch_assoc()){
			$arr[] = $row;
		}
		return $arr;
	}
	public function __destruct(){
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
///	テンプレートクラス
//--------------------------------------------------------------------------------------
class ctemplates extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ユーザーのテンプレート取得
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$user_id	ユーザーID
	@return	テンプレート配列
	*/
	//--------------------------------------------------------------------------------------
	public function get_user_templates($debug, $user_id) {
		$arr = array();
		$query = <<<END_BLOCK
SELECT temprate_id, temprate_title, temprate_text 
FROM temprates 
WHERE temprate_user = :user_id 
ORDER BY temprate_id DESC
END_BLOCK;
		$prep_arr = array(':user_id' => $user_id);
		$this->select_query($debug, $query, $prep_arr);
		while($row = $this->fetch_assoc()){
			$arr[] = $row;
		}
		return $arr;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	テンプレート作成
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$data	テンプレートデータ
	@return	成功の場合true、失敗の場合false
	*/
	//--------------------------------------------------------------------------------------
	public function insert_template($debug, $data) {
		$dataarr = array(
			'temprate_title' => $data['temprate_title'],
			'temprate_text' => $data['temprate_text'],
			'temprate_user' => $data['temprate_user']
		);
		return $this->insert_core($debug, 'temprates', $dataarr);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	テンプレート取得
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$template_id	テンプレートID
	@return	テンプレート情報配列、失敗の場合false
	*/
	//--------------------------------------------------------------------------------------
	public function get_template($debug, $template_id) {
		$query = <<<END_BLOCK
SELECT * FROM temprates WHERE temprate_id = :template_id
END_BLOCK;
		$prep_arr = array(':template_id' => (int)$template_id);
		$this->select_query($debug, $query, $prep_arr);
		return $this->fetch_assoc();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	テンプレート更新
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$template_id	テンプレートID
	@param[in]	$data	更新データ
	@return	成功の場合true、失敗の場合false
	*/
	//--------------------------------------------------------------------------------------
	public function update_template($debug, $template_id, $data) {
		$query = <<<END_BLOCK
UPDATE temprates 
SET temprate_title = :title, temprate_text = :text
WHERE temprate_id = :template_id
END_BLOCK;
		$prep_arr = array(
			':title' => $data['temprate_title'],
			':text' => $data['temprate_text'],
			':template_id' => (int)$template_id
		);
		return $this->change_query($debug, $query, $prep_arr);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	テンプレート削除
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$template_id	テンプレートID
	@return	成功の場合true、失敗の場合false
	*/
	//--------------------------------------------------------------------------------------
	public function delete_template($debug, $template_id) {
		$query = <<<END_BLOCK
DELETE FROM temprates WHERE temprate_id = :template_id
END_BLOCK;
		$prep_arr = array(':template_id' => (int)$template_id);
		return $this->change_query($debug, $query, $prep_arr);
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
