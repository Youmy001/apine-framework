<?php
/**
 * Message
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */

declare(strict_types=1);

namespace Apine\Core\Http;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

abstract class Message implements MessageInterface
{
    /**
     * @var array
     */
    protected $headers = [];
    
    /**
     * @var string
     */
    protected $protocol = '1.1';
    
    /**
     * @var StreamInterface
     */
    protected $body;
    
    /**
     * Retrieves the HTTP protocol version as a string.
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion()
    {
        return $this->protocol;
    }
    
    /**
     * Return an instance with the specified HTTP protocol version.
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new protocol version.
     *
     * @param string $version HTTP protocol version
     *
     * @return static
     */
    public function withProtocolVersion($version)
    {
        $newMessage = clone $this;
        $newMessage->protocol = $version;
        return $newMessage;
    }
    
    /**
     * Retrieves all message header values.
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return string[][] Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings
     *     for that header.
     */
    public function getHeaders()
    {
        $headers = array();
        
        foreach ($this->headers as $header) {
            $headers[$header['name']] = $header['value'];
        }
        
        return $headers;
    }
    
    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     *
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name)
    {
        return isset($this->headers[strtolower($name)]);
    }
    
    /**
     * Retrieves a message header value by the given case-insensitive name.
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name Case-insensitive header field name.
     *
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array.
     */
    public function getHeader($name)
    {
        if (!isset($this->headers[strtolower($name)])) {
            return [];
        }
        
        return $this->headers[strtolower($name)]['value'];
    }
    
    /**
     * Retrieves a comma-separated string of the values for a single header.
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name Case-insensitive header field name.
     *
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     */
    public function getHeaderLine($name)
    {
        $value = $this->getHeader($name);
        
        if (is_array($value)) {
            return implode(', ', $this->getHeader($name));
        } else {
            return $value;
        }
    }
    
    /**
     * Return an instance with the provided value replacing the specified header.
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param string          $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     *
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value)
    {
        $newMessage = clone $this;
    
        /*if (!is_array($value)) {
            $value = [$value];
        }*/
        
        $newMessage->headers[strtolower($name)] = [
            'name' => $name,
            'value' => $value
        ];
        return $newMessage;
    }
    
    /**
     * Return an instance with the specified header appended with the given value.
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param string          $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     *
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withAddedHeader($name, $value)
    {
        $newMessage = clone $this;
        $sanitizedName = strtolower($name);
    
        if (isset($newMessage->headers[$sanitizedName])) {
            if (is_string($newMessage->headers[$sanitizedName]['value'])) {
                $old_value = $newMessage->headers[$sanitizedName]['value'];
                $newMessage->headers[$sanitizedName]['value'] = [$old_value];
            }
        
            //array_push($newMessage->headers[$sanitizedName]['value'], $value);
            if (!is_array($value)) {
                $value = [$value];
            }
    
            $newMessage->headers[$sanitizedName]['value'] = array_merge(
                $newMessage->headers[$sanitizedName]['value'],
                $value
            );
        } else {
            $newMessage->headers[strtolower($name)] = [
                'name'  => $name,
                'value' => $value
            ];
        }
    
        return $newMessage;
    }
    
    /**
     * Return an instance without the specified header.
     * Header resolution MUST be done without case-sensitivity.
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     *
     * @return static
     */
    public function withoutHeader($name)
    {
        $newMessage = clone $this;
        
        if (isset($newMessage->headers[strtolower($name)])) {
            unset($newMessage->headers[strtolower($name)]);
        }
        
        return $newMessage;
    }
    
    /**
     * Gets the body of the message.
     *
     * @return StreamInterface Returns the body as a stream.
     */
    public function getBody()
    {
        if (!$this->body) {
            $stream = fopen('php://memory', 'r+');
            $this->body = new Stream($stream);
        }
        
        return $this->body;
    }
    
    /**
     * Return an instance with the specified message body.
     * The body MUST be a StreamInterface object.
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     *
     * @return static
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body)
    {
        $newMessage = clone $this;
        $newMessage->body = $body;
        return $newMessage;
    }
    
    protected function setBody($body = null)
    {
        $stream = null;
        
        switch (gettype($body)) {
            case 'string':
            case 'integer':
            case 'double':
            case 'boolean':
                $resource = fopen('php://memory', 'r+');
                if ($body !== '') {
                    fwrite($resource, $body);
                    fseek($resource, 0);
                }
                $stream = new Stream($resource);
                break;
            case 'resource':
                $stream = new Stream($body);
                break;
            case 'object':
                if ($body instanceof StreamInterface) {
                    $stream = $body;
                }
                break;
            case 'NULL':
                $stream = new Stream(fopen('php://memory', 'r+'));
        }
        
        if (is_null($stream)) {
            throw new \InvalidArgumentException('Invalid resource type: ' . gettype($body));
        }
        
        $this->body = $stream;
    }
    
    protected function setHeaders(array $headers)
    {
        $this->headers = [];
        foreach ($headers as $header => $value) {
            if (!is_array($value)) {
                $value = [$value];
            }
            
            $sanitizedName = strtolower($header);
            
            if (isset($this->headers[$sanitizedName])) {
                $this->headers[$sanitizedName]['value'] = array_merge(
                    $this->headers[$sanitizedName]['value'],
                    $value
                );
            } else {
                $this->headers[$sanitizedName] = [
                    'name' => $header,
                    'value' => $value
                ];
            }
        }
    }
}