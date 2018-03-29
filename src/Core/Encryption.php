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

use Apine\Core\Json\JsonStore;
use Apine\Core\Json\JsonStoreFileNotFoundException;

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
     * @var Encryption
     */
    private static $instance;
    
    /**
     * @var string
     */
    private $key;
    
    /**
     * Encryption constructor.
     *
     * @throws \Exception
     */
    private function __construct()
    {
        try {
            $json = JsonStore::get('config/encryption.json');
            $this->key = $json->key;
        } catch (JsonStoreFileNotFoundException $e) {
            $newKey = self::generateKey();
            $array = [];
            $array['key'] = $newKey;
    
            JsonStore::set('config/encryption.json', $array);
    
            $this->key = $newKey;
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('Impossible to load the encryption config');
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * @return Encryption
     * @throws \Exception
     */
    public static function getInstance() : self
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
    
        return self::$instance;
    }
    
    /**
     * Encrypt a string against the encryption string
     *
     * @param string $source
     *
     * @return string
     */
    public static function encrypt(string $source) : string
    {
        $key = self::getInstance()->key;
    
        $iv = substr($key, 0, 16);
        $encrypted = openssl_encrypt(
            $source,
            'AES128',
            $key,
            0,
            $iv
        );
        
        return $encrypted;
    }
    
    /**
     * Decrypt a string from the encryption string
     *
     * @param string $source
     *
     * @return string
     */
    public static function decrypt(string $source) : string
    {
        $key = self::getInstance()->key;
    
        $iv = substr($key, 0, 16);
        $decrypted = openssl_decrypt(
            $source,
            'AES128',
            $key,
            0,
            $iv
        );
        
        return $decrypted;
    }
    
    /*private static function generateKey()
    {
        $config = Application::getInstance()->getConfig();
        $hash = hash('md5', rand(1, 100000) . '_' . rand(100001, 200000));
        
        $config->encryption = [
            'key' => $hash,
            'method' => 'ssl'
        ];
    }*/
    
    private static function generateKey() : string
    {
        return hash('md5', rand(1, 100000) . '_' . rand(100001, 200000));
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
