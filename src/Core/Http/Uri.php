<?php
/**
 * Uri
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */

namespace Apine\Core\Http;


use Psr\Http\Message\UriInterface;

/**
 * Implementation of PSR-7 URI
 *
 * @package Apine\Core\HTTP
 */
class Uri implements UriInterface
{
    
    private $scheme;
    
    private $userInfo;
    
    private $username;
    
    private $password;
    
    private $host;
    
    private $port;
    
    private $path;
    
    private $query;
    
    private $fragment;
    
    private static $defaultPorts = [
        'http'   => 80,
        'https'  => 443,
        'ftp'    => 21,
        'ftps'   => 22,
        'gopher' => 70,
        'nntp'   => 119,
        'news'   => 119,
        'telnet' => 23,
        'tn3270' => 23,
        'imap'   => 143,
        'pop'    => 110,
        'ldap'   => 389,
        'smtp'   => 465
    ];
    
    public function __construct(string $uri = '')
    {
        $uriParts = parse_url($uri);
        
        if ($uriParts === false) {
            throw new \InvalidArgumentException('Unable to parse URI : ' . $uri);
        }
        
        $this->applyUriParts($uriParts);
    }
    
    /**
     * Retrieve the scheme component of the URI.
     * If no scheme is present, this method MUST return an empty string.
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.1.
     * The trailing ":" character is not part of the scheme and MUST NOT be
     * added.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * @return string The URI scheme.
     */
    public function getScheme()
    {
        return $this->scheme;
    }
    
    /**
     * Retrieve the authority component of the URI.
     * If no authority information is present, this method MUST return an empty
     * string.
     * The authority syntax of the URI is:
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority()
    {
        $authority = $this->host;
        
        if ('' !== ($userInfo = $this->getUserInfo())) {
            $authority = $userInfo . '@' . $authority;
        }
        
        if ($this->port && !self::isStandardSchemePort($this->port, $this->scheme)) {
            $authority .= ':' . $this->port;
        }
        
        return $authority;
    }
    
    /**
     * Retrieve the user information component of the URI.
     * If no user information is present, this method MUST return an empty
     * string.
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     * The trailing "@" character is not part of the user information and MUST
     * NOT be added.
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo()
    {
        $userInfo = '';
        
        if ($this->username) {
            $userInfo .= $this->username;
            
            if ($this->password) {
                $userInfo .= ':' . $this->password;
            }
        }
        
        return $userInfo;
    }
    
    /**
     * Retrieve the host component of the URI.
     * If no host is present, this method MUST return an empty string.
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.2.2.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string The URI host.
     */
    public function getHost()
    {
        return $this->host;
    }
    
    /**
     * Retrieve the port component of the URI.
     * If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer. If the port is the standard port
     * used with the current scheme, this method SHOULD return null.
     * If no port is present, and no scheme is present, this method MUST return
     * a null value.
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return null|int The URI port.
     */
    public function getPort()
    {
        return $this->port;
        
    }
    
    /**
     * Retrieve the path component of the URI.
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     * Normally, the empty path "" and absolute path "/" are considered equal as
     * defined in RFC 7230 Section 2.7.3. But this method MUST NOT automatically
     * do this normalization because in contexts with a trimmed base path, e.g.
     * the front controller, this difference becomes significant. It's the task
     * of the user to handle both "" and "/".
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.3.
     * As an example, if the value should include a slash ("/") not intended as
     * delimiter between path segments, that value MUST be passed in encoded
     * form (e.g., "%2F") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     * @return string The URI path.
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * Retrieve the query string of the URI.
     * If no query string is present, this method MUST return an empty string.
     * The leading "?" character is not part of the query and MUST NOT be
     * added.
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.4.
     * As an example, if a value in a key/value pair of the query string should
     * include an ampersand ("&") not intended as a delimiter between values,
     * that value MUST be passed in encoded form (e.g., "%26") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @return string The URI query string.
     */
    public function getQuery()
    {
        return $this->query;
    }
    
    /**
     * Retrieve the fragment component of the URI.
     * If no fragment is present, this method MUST return an empty string.
     * The leading "#" character is not part of the fragment and MUST NOT be
     * added.
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.5.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     * @return string The URI fragment.
     */
    public function getFragment()
    {
        return $this->fragment;
    }
    
