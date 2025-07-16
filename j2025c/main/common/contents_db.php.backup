<?php
/*!
@file contents_db.php
@brief 
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

////////////////////////////////////
//莉･荳九．B繧ｯ繝ｩ繧ｹ菴ｿ逕ｨ萓・

//--------------------------------------------------------------------------------------
///	驛ｽ驕灘ｺ懃恁繧ｯ繝ｩ繧ｹ
//--------------------------------------------------------------------------------------
class cprefecture extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	縺吶∋縺ｦ縺ｮ蛟区焚繧貞ｾ励ｋ
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@return	蛟区焚
	*/
	//--------------------------------------------------------------------------------------
	public function get_all_count($debug){
		//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺・
		$query = <<< END_BLOCK
select
count(*)
from
prefecture
where
1
END_BLOCK;
		//遨ｺ縺ｮ繝・・繧ｿ
		$prep_arr = array();
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮselect_query()繝｡繝ｳ繝宣未謨ｰ繧貞他縺ｶ
		$this->select_query(
			$debug,			//繝・ヰ繝・げ陦ｨ遉ｺ縺吶ｋ縺九←縺・°
			$query,			//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺拘QL
			$prep_arr		//繝・・繧ｿ縺ｮ驟榊・
		);
		if($row = $this->fetch_assoc()){
			//蜿門ｾ励＠縺溷区焚繧定ｿ斐☆
			return $row['count(*)'];
		}
		else{
			return 0;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	謖・ｮ壹＆繧後◆遽・峇縺ｮ驟榊・繧貞ｾ励ｋ
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$from	謚ｽ蜃ｺ髢句ｧ玖｡・
	@param[in]	$limit	謚ｽ蜃ｺ謨ｰ
	@return	驟榊・・・谺｡蜈・・蛻励↓縺ｪ繧具ｼ・
	*/
	//--------------------------------------------------------------------------------------
	public function get_all($debug,$from,$limit){
		$arr = array();
		//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺・
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
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮselect_query()繝｡繝ｳ繝宣未謨ｰ繧貞他縺ｶ
		$this->select_query(
			$debug,			//繝・ヰ繝・げ陦ｨ遉ｺ縺吶ｋ縺九←縺・°
			$query,			//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺拘QL
			$prep_arr		//繝・・繧ｿ縺ｮ驟榊・
		);
		//鬆・ｬ｡蜿悶ｊ蜃ｺ縺・
		while($row = $this->fetch_assoc()){
			$arr[] = $row;
		}
		//蜿門ｾ励＠縺滄・蛻励ｒ霑斐☆
		return $arr;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	謖・ｮ壹＆繧後◆ID縺ｮ驟榊・繧貞ｾ励ｋ
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$id		ID
	@return	驟榊・・・谺｡蜈・・蛻励↓縺ｪ繧具ｼ臥ｩｺ縺ｮ蝣ｴ蜷医・false
	*/
	//--------------------------------------------------------------------------------------
	public function get_tgt($debug,$id){
		if(!cutil::is_number($id)
		||  $id < 1){
			//false繧定ｿ斐☆
			return false;
		}
		//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺・
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
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮselect_query()繝｡繝ｳ繝宣未謨ｰ繧貞他縺ｶ
		$this->select_query(
			$debug,			//繝・ヰ繝・げ陦ｨ遉ｺ縺吶ｋ縺九←縺・°
			$query,			//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺拘QL
			$prep_arr		//繝・・繧ｿ縺ｮ驟榊・
		);
		return $this->fetch_assoc();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝・せ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繝・せ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
///	繝輔Ν繝ｼ繝・け繝ｩ繧ｹ
//--------------------------------------------------------------------------------------
class cfruits extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	縺吶∋縺ｦ縺ｮ蛟区焚繧貞ｾ励ｋ
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@return	蛟区焚
	*/
	//--------------------------------------------------------------------------------------
	public function get_all_count($debug){
		//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺・
		$query = <<< END_BLOCK
select
count(*)
from
fruits
where
1
END_BLOCK;
		//遨ｺ縺ｮ繝・・繧ｿ
		$prep_arr = array();
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮselect_query()繝｡繝ｳ繝宣未謨ｰ繧貞他縺ｶ
		$this->select_query(
			$debug,			//繝・ヰ繝・げ陦ｨ遉ｺ縺吶ｋ縺九←縺・°
			$query,			//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺拘QL
			$prep_arr		//繝・・繧ｿ縺ｮ驟榊・
		);
		if($row = $this->fetch_assoc()){
			//蜿門ｾ励＠縺溷区焚繧定ｿ斐☆
			return $row['count(*)'];
		}
		else{
			return 0;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	縺吶∋縺ｦ縺ｮ驟榊・繧貞ｾ励ｋ
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@return	驟榊・・・谺｡蜈・・蛻励↓縺ｪ繧具ｼ・
	*/
	//--------------------------------------------------------------------------------------
	public function get_all($debug){
		$arr = array();
		//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺・
		$query = <<< END_BLOCK
select
*
from
fruits
where
1
order by
fruits_id asc
END_BLOCK;
		//遨ｺ縺ｮ繝・・繧ｿ
		$prep_arr = array();
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮselect_query()繝｡繝ｳ繝宣未謨ｰ繧貞他縺ｶ
		$this->select_query(
			$debug,			//繝・ヰ繝・げ陦ｨ遉ｺ縺吶ｋ縺九←縺・°
			$query,			//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺拘QL
			$prep_arr		//繝・・繧ｿ縺ｮ驟榊・
		);
		//鬆・ｬ｡蜿悶ｊ蜃ｺ縺・
		while($row = $this->fetch_assoc()){
			$arr[] = $row;
		}
		//蜿門ｾ励＠縺滄・蛻励ｒ霑斐☆
		return $arr;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	謖・ｮ壹＆繧後◆ID縺ｮ驟榊・繧貞ｾ励ｋ
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$id		ID
	@return	驟榊・・・谺｡蜈・・蛻励↓縺ｪ繧具ｼ臥ｩｺ縺ｮ蝣ｴ蜷医・false
	*/
	//--------------------------------------------------------------------------------------
	public function get_tgt($debug,$id){
		if(!cutil::is_number($id)
		||  $id < 1){
			//false繧定ｿ斐☆
			return false;
		}
		//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺・
		$query = <<< END_BLOCK
select
*
from
fruits
where
fruits_id = :fruits_id
END_BLOCK;
		$prep_arr = array(
				':fruits_id' => (int)$id
		);
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮselect_query()繝｡繝ｳ繝宣未謨ｰ繧貞他縺ｶ
		$this->select_query(
			$debug,			//繝・ヰ繝・げ陦ｨ遉ｺ縺吶ｋ縺九←縺・°
			$query,			//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺拘QL
			$prep_arr		//繝・・繧ｿ縺ｮ驟榊・
		);
		return $this->fetch_assoc();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝・せ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繝・せ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
///	繝｡繝ｳ繝舌・繧ｯ繝ｩ繧ｹ
//--------------------------------------------------------------------------------------
class cmember extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	縺吶∋縺ｦ縺ｮ蛟区焚繧貞ｾ励ｋ
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@return	蛟区焚
	*/
	//--------------------------------------------------------------------------------------
	public function get_all_count($debug){
		//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺・
		$query = <<< END_BLOCK
select
count(*)
from
member,prefecture
where
member.member_prefecture_id = prefecture.prefecture_id
END_BLOCK;
		//遨ｺ縺ｮ繝・・繧ｿ
		$prep_arr = array();
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮselect_query()繝｡繝ｳ繝宣未謨ｰ繧貞他縺ｶ
		$this->select_query(
			$debug,			//繝・ヰ繝・げ陦ｨ遉ｺ縺吶ｋ縺九←縺・°
			$query,			//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺拘QL
			$prep_arr		//繝・・繧ｿ縺ｮ驟榊・
		);
		if($row = $this->fetch_assoc()){
			//蜿門ｾ励＠縺溷区焚繧定ｿ斐☆
			return $row['count(*)'];
		}
		else{
			return 0;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	謖・ｮ壹＆繧後◆遽・峇縺ｮ驟榊・繧貞ｾ励ｋ
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$from	謚ｽ蜃ｺ髢句ｧ玖｡・
	@param[in]	$limit	謚ｽ蜃ｺ謨ｰ
	@return	驟榊・・・谺｡蜈・・蛻励↓縺ｪ繧具ｼ・
	*/
	//--------------------------------------------------------------------------------------
	public function get_all($debug,$from,$limit){
		$arr = array();
		//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺・
		$query = <<< END_BLOCK
select
member.*,prefecture.*
from
member,prefecture
where
member.member_prefecture_id = prefecture.prefecture_id
order by
member.member_id asc
limit :from, :limit
END_BLOCK;
		$prep_arr = array(
				':from' => (int)$from,
				':limit' => (int)$limit);
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮselect_query()繝｡繝ｳ繝宣未謨ｰ繧貞他縺ｶ
		$this->select_query(
			$debug,			//繝・ヰ繝・げ陦ｨ遉ｺ縺吶ｋ縺九←縺・°
			$query,			//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺拘QL
			$prep_arr		//繝・・繧ｿ縺ｮ驟榊・
		);
		//鬆・ｬ｡蜿悶ｊ蜃ｺ縺・
		while($row = $this->fetch_assoc()){
			$arr[] = $row;
		}
		//蜿門ｾ励＠縺滄・蛻励ｒ霑斐☆
		return $arr;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	謖・ｮ壹＆繧後◆ID縺ｮ驟榊・繧貞ｾ励ｋ
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$id		ID
	@return	驟榊・・・谺｡蜈・・蛻励↓縺ｪ繧具ｼ臥ｩｺ縺ｮ蝣ｴ蜷医・false
	*/
	//--------------------------------------------------------------------------------------
	public function get_tgt($debug,$id){
		if(!cutil::is_number($id)
		||  $id < 1){
			//false繧定ｿ斐☆
			return false;
		}
		//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺・
		$query = <<< END_BLOCK
select
member.*,prefecture.*
from
member,prefecture
where
member.member_prefecture_id = prefecture.prefecture_id
and
member.member_id = :member_id
END_BLOCK;
		$prep_arr = array(
				':member_id' => (int)$id
		);
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮselect_query()繝｡繝ｳ繝宣未謨ｰ繧貞他縺ｶ
		$this->select_query(
			$debug,			//繝・ヰ繝・げ陦ｨ遉ｺ縺吶ｋ縺九←縺・°
			$query,			//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺拘QL
			$prep_arr		//繝・・繧ｿ縺ｮ驟榊・
		);
		return $this->fetch_assoc();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝輔Ν繝ｼ繝・→縺ｮ繝槭ャ繝√☆繧矩・蛻励ｒ蠕励ｋ
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$id		ID
	@return	驟榊・・・谺｡蜈・・蛻励↓縺ｪ繧具ｼ・
	*/
	//--------------------------------------------------------------------------------------
	public function get_all_fruits_match($debug,$id){
		if(!cutil::is_number($id)
		||  $id < 1){
			//false繧定ｿ斐☆
			return false;
		}
		//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺・
		$query = <<< END_BLOCK
select
*
from
fruits_match
where
member_id = :member_id
order by
fruits_id asc
END_BLOCK;
		$prep_arr = array(
				':member_id' => (int)$id
		);
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮselect_query()繝｡繝ｳ繝宣未謨ｰ繧貞他縺ｶ
		$this->select_query(
			$debug,			//繝・ヰ繝・げ陦ｨ遉ｺ縺吶ｋ縺九←縺・°
			$query,			//繝励Ξ繝ｼ繧ｹ繝帙Ν繝縺､縺拘QL
			$prep_arr		//繝・・繧ｿ縺ｮ驟榊・
		);
		//鬆・ｬ｡蜿悶ｊ蜃ｺ縺・
		while($row = $this->fetch_assoc()){
			$arr[] = $row['fruits_id'];
		}
		//蜿門ｾ励＠縺滄・蛻励ｒ霑斐☆
		return $arr;
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝・せ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繝・せ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
///	繝ｦ繝ｼ繧ｶ繝ｼ繧ｯ繝ｩ繧ｹ
//--------------------------------------------------------------------------------------
class cusers extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝ｦ繝ｼ繧ｶ繝ｼ逋ｻ骭ｲ
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$data	繝ｦ繝ｼ繧ｶ繝ｼ繝・・繧ｿ
	@return	謌仙粥縺ｮ蝣ｴ蜷・rue縲∝､ｱ謨励・蝣ｴ蜷・alse
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
	@brief	繝ｦ繝ｼ繧ｶ繝ｼ隱崎ｨｼ
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$login	繝ｭ繧ｰ繧､繝ｳID
	@param[in]	$password	繝代せ繝ｯ繝ｼ繝・
	@return	繝ｦ繝ｼ繧ｶ繝ｼ諠・ｱ驟榊・縲∝､ｱ謨励・蝣ｴ蜷・alse
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
	@brief	繝ｦ繝ｼ繧ｶ繝ｼ諠・ｱ蜿門ｾ・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$user_id	繝ｦ繝ｼ繧ｶ繝ｼID
	@return	繝ｦ繝ｼ繧ｶ繝ｼ諠・ｱ驟榊・縲∝､ｱ謨励・蝣ｴ蜷・alse
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
	@brief	縺吶∋縺ｦ縺ｮ繝ｦ繝ｼ繧ｶ繝ｼ蜿門ｾ・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@return	繝ｦ繝ｼ繧ｶ繝ｼ驟榊・
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
	@brief	謖・ｮ壹Θ繝ｼ繧ｶ繝ｼ莉･螟悶・縺吶∋縺ｦ縺ｮ繝ｦ繝ｼ繧ｶ繝ｼ蜿門ｾ・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$user_id	髯､螟悶☆繧九Θ繝ｼ繧ｶ繝ｼID
	@return	繝ｦ繝ｼ繧ｶ繝ｼ驟榊・
	*/
	//--------------------------------------------------------------------------------------
	public function get_all_except_user($debug, $user_id) {
		$arr = array();
		$query = <<< END_BLOCK
SELECT * FROM users WHERE uuid != :user_id ORDER BY user_id ASC
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
	@brief	繝・せ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繝・せ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
///	繧ｳ繝溘Η繝九ユ繧｣繧ｯ繝ｩ繧ｹ
//--------------------------------------------------------------------------------------
class ccommunities extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｳ繝溘Η繝九ユ繧｣菴懈・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$data	繧ｳ繝溘Η繝九ユ繧｣繝・・繧ｿ
	@return	謌仙粥縺ｮ蝣ｴ蜷・rue縲∝､ｱ謨励・蝣ｴ蜷・alse
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
	@brief	繝ｦ繝ｼ繧ｶ繝ｼ縺ｮ繧ｳ繝溘Η繝九ユ繧｣荳隕ｧ蜿門ｾ・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$user_id	繝ｦ繝ｼ繧ｶ繝ｼID
	@return	繧ｳ繝溘Η繝九ユ繧｣驟榊・
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
	@brief	繧ｳ繝溘Η繝九ユ繧｣諠・ｱ蜿門ｾ・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$community_id	繧ｳ繝溘Η繝九ユ繧｣ID
	@return	繧ｳ繝溘Η繝九ユ繧｣諠・ｱ驟榊・縲∝､ｱ謨励・蝣ｴ蜷・alse
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
	@brief	縺吶∋縺ｦ縺ｮ繧ｳ繝溘Η繝九ユ繧｣蜿門ｾ・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@return	繧ｳ繝溘Η繝九ユ繧｣驟榊・
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
	@brief	繝・せ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繝・せ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
///	繧ｳ繝溘Η繝九ユ繧｣繝ｦ繝ｼ繧ｶ繝ｼ繧ｯ繝ｩ繧ｹ
//--------------------------------------------------------------------------------------
class ccommunity_users extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝ｦ繝ｼ繧ｶ繝ｼ繧偵さ繝溘Η繝九ユ繧｣縺ｫ霑ｽ蜉
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$user_id	繝ｦ繝ｼ繧ｶ繝ｼID
	@param[in]	$community_id	繧ｳ繝溘Η繝九ユ繧｣ID
	@return	謌仙粥縺ｮ蝣ｴ蜷・rue縲∝､ｱ謨励・蝣ｴ蜷・alse
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
	@brief	繝ｦ繝ｼ繧ｶ繝ｼ縺後さ繝溘Η繝九ユ繧｣繝｡繝ｳ繝舌・縺九メ繧ｧ繝・け
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$user_id	繝ｦ繝ｼ繧ｶ繝ｼID
	@param[in]	$community_id	繧ｳ繝溘Η繝九ユ繧｣ID
	@return	繝｡繝ｳ繝舌・縺ｮ蝣ｴ蜷・rue縲√◎縺・〒縺ｪ縺代ｌ縺ｰfalse
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
	@brief	繝・せ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繝・せ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
///	繧ｳ繝溘Η繝九ユ繧｣諡帛ｾ・さ繝ｼ繝峨け繝ｩ繧ｹ
//--------------------------------------------------------------------------------------
class ccommunity_invite_codes extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	諡帛ｾ・さ繝ｼ繝我ｽ懈・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$community_id	繧ｳ繝溘Η繝九ユ繧｣ID
	@param[in]	$invite_code	諡帛ｾ・さ繝ｼ繝・
	@return	謌仙粥縺ｮ蝣ｴ蜷・rue縲∝､ｱ謨励・蝣ｴ蜷・alse
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
	@brief	諡帛ｾ・さ繝ｼ繝峨°繧峨さ繝溘Η繝九ユ繧｣ID蜿門ｾ・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$invite_code	諡帛ｾ・さ繝ｼ繝・
	@return	繧ｳ繝溘Η繝九ユ繧｣ID縲∝､ｱ謨励・蝣ｴ蜷・alse
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
	@brief	繧ｳ繝溘Η繝九ユ繧｣ID縺九ｉ諡帛ｾ・さ繝ｼ繝牙叙蠕・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$community_id	繧ｳ繝溘Η繝九ユ繧｣ID
	@return	諡帛ｾ・さ繝ｼ繝峨∝､ｱ謨励・蝣ｴ蜷・alse
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
	@brief	諡帛ｾ・さ繝ｼ繝牙炎髯､
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$community_id	繧ｳ繝溘Η繝九ユ繧｣ID
	@return	謌仙粥縺ｮ蝣ｴ蜷・rue縲∝､ｱ謨励・蝣ｴ蜷・alse
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
	@brief	繝・せ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繝・せ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
///	繧ｯ繝ｩ繧ｹ繧ｯ繝ｩ繧ｹ
//--------------------------------------------------------------------------------------
class cclasses extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｯ繝ｩ繧ｹ菴懈・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$data	繧ｯ繝ｩ繧ｹ繝・・繧ｿ
	@return	謌仙粥縺ｮ蝣ｴ蜷・rue縲∝､ｱ謨励・蝣ｴ蜷・alse
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
	@brief	繧ｳ繝溘Η繝九ユ繧｣縺ｮ繧ｯ繝ｩ繧ｹ荳隕ｧ蜿門ｾ・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$community_id	繧ｳ繝溘Η繝九ユ繧｣ID
	@return	繧ｯ繝ｩ繧ｹ驟榊・
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
	@brief	繧ｯ繝ｩ繧ｹ諠・ｱ蜿門ｾ・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$class_id	繧ｯ繝ｩ繧ｹID
	@return	繧ｯ繝ｩ繧ｹ諠・ｱ驟榊・縲∝､ｱ謨励・蝣ｴ蜷・alse
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
	@brief	繝・せ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繝・せ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
///	繧ｯ繝ｩ繧ｹ繝√Ε繝・ヨ繧ｯ繝ｩ繧ｹ
//--------------------------------------------------------------------------------------
class cclass_chats extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝√Ε繝・ヨ繝｡繝・そ繝ｼ繧ｸ騾∽ｿ｡
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$data	繝√Ε繝・ヨ繝・・繧ｿ
	@return	謌仙粥縺ｮ蝣ｴ蜷・rue縲∝､ｱ謨励・蝣ｴ蜷・alse
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
	@brief	繧ｯ繝ｩ繧ｹ縺ｮ繝√Ε繝・ヨ繝｡繝・そ繝ｼ繧ｸ蜿門ｾ・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$class_id	繧ｯ繝ｩ繧ｹID
	@return	繝√Ε繝・ヨ繝｡繝・そ繝ｼ繧ｸ驟榊・
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
	@brief	繝・せ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繝・せ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
///	繝・Φ繝励Ξ繝ｼ繝医け繝ｩ繧ｹ
//--------------------------------------------------------------------------------------
class ctemprates extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝・Φ繝励Ξ繝ｼ繝井ｽ懈・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$data	繝・Φ繝励Ξ繝ｼ繝医ョ繝ｼ繧ｿ
	@return	謌仙粥縺ｮ蝣ｴ蜷・rue縲∝､ｱ謨励・蝣ｴ蜷・alse
	*/
	//--------------------------------------------------------------------------------------
	public function insert_template($debug, $data) {
		$query = <<< END_BLOCK
INSERT INTO temprates (temprate_title, temprate_text, temprate_user)
VALUES (:temprate_title, :temprate_text, :temprate_user)
END_BLOCK;
		$prep_arr = array(
			':temprate_title' => $data['temprate_title'],
			':temprate_text' => $data['temprate_text'],
			':temprate_user' => $data['temprate_user']
		);
		return $this->change_query($debug, $query, $prep_arr);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝ｦ繝ｼ繧ｶ繝ｼ縺ｮ繝・Φ繝励Ξ繝ｼ繝井ｸ隕ｧ蜿門ｾ・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$user_id	繝ｦ繝ｼ繧ｶ繝ｼID
	@return	繝・Φ繝励Ξ繝ｼ繝磯・蛻・
	*/
	//--------------------------------------------------------------------------------------
	public function get_user_templates($debug, $user_id) {
		$arr = array();
		$query = <<< END_BLOCK
SELECT * FROM temprates 
WHERE temprate_user = :user_id
ORDER BY temprate_id ASC
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
	@brief	繝・Φ繝励Ξ繝ｼ繝亥炎髯､
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$template_id	繝・Φ繝励Ξ繝ｼ繝・D
	@param[in]	$user_id	繝ｦ繝ｼ繧ｶ繝ｼID・域園譛芽・メ繧ｧ繝・け逕ｨ・・
	@return	謌仙粥縺ｮ蝣ｴ蜷・rue縲∝､ｱ謨励・蝣ｴ蜷・alse
	*/
	//--------------------------------------------------------------------------------------
	public function delete_template($debug, $template_id, $user_id) {
		$query = <<< END_BLOCK
DELETE FROM temprates 
WHERE temprate_id = :temprate_id AND temprate_user = :user_id
END_BLOCK;
		$prep_arr = array(
			':temprate_id' => $template_id,
			':user_id' => $user_id
		);
		return $this->change_query($debug, $query, $prep_arr);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝・せ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繝・せ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
///	雉ｪ蝠上け繝ｩ繧ｹ
//--------------------------------------------------------------------------------------
class cquestions extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	雉ｪ蝠丈ｽ懈・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$data	雉ｪ蝠上ョ繝ｼ繧ｿ
	@return	謌仙粥縺ｮ蝣ｴ蜷・rue縲∝､ｱ謨励・蝣ｴ蜷・alse
	*/
	//--------------------------------------------------------------------------------------
	public function insert_question($debug, $data) {
		$query = <<< END_BLOCK
INSERT INTO questions (from_user, to_user, question_title, question_text)
VALUES (:from_user, :to_user, :question_title, :question_text)
END_BLOCK;
		$prep_arr = array(
			':from_user' => $data['from_user'],
			':to_user' => $data['to_user'],
			':question_title' => $data['question_title'],
			':question_text' => $data['question_text']
		);
		return $this->change_query($debug, $query, $prep_arr);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	雉ｪ蝠丈ｸ隕ｧ蜿門ｾ・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$user_id	繝ｦ繝ｼ繧ｶ繝ｼID・亥女菫｡閠・ｼ・
	@return	雉ｪ蝠城・蛻・
	*/
	//--------------------------------------------------------------------------------------
	public function get_user_questions($debug, $user_id) {
		$arr = array();
		$query = <<< END_BLOCK
SELECT q.*, u.user_name as from_user_name 
FROM questions q
LEFT JOIN users u ON q.from_user = u.user_id
WHERE q.to_user = :user_id OR q.to_user IS NULL
ORDER BY q.asked_at DESC
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
	@brief	蝗樒ｭ疲峩譁ｰ
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$question_id	雉ｪ蝠終D
	@param[in]	$answer_text	蝗樒ｭ斐ユ繧ｭ繧ｹ繝・
	@return	謌仙粥縺ｮ蝣ｴ蜷・rue縲∝､ｱ謨励・蝣ｴ蜷・alse
	*/
	//--------------------------------------------------------------------------------------
	public function update_answer($debug, $question_id, $answer_text) {
		$query = <<< END_BLOCK
UPDATE questions 
SET anser_text = :answer_text, is_anser = 1, answered_at = NOW()
WHERE question_id = :question_id
END_BLOCK;
		$prep_arr = array(
			':answer_text' => $answer_text,
			':question_id' => $question_id
		);
		return $this->change_query($debug, $query, $prep_arr);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝・せ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繝・せ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
///	繧ｰ繝ｫ繝ｼ繝励メ繝｣繝・ヨ繧ｯ繝ｩ繧ｹ
//--------------------------------------------------------------------------------------
class cgroup_chats extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｰ繝ｫ繝ｼ繝励メ繝｣繝・ヨ菴懈・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$group_name	繧ｰ繝ｫ繝ｼ繝怜錐
	@return	謌仙粥縺ｮ蝣ｴ蜷医げ繝ｫ繝ｼ繝悠D縲∝､ｱ謨励・蝣ｴ蜷・alse
	*/
	//--------------------------------------------------------------------------------------
	public function create_group($debug, $group_name) {
		$query = <<< END_BLOCK
INSERT INTO group_chats (group_name) VALUES (:group_name)
END_BLOCK;
		$prep_arr = array(':group_name' => $group_name);
		if($this->change_query($debug, $query, $prep_arr)) {
			return $this->get_last_insert_id();
		}
		return false;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝ｦ繝ｼ繧ｶ繝ｼ縺ｮ繧ｰ繝ｫ繝ｼ繝嶺ｸ隕ｧ蜿門ｾ・
	@param[in]	$debug	繝・ヰ繝・げ蜃ｺ蜉帙ｒ縺吶ｋ縺九←縺・°
	@param[in]	$user_id	繝ｦ繝ｼ繧ｶ繝ｼID
	@return	繧ｰ繝ｫ繝ｼ繝鈴・蛻・
	*/
	//--------------------------------------------------------------------------------------
	public function get_user_groups($debug, $user_id) {
		$arr = array();
		$query = <<< END_BLOCK
SELECT gc.* FROM group_chats gc
JOIN group_chat_members gcm ON gc.group_id = gcm.group_id
WHERE gcm.user_id = :user_id
ORDER BY gc.group_id ASC
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
	@brief	繝・せ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繝・せ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__destruct();
	}
}


