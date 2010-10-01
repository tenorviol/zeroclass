<?php

require_once 'lib/autoload.php';

/**
 * IMPORTANT: This test does not guarantee the hash class is cryptographically sound.
 *            Rather, it tests some basic properties of the generated hashes.
 */
class Auth_HashTest extends PHPUnit_Framework_TestCase {

	public function test() {
		$auth = new Auth_Hash_Blowfish();
		$password = 'password';
		
		$start = microtime(true);
		
		// hash with auto-generated salt
		$hash = $auth->hashPassword($password);
		
		// same credentials, same hash
		$rehash = $auth->hashPassword($password, $hash);
		$this->assertEquals($hash, $rehash);
		
		// new salt, new hash
		$newhash = $auth->hashPassword($password);
		$this->assertNotEquals($hash, $newhash);
		
		// new password, new hash
		$newpass = $auth->hashPassword('new password', $hash);
		$this->assertNotEquals($hash, $newpass);
		
		// hashing passwords should be slow
		$time = microtime(true) - $start;
		$this->assertGreaterThan(.1, $time);
	}
	
	/**
	 * @expectedException Exception
	 */
	public function testInvalidHash() {
		$auth = new Auth_Hash();
		// this is an invalid Blowfish salt
		$hash = $auth->hashPassword('foo', '$2a$01$!@#$');
	}
}
