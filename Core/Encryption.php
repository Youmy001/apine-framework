<?php
/**
 * Encryption and hashing Tools
 * This script contains an helper to encrypt and derypt strings
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Core;

use Apine\Application\ApplicationConfig as ApplicationConfig;

require_once __DIR__ . '/pseudo_crypt.php';

/**
 * Encryption Tools
 * 
 * Encrypt and decrypt string namely for security concerns
 */
final class Encryption {
	
	/**
	 * Encrypt a string against the encryption string
	 * 
	 * @param string $origin_string
	 * @return string
	 */
	public static function encrypt ($origin_string) {
		
		if (!ApplicationConfig::get('runtime', 'encryption_key')) {
			self::generate_key();
		}
		
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, ApplicationConfig::get('runtime', 'encryption_key'), utf8_encode($origin_string), MCRYPT_MODE_ECB, $iv);
		
		return $encrypted_string;
		
	}
	
	/**
	 * Decrypt a string from the encryption string
	 * 
	 * @param string $encrypted_string
	 * @return string
	 */
	public static function decrypt ($encrypted_string) {
		
		if (!ApplicationConfig::get('runtime', 'encryption_key')) {
			self::generate_key();
		}
		
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, ApplicationConfig::get('runtime', 'encryption_key'), $encrypted_string, MCRYPT_MODE_ECB, $iv);
		
		return $decrypted_string;
		
	}
	
	private static function generate_key() {
        
		$hash = \PseudoCrypt::hash(intval(rand(1, 1000)), 10);
		ApplicationConfig::set('runtime', 'encryption_key', $hash);
		
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
	 * Cipher an api user token
	 * 
	 * @param string $a_username
	 * @param string $a_clear_password
	 * @param string $a_date
	 * @return string
	 */
	public static function hash_api_user_token ($a_username, $a_clear_password, $a_date) {
		
		$encrypt_pass = self::encrypt($a_clear_password);
		$encrypt_user = self::encrypt($a_username.$encrypt_pass.$a_date);
		$token = self::encrypt($encrypt_pass.$encrypt_user);
		$cipher_token = hash('sha256', base64_encode($token));
		
		return $cipher_token;
		
	}
	
	/**
	 * Generate a unique token
	 * 
	 * @return string
	 */
	public static function token () {
		
		$time = microtime(true);
		$micro = sprintf("%06d", ($time - floor($time)) * 1000000);
		$date = new \DateTime(date('Y-m-d H:i:s.' . $micro, $time));
		$milliseconds = $date->format("u");
		
		$cipher_string = hash('sha256', $milliseconds);
		
		return $cipher_string;
		
	}
	
	/**
	 * Generate a md5 hash for string
	 *
	 * @param string $string
	 */
	public static function md5 ($string) {
	
		return hash('md5', $string);
	
	}
	
}
