<?php

class Users {
static function loadUserData($userId) {
		$db=getdb();
		$user_data=$db->getrow("select * from users where id='{$userId}'");
		return $user_data;
	}
	
	static function getUserId() {
	
		return (int)$_SESSION['user_id'];
	}
	
	static function getAgentId() {
		$data=Users::getUserData();
		return (int)$data['agent_id'];
	}
	
	static function getUserRightsId() {
		$data=Users::getUserData();
		return (int)$data['user_rights_id'];
	}
	
	static function logout() {
		$_SESSION['user_id']=0;
		$_SESSION['user_data']=array();
	}
	
	static function getUserData() {
		return $_SESSION['user_data'];
	}
	
	static function getUserUsername() {
		$data=Users::getUserData();
		return $data['username'];
	}
	
	static function getUserName() {
		$data=Users::getUserData();
		return $data['name'];
	}
	
	static function getUserEmail() {
		$data=Users::getUserData();
		return $data['emailaddress'];
	}
	
	static function is_active() {
		$data=Users::getUserData();
		return (int)$data['is_active'];
	}
	
	static function getUserStatusId() {
		$data=Users::getUserData();
		return $data['status_id'];
	}
	
	static function canCreateNewHistory() {
		
		Users::getUserStatusId();
		$statuses_true=array(USER_STATUS_ADMIN,USER_STATUS_SECRETARY,USER_STATUS_DISPATCHER);
		return in_array(Users::getUserStatusId(),$statuses_true);
	}
}

?>