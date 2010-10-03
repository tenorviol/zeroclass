<?php

class Auth_Mongo implements Auth {
	
	private $users;
	
	public function __construct(MongoCollection $users) {
		$this->users = $users;
	}
	
	public function setPassword($user_id, $password) {
		$hash = $this->hashPassword($password);
		$result = $this->users->update(
			array('_id'=>$user_id),
			array('_id'=>$user_id, 'passhash'=>$hash),
			array('upsert'=>true, 'safe'=>true)
		);
		if ($result['n'] != 1) {
			throw new Auth_Exception();
		}
	}
	
	public function deletePassword($user_id) {
		$this->users->remove(array('_id'=>$user_id));
	}
	
	public function login($user_id, $password) {
		$this->checkPassword($user_id, $password);
		$_SESSION['user_id'] = $user_id;
	}
	
	public function logout() {
		unset($_SESSION['user_id']);
	}
	
	public function checkPassword($user_id, $password) {
		$user = $this->users->findOne(array('_id'=>$user_id));
		if ($user === null) {
			throw new Auth_Exception();
		}
		$hash = $this->hashPassword($password, $user['passhash']);
		if ($user['passhash'] != $hash) {
			throw new Auth_Exception();
		}
	}
	
	public function hashPassword($password, $salt = null) {
		$bcrypt = new Auth_Hash_Blowfish();
		return $bcrypt->hashPassword($password, $salt);
	}
	
	public function userId() {
		return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
	}
}
