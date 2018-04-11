<?php
/**
 * Internal URL Writer
 * This script contains an helper to write internal URL
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */

declare(strict_types=1);

namespace Apine\Core\Views;

use Apine\Core\Http\Factories\UriFactory;
use const Apine\Core\PROTOCOL_HTTP;
use const Apine\Core\PROTOCOL_HTTPS;
use const Apine\Core\PROTOCOL_DEFAULT;
use function strlen, explode, substr, count, ltrim;

/**
 * Internal URL Writer
 * Write URL from server's information
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Views
 */
final class URLHelper
{
    /**
     * Server Domain Name
     *
     * @var string
     */
    private $authority;
    
    /**
     * Main Server's Domain Name
     *
     * @var string
     */
    private $mainAuthority;
    
    /**
     * Path of the current session
     *
     * @var string
     */
    private $path;
    
    /**
     * @var \Psr\Http\Message\UriInterface
     */
    private $uri;
    
    /**
     * Construct the URL Writer helper
     * Extract string from server configuration
     *
     * @param array|null $server Array compatible with $_SERVER
     */
    public function __construct(array $server = null)
    {
        if (null === $server) {
            $server = $_SERVER;
        }
        
        $uri = (new UriFactory())->createUriFromArray($server);
    
        $hostArray = explode('.', $uri->getAuthority());
    
        if (count($hostArray) >= 3) {
            $start = strlen($hostArray[0]) + 1;
            $this->mainAuthority = substr($uri->getAuthority(), $start);
        } else {
            $this->mainAuthority = $uri->getAuthority();
        }
        
        $this->authority = $uri->getAuthority();
        $this->path = $uri->getPath();
        $this->uri = $uri;
    }
    
    /**
     * Select protocol to use
     *
     * @param integer $param
     *
     * @return string
     */
    private function protocol(int $param) : string
    {
        switch ($param) {
            case PROTOCOL_HTTP:
                $protocol = 'http://';
                break;
            case PROTOCOL_HTTPS:
                $protocol = 'https://';
                break;
            case PROTOCOL_DEFAULT:
            default:
                $protocol = $this->uri->getScheme() . '://';
                break;
        }
        
        return $protocol;
    }
    
    /**
     * Append a path to the current absolute path
     *
     * @param string  $base
     *            Base url
     * @param string  $path
     *        String to append
     * @param integer $protocol
     *        Protocol to append to the path
     *
     * @return string
     */
    private function writeUrl(string $base, string $path, int $protocol) : string
    {
        /*if (isset(Request::get()['language'])) {
            if (Request::get()['language'] == Translator::language()->code || Request::get()['language'] == Translator::language()->code_short) {
                $language = Request::get()['language'];
            } else {
                $language = Translator::language()->code_short;
            }
            
            return self::protocol($protocol) . $base . '/' . $language . '/' . $path;
        } else {*/
            return $this->protocol($protocol) . $base . '/' . ltrim($path, '/');
        //}
    }
    
    public function resource(string $path) : string
    {
        return $this->protocol(PROTOCOL_DEFAULT) . $this->authority . '/' . $path;
    }
    
    /**
     * Retrieve the http path to a ressource relative to site's root
     *
     * @param string  $path
     *        String to append
     * @param integer $protocol
     *        Protocol to append to the path
     *
     * @return string
     */
    public function path(string $path, int $protocol = PROTOCOL_DEFAULT) : string
    {
        return $this->writeUrl($this->authority, $path, $protocol);
    }
    
    /**
     * Retrieve the http path to a resource relative to site's main
     * domains's root
     *
     * @param string  $path
     *        String to append
     * @param integer $protocol
     *        Protocol to append to the path
     *
     * @return string
     */
    public function mainPath(string $path, int $protocol = PROTOCOL_DEFAULT) : string
    {
        return $this->writeUrl($this->mainAuthority, $path, $protocol);
    }
    
    /**
     * Retrieve the http path to a resource relative to current
     * resource
     *
     * @param string  $path
     *        String to append
     * @param integer $protocol
     *        Protocol to append to the path
     *
     * @return string
     */
    public function relativePath(string $path, int $protocol = PROTOCOL_DEFAULT) : string
    {
        return $this->writeUrl($this->authority, $this->path . '/' . $path,
            $protocol);
    }
    
    /**
     * Get current current http path
     *
     * @param integer $protocol
     *
     * @return string
     */
    public function getCurrentPath(int $protocol = PROTOCOL_DEFAULT) : string
    {
        return self::writeUrl($this->authority, $this->path, $protocol);
    }
}
