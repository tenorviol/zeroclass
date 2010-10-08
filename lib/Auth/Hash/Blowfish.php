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