    /**
     * Return an instance with the specified scheme.
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified scheme.
     * Implementations MUST support the schemes "http" and "https" case
     * insensitively, and MAY accommodate other schemes if required.
     * An empty scheme is equivalent to removing the scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     *
     * @return static A new instance with the specified scheme.
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme)
    {
        if (!is_string($scheme)) {
            throw new \InvalidArgumentException('Uri scheme must be a string');
        }
        
        $scheme = str_replace('://', '', strtolower($scheme));
        
        if ($scheme != '' && !isset(self::$defaultPorts[$scheme])) {
            throw new \InvalidArgumentException('Invalid or unsupported scheme ' . $scheme);
        }
        
        if ($scheme === $this->scheme) {
            return $this;
        }
        
        $newUri = clone $this;
        $newUri->scheme = $scheme;
        
        return $newUri;
    }
    
    /**
     * Return an instance with the specified user information.
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified user information.
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string      $user The user name to use for authority.
     * @param null|string $password The password associated with $user.
     *
     * @return static A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        $newUri = clone $this;
        $newUri->username = $user;
        $newUri->password = $password;
        
        return $newUri;
    }
    
    /**
     * Return an instance with the specified host.
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified host.
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host The hostname to use with the new instance.
     *
     * @return static A new instance with the specified host.
     * @throws \InvalidArgumentException for invalid hostnames.
     */
    public function withHost($host)
    {
        if (!is_string($host) || empty($host)) {
            $host = '';
        } else if ($host !== '' && !$this->validateHost($host)) {
            throw new \InvalidArgumentException(sprintf("Invalid Hostname %s",$host));
        }
        
        if ($host === $this->host) {
            return $this;
        }
        
        $newUri = clone $this;
        $newUri->host = $host;
        
        return $newUri;
    }
    
    /**
     * Return an instance with the specified port.
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified port.
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param null|int $port The port to use with the new instance; a null value
     *     removes the port information.
     *
     * @return static A new instance with the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort($port = null)
    {
        if (!$this->validatePort($port)) {
            throw new \InvalidArgumentException(sprintf('Invalid Port Number %s', $port));
        }
        
        if (is_null($port) && !empty($this->scheme)) {
            $port = self::$defaultPorts[$this->scheme];
        }
        
        if ($port === $this->port) {
            return $this;
        }
        
        $newUri = clone $this;
        $newUri->port = $port;
        
        return $newUri;
    }
    
    /**
     * Return an instance with the specified path.
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified path.
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     * If the path is intended to be domain-relative rather than path relative then
     * it must begin with a slash ("/"). Paths not starting with a slash ("/")
     * are assumed to be relative to some base path known to the application or
     * consumer.
     * Users can provide both encoded and decoded path characters.
     * Implementations ensure the correct encoding as outlined in getPath().
     *
     * @param string $path The path to use with the new instance.
     *
     * @return static A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path)
    {
        if ($path === null) {
            $path = '';
        } else {
            $path = $this->sanitizeUriString($path);
            
            if (!empty($this->host) && $path[0] !== '/') {
                throw new \InvalidArgumentException(sprintf('Invalid Path %s', $path));
            }
        }
        
        if ($this->path === $path) {
            return $this;
        }
        
        $newUri = clone $this;
        $newUri->path = $path;
        
        return $newUri;
    }
    
    /**
     * Return an instance with the specified query string.
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified query string.
     * Users can provide both encoded and decoded query characters.
     * Implementations ensure the correct encoding as outlined in getQuery().
     * An empty query string value is equivalent to removing the query string.
     *
     * @param string $query The query string to use with the new instance.
     *
     * @return static A new instance with the specified query string.
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query)
    {
        if ($query === null) {
            $query = '';
        } else {
            $query = $this->sanitizeUriString($query);
        }
        
        if ($this->query === $query) {
            return $this;
        }
        
        $newUri = clone $this;
        $newUri->query = $query;
        
        return $newUri;
    }
    
    /**
     * Return an instance with the specified URI fragment.
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified URI fragment.
     * Users can provide both encoded and decoded fragment characters.
     * Implementations ensure the correct encoding as outlined in getFragment().
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     *
     * @return static A new instance with the specified fragment.
     */
    public function withFragment($fragment)
    {
        if ($fragment === null) {
            $fragment = '';
        } else {
            $fragment = $this->sanitizeUriString($fragment);
        }
        
        if ($this->fragment === $fragment) {
            return $this;
        }
        
        $newUri = clone $this;
        $newUri->fragment = $fragment;
        
        return $newUri;
    }
    
