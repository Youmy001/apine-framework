<?php
/**
 * Request
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */

namespace Apine\Core\Http;


use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request extends Message implements ServerRequestInterface
{
    /**
     * @var string
     */
    protected $method;
    
    /**
     * @var string
     */
    protected $requestTarget;
    
    /**
     * @var Uri
     */
    protected $uri;
    
    protected $attributes = [];
    
    protected $cookieParams = [];
    
    protected $parsedBody;
    
    protected $queryParams = [];
    
    protected $serverParams = [];
    
    protected $uploadedFiles = [];
    
    /**
     * @var string
     */
    private $requestAction;
    
    /**
     * @var integer
     */
    private $requestType;
    
    /**
     * Request constructor.
     *
     * @param string        $method
     * @param string|Uri    $uri
     * @param array         $headers
     * @param null          $body
     * @param string        $protocol
     */
    public function __construct (
        string  $method,
        $uri,
        array   $headers = [],
        $body = null,
        string  $protocol = '1.1',
        array $serverParams = []
    )
    {
        if (!($uri instanceof UriInterface)) {
            $uri = new Uri($uri);
        }
        
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->setHeaders($headers);
        $this->protocol = $protocol;
        $this->serverParams = $serverParams;
        $this->setBody($body);
        $this->parsedBody = $this->parseBody();
        
        if (!$this->hasHeader('Host')) {
            $this->updateHostFromUri();
        }
        
        $this->updateHeadersFromUri();
        $this->updateQueryParamsFromUri();
    }
    
    public static function createFromGlobals () : ServerRequestInterface
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $headers = getallheaders();
        $uri = self::getUriFromGlobals();
        $body = file_get_contents('php://input');
        $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']) : '1.1';
        
        $gets = $_GET;
    
        if (isset($gets['apine-request'])) {
            $requestString = $gets['apine-request'];
            $requestArray = explode("/", $requestString);
        
            if ($requestArray[1] === 'api') {
                $type = APINE_REQUEST_MACHINE;
            } else {
                $type = APINE_REQUEST_USER;
            }
        
            unset($gets['apine-request']);
        }
        
        $request = (new static($method, $uri, $headers, $body, $protocol, $_SERVER))
            ->withCookieParams($_COOKIE)
            ->withQueryParams($gets)
            ->withParsedBody($_POST)
            ->withUploadedFiles(self::formatFiles($_FILES));
        
        $request->requestType = $type;
        
        return $request;
    }
    
    /**
     * Get a Uri populated with values from $_SERVER.
     *
     * @return UriInterface
     */
    public static function getUriFromGlobals() : UriInterface
    {
        $uri_string = (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : '';
        $hasQuery = false;
        
        
        if (isset($_SERVER['HTTP_HOST'])) {
            $uri_string .= $_SERVER['HTTP_HOST'];
        } else {
            if (isset($_SERVER['SERVER_NAME'])) {
                $uri_string .= $_SERVER['SERVER_NAME'];
            } else if (isset($_SERVER['SERVER_ADDR'])) {
                $uri_string .= $_SERVER['SERVER_ADDR'];
            }
            
            if (isset($_SERVER['SERVER_PORT'])) {
                $uri_string .= ':' . $_SERVER['SERVER_PORT'];
            }
        }
        
        if (isset($_SERVER['REQUEST_URI'])) {
            $requestParts = explode('?', $_SERVER['REQUEST_URI'],2);
            
            //$uri_string = implode('/', [$uri_string, $requestParts[0]]);
            
            if ($requestParts[0] !== "/") {
                //$uri_string = implode('/', [$uri_string, $requestParts[0]]);
                $uri_string .= $requestParts[0];
            } else {
                $uri_string = implode('', [$uri_string, $requestParts[0]]);
            }
            
            if (isset($requestParts[1])) {
                $hasQuery = true;
                $uri_string = implode('?', [$uri_string, $requestParts[1]]);
            }
        }
        
        if (!$hasQuery && isset($_SERVER['QUERY_STRING'])) {
            $uri_string = implode('?', [$uri_string, $_SERVER['QUERY_STRING']]);
        }
        
        return new Uri($uri_string);
    }
    
    /**
     * Retrieves the message's request target.
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget()
    {
        if (is_null($this->requestTarget)) {
            $target = $this->uri->getPath();
            
            if (empty($target)) {
                $target = "/";
            }
            
            if ($this->uri->getQuery() != "") {
                $target.= "?" . $this->uri->getQuery();
            }
            
            $this->requestTarget = $target;
        }
        
        return $this->requestTarget;
    }
    
    /**
     * Return an instance with the specific request-target.
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
     *     request-target forms allowed in request messages)
     *
     * @param mixed $requestTarget
     *
     * @return static
     */
    public function withRequestTarget($requestTarget)
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new \InvalidArgumentException("Invalid target provided. Request targets may not contain whitespaces.");
        }
        
        $newRequest = clone $this;
        $newRequest->requestTarget = $requestTarget;
        return $newRequest;
    }
    
    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
     * Return an instance with the provided HTTP method.
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     *
     * @return static
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method)
    {
        $newRequest = clone $this;
        $newRequest->method = strtoupper($method);
        return $newRequest;
    }
    
    /**
     * Retrieves the URI instance.
     * This method MUST return a UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri()
    {
        return $this->uri;
    }
    
    /**
     * Returns an instance with the provided URI.
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     * - If the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     *
     * @param UriInterface $uri New request URI to use.
     * @param bool         $preserveHost Preserve the original state of the Host header.
     *
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $newRequest = clone $this;
        $newRequest->uri = $uri;
        
        if (!$preserveHost) {
            $newRequest->updateHostFromUri();
        }
        
        return $newRequest;
    }
    
    /**
     * Retrieve server parameters.
     * Retrieves data related to the incoming request environment,
     * typically derived from PHP's $_SERVER superglobal. The data IS NOT
     * REQUIRED to originate from $_SERVER.
     *
     * @return array
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }
    
    /**
     * Retrieve cookies.
     * Retrieves cookies sent by the client to the server.
     * The data MUST be compatible with the structure of the $_COOKIE
     * superglobal.
     *
     * @return array
     */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }
    
    /**
     * Return an instance with the specified cookies.
     * The data IS NOT REQUIRED to come from the $_COOKIE superglobal, but MUST
     * be compatible with the structure of $_COOKIE. Typically, this data will
     * be injected at instantiation.
     * This method MUST NOT update the related Cookie header of the request
     * instance, nor related values in the server params.
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated cookie values.
     *
     * @param array $cookies Array of key/value pairs representing cookies.
     *
     * @return static
     */
    public function withCookieParams(array $cookies)
    {
        $new = clone $this;
        $new->cookieParams = $cookies;
        return $new;
    }
    
    /**
     * Retrieve query string arguments.
     * Retrieves the deserialized query string arguments, if any.
     * Note: the query params might not be in sync with the URI or server
     * params. If you need to ensure you are only getting the original
     * values, you may need to parse the query string from `getUri()->getQuery()`
     * or from the `QUERY_STRING` server param.
     *
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }
    
    /**
     * Return an instance with the specified query string arguments.
     * These values SHOULD remain immutable over the course of the incoming
     * request. They MAY be injected during instantiation, such as from PHP's
     * $_GET superglobal, or MAY be derived from some other value such as the
     * URI. In cases where the arguments are parsed from the URI, the data
     * MUST be compatible with what PHP's parse_str() would return for
     * purposes of how duplicate query parameters are handled, and how nested
     * sets are handled.
     * Setting query string arguments MUST NOT change the URI stored by the
     * request, nor the values in the server params.
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated query string arguments.
     *
     * @param array $query Array of query string arguments, typically from
     *     $_GET.
     *
     * @return static
     */
    public function withQueryParams(array $query)
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }
    
    /**
     * Retrieve normalized file upload data.
     * This method returns upload metadata in a normalized tree, with each leaf
     * an instance of Psr\Http\Message\UploadedFileInterface.
     * These values MAY be prepared from $_FILES or the message body during
     * instantiation, or MAY be injected via withUploadedFiles().
     *
     * @return array An array tree of UploadedFileInterface instances; an empty
     *     array MUST be returned if no data is present.
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }
    
    /**
     * Create a new instance with the specified uploaded files.
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param array $uploadedFiles An array tree of UploadedFileInterface instances.
     *
     * @return static
     * @throws \InvalidArgumentException if an invalid structure is provided.
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;
        return $new;
    }
    
    /**
     * Retrieve any parameters provided in the request body.
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, this method MUST
     * return the contents of $_POST.
     * Otherwise, this method may return any results of deserializing
     * the request body content; as parsing returns structured content, the
     * potential types MUST be arrays or objects only. A null value indicates
     * the absence of body content.
     *
     * @return null|array|object The deserialized body parameters, if any.
     *     These will typically be an array or object.
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }
    
    /**
     * Return an instance with the specified body parameters.
     * These MAY be injected during instantiation.
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, use this method
     * ONLY to inject the contents of $_POST.
     * The data IS NOT REQUIRED to come from $_POST, but MUST be the results of
     * deserializing the request body content. Deserialization/parsing returns
     * structured data, and, as such, this method ONLY accepts arrays or objects,
     * or a null value if nothing was available to parse.
     * As an example, if content negotiation determines that the request data
     * is a JSON payload, this method could be used to create a request
     * instance with the deserialized parameters.
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param null|array|object $data The deserialized body data. This will
     *     typically be in an array or object.
     *
     * @return static
     * @throws \InvalidArgumentException if an unsupported argument type is
     *     provided.
     */
    public function withParsedBody($data)
    {
        $new = clone $this;
        $new->parsedBody = $data;
        return $new;
    }
    
    /**
     * Retrieve attributes derived from the request.
     * The request "attributes" may be used to allow injection of any
     * parameters derived from the request: e.g., the results of path
     * match operations; the results of decrypting cookies; the results of
     * deserializing non-form-encoded message bodies; etc. Attributes
     * will be application and request specific, and CAN be mutable.
     *
     * @return array Attributes derived from the request.
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    /**
     * Retrieve a single derived request attribute.
     * Retrieves a single derived request attribute as described in
     * getAttributes(). If the attribute has not been previously set, returns
     * the default value as provided.
     * This method obviates the need for a hasAttribute() method, as it allows
     * specifying a default value to return if the attribute is not found.
     *
     * @see getAttributes()
     *
     * @param string $name The attribute name.
     * @param mixed  $default Default value to return if the attribute does not exist.
     *
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        if (false === array_key_exists($name, $this->attributes)) {
            return $default;
        }
        
        return $this->attributes[$name];
    }
    
    /**
     * Return an instance with the specified derived request attribute.
     * This method allows setting a single derived request attribute as
     * described in getAttributes().
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated attribute.
     *
     * @see getAttributes()
     *
     * @param string $name The attribute name.
     * @param mixed  $value The value of the attribute.
     *
     * @return static
     */
    public function withAttribute($name, $value)
    {
        $new = clone $this;
        $new->attributes[$name] = $value;
        return $new;
    }
    
    /**
     * Return an instance that removes the specified derived request attribute.
     * This method allows removing a single derived request attribute as
     * described in getAttributes().
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the attribute.
     *
     * @see getAttributes()
     *
     * @param string $name The attribute name.
     *
     * @return static
     */
    public function withoutAttribute($name)
    {
        $new = clone $this;
        unset($new->attributes[$name]);
        return $new;
    }
    
    /**
     * Checks if the request is made through the HTTPS protocol
     *
     * @return boolean
     */
    public function isHttps() : bool
    {
        $headers = $this->getServerParams();
        return ($this->hasHeader('HTTPS'));
    }
    
    /**
     * Checks if the request is made to the API
     *
     * @return boolean
     */
    public function isAPICall() : bool
    {
        return ($this->requestType === APINE_REQUEST_MACHINE);
    }
    
    /**
     * Checks if the request is made from a Javascript script
     *
     * @return boolean
     */
    public function isAjax() : bool
    {
        $headers = $this->getServerParams();
        return ($this->hasHeader('X-Requested-With') && $this->getHeaderLine('X-Requested-With') == 'XMLHttpRequest');
    }
    
    /**
     * Returns whether the current http request is a GET request or not
     *
     * @return boolean
     */
    public function isGet() : bool
    {
        return ($this->getMethod() == "GET");
    }
    
    /**
     * Returns whether the current http request is a POST request or not
     *
     * @return boolean
     */
    public function isPost() : bool
    {
        return ($this->getMethod() == "POST");
    }
    
    /**
     * Returns whether the current http request is a PUT request or not
     *
     * @return boolean
     */
    public function isPut() : bool
    {
        return ($this->getMethod() == "PUT");
    }
    
    /**
     * Returns whether the current http request is a DELETE request or not
     *
     * @return boolean
     */
    public function isDelete()
    {
        return ($this->getMethod() == "DELETE");
    }
    
    protected static function formatFiles(array $files) : array
    {
        $normalized = [];
        
        foreach ($files as $key => $value) {
            if (is_array($value) && isset($value['name'])) {
                $normalized[$key] = self::createUploadedFile($value);
            } else if (is_array($value)) {
                $normalized[$key] = self::formatFiles($value);
            } else {
                throw new \InvalidArgumentException('Invalid files specification');
            }
            
        }
        
        return $normalized;
    }
    
    protected static function createUploadedFile (array $value)
    {
        if (is_array($value['name'])) {
            $normalized = [];
            $files = $value['name'];
            
            foreach (array_keys($files['name']) as $key) {
                $values = [
                    'tmp_name' => $files['tmp_name'][$key],
                    'size' => $files['size'][$key],
                    'error' => $files['error'][$key],
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key]
                ];
                
                $normalized[$key] = self::createUploadedFile($values);
            }
            
            return $files;
        } else {
            return new UploadedFile(
                $value['tmp_name'],
                (int)$value['size'],
                (int)$value['error'],
                $value['name'],
                $value['type']
            );
        }
    }
    
    /**
     * @todo Sanitize data
     * @return null|array
     */
    private function parseBody()
    {
        $result = null;
        $body = $this->getBody()->getContents();
        
        if ($this->hasHeader('Content-Type')) {
            if (false !== array_search('application/json', $this->getHeader('Content-Type'))) {
                $result = json_decode($body, true);
            }
            
            if (false !== array_search('application/x-www-form-urlencoded', $this->getHeader('Content-Type'))) {
                parse_str($body, $result);
            }
            
            // TODO Add parsing of xml content
        }
        
        return $result;
    }
    
    private function updateHostFromUri()
    {
        $host = $this->uri->getHost();
        
        if ($host == '') {
            return;
        }
        
        if (
            $this->uri->getPort() !== null &&
            !Uri::isStandardSchemePort($this->uri->getPort(), $this->uri->getScheme())
        ) {
            $host .= ':' . $this->uri->getPort();
        }
    
        if (isset($this->headers['host'])) {
            $this->headers['host'] = [
                "name" => "Host",
                "value" => [$host]
            ];
        } else {
            $headers = [
                "host" => [
                    "name" => "Host",
                    "value" => [$host]
                ]
            ];
            $this->headers = array_merge($headers, $this->headers);
        }
    }
    
    private function updateHeadersFromUri()
    {
        $scheme = $this->uri->getScheme();
        
        if ($scheme === 'https' && !isset($this->headers['https'])) {
            $this->headers['https'] = [
                "name" => "Https",
                "value" => ['HTTPS']
            ];
        }
    }
    
    private function updateQueryParamsFromUri()
    {
        if (null !== ($query = $this->getUri()->getQuery())) {
            parse_str($this->getUri()->getQuery(), $this->queryParams);
        }
    }
}