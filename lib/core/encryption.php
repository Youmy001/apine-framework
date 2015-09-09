<?php
/**
 * Encryption and hashing Tools
 * This script contains an helper to encrypt and derypt strings
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

/**
 * Encryption Tools
 * Encrypt and decrypt string namely for security concerns
 */
class Encryption {
	
	/**
	 * Encryption String
	 * 
	 * @var string
	 */
	const ENCRYPT_KEY = 'apine_framework';
	
	/**
	 * Encrypt a string against the encryption string
	 * 
	 * @param string $origin_string
	 * @return string
	 */
	public static function encrypt ($origin_string) {
		
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, self::ENCRYPT_KEY, utf8_encode($origin_string), MCRYPT_MODE_ECB, $iv);
		
		return $encrypted_string;
		
	}
	
	/**
	 * Decrypt a string from the encryption string
	 * 
	 * @param string $encrypted_string
	 * @return string
	 */
	public static function decrypt ($encrypted_string) {
		
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, self::ENCRYPT_KEY, $encrypted_string, MCRYPT_MODE_ECB, $iv);
		
		return $decrypted_string;
		
	}
	
	/**
	 * Cipher a user password
	 * 
	 * @param string $clear_password
	 * @param string $username
	 * @return string
	 */
	public static function hash_password ($clear_password) {
		
		$encrypt_password = self::encrypt($clear_password);
		$password = $clear_password . $encrypt_password;
		$password = self::encrypt($password);
		$ciphered_password = hash('sha256', $password);

		return $ciphered_password;
		
	}
	
	/**
	 * Cipher an application access key
	 */
	public static function hash_app_key () {
		
		// Magic Here
		
	}
	
}