    /**
     * Return the string representation as a URI reference.
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function __toString()
    {
        return $this->assembleUri(
            $this->scheme,
            $this->getAuthority(),
            $this->path,
            $this->query,
            $this->fragment
        );
    }
    
    private function applyUriParts(array $parts)
    {
        $this->scheme = isset($parts['scheme']) ? $parts['scheme'] : null;
        $this->host = (isset($parts['host']) && $this->validateHost($parts['host'])) ? $parts['host'] : null;
        $this->username = isset($parts['user']) ? $parts['user'] : null;
        $this->password = isset($parts['pass']) ? $parts['pass'] : null;
        
        $this->port = (isset($parts['port']) && $this->validatePort($parts['port'])) ? (int)$parts['port'] : null;
        
        if (is_null($this->port) && !empty($this->scheme)) {
            $this->port = self::$defaultPorts[$this->scheme];
        }
        
        $this->path = isset($parts['path']) ? $this->sanitizeUriString($parts['path']) : null;
        $this->query = isset($parts['query']) ? $this->sanitizeUriString($parts['query']) : null;
        $this->fragment = isset($parts['fragment']) ? $this->sanitizeUriString($parts['fragment']) : null;
    }
    
    private function assembleUri($scheme, $authority, $path, $query, $fragment): string
    {
        $uri = '';
        
        if (!empty($scheme)) {
            $uri = $scheme . ':';
        }
        
        if (!empty($authority) || $scheme === 'file') {
            $uri .= '//' . $authority;
        }
        
        $uri .= $path;
        
        if (!empty($query)) {
            $uri .= '?' . $query;
        }
        
        if (!empty($fragment)) {
            $uri .= '#' . $fragment;
        }
        
        return $uri;
    }
    
    /**
     * Validates the format of a host name
     *
     * A host name can be either an IP address or a domain name. It
     * is a string of no more than 255 bytes (ASCII characters) divided in domain labels
     * of no more than 63 bytes separated by by ".". Parts may start with either a digit of a letter.
     * The only characters allowed in the labels are alphanumerics plus "-".
     *
     * The syntax allows percent-encoded octets in order to represent non-ASCII characters.
     *
     * @param string $host
     *
     * @return bool
     * @see https://tools.ietf.org/html/rfc1123
     * @see https://tools.ietf.org/html/rfc3986#section-3.2.2
     */
    private function validateHost(string $host): bool
    {
        //$regex = '/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$|^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]{1,63})\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]{1,63}[A-Za-z0-9])$/';
        
        $regex = '/^(([a-zA-Z0-9\%]|[a-zA-Z0-9\%][a-zA-Z0-9\-\%]{1,63})\.)*([A-Za-z0-9\%]|[A-Za-z0-9\%][A-Za-z0-9\-\%]{1,63}[A-Za-z0-9\%])$/';
        
        return (preg_match($regex, rawurlencode($host)) === 1) && strlen(rawurlencode($host)) <= 255;
    }
    
    /**
     * Validate the port number
     * Returns false if port number is below 1 and over 65535
     *
     * @param int $port
     *
     * @return bool
     */
    private function validatePort(int $port): bool
    {
        return !($port < 1 || $port > 65535);
    }
    
    /**
     * Verify the port number is standard for the scheme
     *
     * @param int    $port
     * @param string $scheme
     *
     * @return bool
     */
    public static function isStandardSchemePort(int $port, string $scheme): bool
    {
        return (!empty($scheme) && self::$defaultPorts[$scheme] === $port);
    }
    
    private function sanitizeUriString(string $string): string
    {
        return preg_replace_callback(
            "/(?:[^a-zA-Z0-9_\-\.~!\$&\\'\(\)\*\+,;=%:@\/]++|%(?![A-Fa-f0-9]{2}))/",
            function ($match) {
                return rawurlencode($match[0]);
            },
            $string
        );
    }
}