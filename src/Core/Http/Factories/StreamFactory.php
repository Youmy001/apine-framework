<?php
/**
 * StreamFactory
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Http\Factories;

use Apine\Core\Http\Stream;
use Psr\Http\Message\StreamInterface;

/**
 * Class StreamFactory
 *
 * @package Apine\Core\Http\Factories
 */
class StreamFactory
{
    /**
     * Create a new stream from a string.
     *
     * The stream SHOULD be created with a temporary resource.
     *
     * @param string $content
     *
     * @return StreamInterface
     */
    public function createStream(string $content = '') : StreamInterface
    {
        $resource = fopen('php://temp', 'r+');
        fwrite($resource, $content);
        
        return new Stream($resource);
    }
    
    /**
     * Create a stream from an existing file.
     *
     * The file MUST be opened using the given mode, which may be any mode
     * supported by the `fopen` function.
     *
     * The `$filename` MAY be any string supported by `fopen()`.
     *
     * @param string $filename
     * @param string $mode
     *
     * @return StreamInterface
     */
    public function createStreamFromFile($filename, $mode = 'r') : StreamInterface
    {
        if (!file_exists($filename)) {
            throw new \InvalidArgumentException('File not found');
        }
        
        $resource = fopen($filename, $mode);
        
        return new Stream($resource);
    }
    
    /**
     * Create a new stream from an existing resource.
     *
     * The stream MUST be readable and may be writable.
     *
     * @param resource $resource
     *
     * @return StreamInterface
     */
    public function createStreamFromResource($resource) : StreamInterface
    {
        return new Stream($resource);
    }
}