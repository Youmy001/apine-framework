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

use const E_USER_WARNING;
use function openssl_decrypt, openssl_encrypt, openssl_random_pseudo_bytes;
use function base64_encode, hash, substr, trigger_error;

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
     * @var string
     */
    private $key;
    
    /**
     * Encryption constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        try {
            $config = new Config('config/encryption.json');
    
            if (isset($config->key)) {
                $this->key = $config->key;
            } else {
                $newKey = self::generateKey();
        
                $config->key = $newKey;
                $config->save();
        
                $this->key = $newKey;
            }
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('Impossible to load the encryption config');
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Encrypt a string against the encryption string
     *
     * @param string $source
     *
     * @return string
     */
    public function encrypt(string $source) : string
    {
        $key = $this->key;
    
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
    public function decrypt(string $source) : string
    {
        $key = $this->key;
    
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
    
    public function generateKey() : string
    {
        $strong = false;
        $bytes = openssl_random_pseudo_bytes(256,$strong);
        
        if (false === $strong) {
            trigger_error('The random bytes were not generated with a cryptographically secure algorithm safe for usage with passwords.', E_USER_WARNING);
        }
        
        return hash('sha256', $bytes);
    }
    
    /**
     * Cipher a user password
     *
     * @param string $clear_password
     *
     * @return string
     */
    public function hashPassword(string $clear_password) : string
    {
        $encrypt_password = $this->encrypt($clear_password);
        $password = $clear_password . $encrypt_password;
        $password = $this->encrypt($password);
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
    public function hashUserToken(string $a_username, string $a_clear_password, string $a_date) : string
    {
        $encrypt_pass = $this->encrypt($a_clear_password);
        $encrypt_user = $this->encrypt($a_username . $encrypt_pass . $a_date);
        $token = $this->encrypt($encrypt_pass . $encrypt_user);
        $cipher_token = hash('sha256', base64_encode($token));
        
        return $cipher_token;
    }
    
    /**
     * Generate a unique token
     *
     * @return string
     */
    public function token() : string
    {
        return $this->generateKey();
    }
    
    /**
     * Generate a md5 hash for string
     *
     * @param string $string
     *
     * @return string
     */
    public function md5($string) : string
    {
        return hash('md5', $string);
    }
}
