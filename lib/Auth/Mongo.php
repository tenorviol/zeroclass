<?php
/**
 * Zeroclass library
 * http://github.com/tenorviol/zeroclass
 *
 * Copyright (c) 2010 Christopher Johnson
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE. 
 */

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
