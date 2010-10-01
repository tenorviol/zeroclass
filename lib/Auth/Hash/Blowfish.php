<?php

class Auth_Hash_Blowfish {
	
	private $iterations_log2;
	
	/**
	 * Create a new hashing object. Passwords will be re-hashed
	 * (2 ^ iterations_log2) times, so incrementing this number
	 * doubles the hash time. You want to pick a number that
	 * reasonably balances between application efficiency and
	 * aggravating potential dictionary attacks. This number
	 * should be increased as available processing power grows.
	 *
	 * @param int $iterations_log2
	 */
	public function __construct($iterations_log2 = 10) {
		if (!CRYPT_BLOWFISH) {
			throw new Exception('Blowfish crypt is not available');
		}
		if ($iterations_log2 < 4 || $iterations_log2 > 31) {
			throw new InvalidArgumentException("iterations_log2 must be in the range 4-31; $iterations_log2 given");
		}
		$this->iterations_log2 = $iterations_log2;
	}
	
	/**
	 * Hash a password. To compare a password to a previous password hash,
	 * use the old hash as the salt.
	 *
	 *   $oldhash = $blowfish->hashPassword('my password');
	 *   $newhash = $blowfish->hashPassword('my password', $oldhash);
	 *
	 * @param string $password 
	 * @param string $salt 
	 * @return string
	 */
	public function hashPassword($password, $salt = null) {
		if (!$salt) {
			$salt = $this->generateSalt();
		}
		$hash = crypt($password, $salt);
		if (!$hash || $hash == '*0' || $hash == '*1') {
			throw new Exception("Invalid hash");
		}
		return $hash;
	}
	
	/**
	 * Generate a randomly generated, Blowfish-crypt salt.
	 *
	 * @return string
	 */
	public function generateSalt() {
		$handle = fopen('/dev/urandom', 'r');
		if (!$handle) {
			throw new Exception('Unable to open /dev/urandom');
		}
		$randoms = fread($handle, 22);
		fclose($handle);
		$set = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$salt = '';
		for ($i = 0; $i < 22; $i++) {
			$int = ord($randoms[$i]) & 0x3f;
			$salt .= $set[$int];
		}
		$prefix = sprintf('$2a$%02d$', $this->iterations_log2);
		return $prefix.$salt;
	}
}
