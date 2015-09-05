<?php
/**
 * Encryption and hashing Tools
 * This script contains an helper to encrypt and derypt strings
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

class Encryption {
	
	const ENCRYPT_KEY = 'apine_framework';
	
	public static function encrypt ($origin_string) {
		
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, self::ENCRYPT_KEY, utf8_encode($origin_string), MCRYPT_MODE_ECB, $iv);
		
		return $encrypted_string;
		
	}
	
	public static function decrypt ($encrypted_string) {
		
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, self::ENCRYPT_KEY, $encrypted_string, MCRYPT_MODE_ECB, $iv);
		
		return $decrypted_string;
		
	}
	
	public static function hash_password ($clear_password, $username) {
		
		$encrypt_user = self::encrypt($username);
		$password = $clear_password . $encrypt_user;
		$ciphered_password = hash('sha256', $password);

		return $ciphered_password;
		
	}
	
	public static function hash_app_key () {
		
		// Magic Here
		
	}
	
}