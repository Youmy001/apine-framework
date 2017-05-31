<?php
/**
 * Encryption and hashing Tools
 * This script contains an helper to encrypt and derypt strings
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core;

use Apine\Application\Application;

/**
 * Encryption Tools
 * Encrypt and decrypt string namely for security concerns
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Core
 */
final class Encryption
{
    /**
     * Encrypt a string against the encryption string
     *
     * @param string $origin_string
     *
     * @return string
     */
    public static function encrypt(string $origin_string) : string
    {
        $config = Application::getInstance()->getConfig();
        
        if (!$config->encryption->key) {
            self::generateKey();
        }
        
        if ($config->encryption->method !== 'ssl') {
            $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $config->encryption->key,
                utf8_encode($origin_string), MCRYPT_MODE_ECB, $iv);
        } else {
            $iv = substr($config->encryption->key, 0, 16);
            $encrypted_string = openssl_encrypt($origin_string, 'AES128',
                $config->encryption->key, 0, $iv);
        }
        
        return $encrypted_string;
    }
    
    /**
     * Decrypt a string from the encryption string
     *
     * @param string $encrypted_string
     *
     * @return string
     */
    public static function decrypt(string $encrypted_string) : string
    {
        $config = Application::getInstance()->getConfig();
        
        if (!$config->encryption->key) {
            self::generateKey();
        }
        
        if ($config->encryption->method !== 'ssl') {
            $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $config->encryption->key,
                $encrypted_string, MCRYPT_MODE_ECB, $iv);
        } else {
            $iv = substr($config->encryption->key, 0, 16);
            $decrypted_string = openssl_decrypt($encrypted_string, 'AES128',
                $config->encryption->key, 0, $iv);
        }
        
        return $decrypted_string;
        
    }
    
    private static function generateKey()
    {
        $config = Application::getInstance()->getConfig();
        $hash = hash('md5', rand(1, 100000) . '_' . rand(100001, 200000));
        
        $config->encryption = [
            'key' => $hash,
            'method' => 'ssl'
        ];
    }
    
    /**
     * Cipher a user password
     *
     * @param string $clear_password
     *
     * @return string
     */
    public static function hashPassword(string $clear_password) : string
    {
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
     *
     * @return string
     */
    public static function hashUserToken(string $a_username, string $a_clear_password, string $a_date) : string
    {
        $encrypt_pass = self::encrypt($a_clear_password);
        $encrypt_user = self::encrypt($a_username . $encrypt_pass . $a_date);
        $token = self::encrypt($encrypt_pass . $encrypt_user);
        $cipher_token = hash('sha256', base64_encode($token));
        
        return $cipher_token;
    }
    
    /**
     * Generate a unique token
     *
     * @return string
     */
    public static function token() : string
    {
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
     *
     * @return string
     */
    public static function md5($string) : string
    {
        return hash('md5', $string);
    }
}
