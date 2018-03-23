<?php
/**
 * Stream
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */

declare(strict_types=1);

namespace Apine\Core\Http;

use Psr\Http\Message\StreamInterface;

/**
 * Implementation of PSR-7 Stream
 *
 * @package Apine\Core\HTTP
 * @author Tommy Teasdale
 */
class Stream implements StreamInterface
{
    
    /**
     * @var resource
     */
    private $stream;
    
    /**
     * @var integer
     */
    private $size;
    
    /**
     * @var array
     */
    private $meta;
    
    /**
     * @var bool
     */
    //private $seekable = false;
    
    /**
     * @var bool
     */
    //private $readable = false;
    
    /**
     * @var bool
     */
    //private $writable = false;
    
    /**
     * Readable flags of files and streams.
     *
     * @see http://php.net/manual/en/function.fopen.php
     * @var array
     */
    private $readableModes = [
        'r', 'r+', 'w+', 'a+', 'x+', 'c+', 'rb', 'w+b', 'r+b',
        'x+b', 'c+b', 'rt', 'w+t', 'r+t', 'x+t', 'c+t'
    ];
    
    /**
     * Writable flags of files and streams.
     *
     * @see http://php.net/manual/en/function.fopen.php
     * @var array
     */
    private $writableModes = [
        'w', 'w+', 'rw', 'r+', 'x+', 'c+', 'wb', 'w+b', 'r+b',
        'x+b', 'c+b', 'w+t', 'r+t', 'x+t', 'c+t', 'a', 'a+'
    ];
    
    /**
     * Stream constructor.
     *
     * @param resource $streamSource
     *
     * @throws \InvalidArgumentException If the stream source is not a resource
     */
    public function __construct($streamSource) {
        
        if (!is_resource($streamSource)) {
            throw new \InvalidArgumentException('Stream source is not a resource');
        }
        
        $this->stream = $streamSource;
        
        $this->meta = stream_get_meta_data($this->stream);
        //$this->seekable = $this->meta['seekable'];
        //$this->readable = array_search($this->meta['mode'], $this->readableArray);
        //$this->writable = array_search($this->meta['mode'], $this->writableArray);
        
    }
    
    /**
     * Destructor of Stream
     */
    public function __destruct()
    {
        $this->close();
    }
    
    /**
     * Reads all data from the stream into a string, from the beginning to end.
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     * Warning: This could attempt to load a large amount of data into memory.
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString() : string
    {
        try {
            $this->rewind();
            return $this->getContents();
        } catch (\Exception $e) {
            return '';
        }
    }
    
    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
        
        $this->detach();
    }
    
    /**
     * Separates any underlying resources from the stream.
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $result = isset($this->stream) ? $this->stream : null;
        
        if (!is_null($result)) {
            $this->stream = null;
            $this->size = null;
            $this->meta = null;
            /*$this->readable = false;
            $this->seekable = false;
            $this->writable = false;*/
        }
        
        return $result;
        
    }
    
    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        if (!isset($this->stream)) {
            return null;
        }
        
        if (is_null($this->size)) {
            
            $stats = fstat($this->stream);
            
            if (isset($stats['size'])) {
                $this->size = (int) $stats['size'];
            }
        }
        
        return $this->size;
    }
    
    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell() : int
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }
    
        $position = ftell($this->stream);
    
        if ($position === false) {
            throw new \RuntimeException('Cannot read to position of read/write pointer');
        }
        
        return $position;
    }
    
    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }
        
        return feof($this->stream);
    }
    
    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        return $this->meta['seekable'];
    }
    
    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     *
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.
     *     SEEK_SET: Set position equal to offset bytes
     *     SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     *
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }
    
        if (!$this->isSeekable()) {
            throw new \RuntimeException('Stream is not seekable');
        }
        
        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new \RuntimeException('Cannot seek to position ' . $offset . ' with whence ' . var_export($whence, true));
        }
    }
    
    /**
     * Seek to the beginning of the stream.
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind()
    {
        try {
            $this->seek(0);
        } catch (\RuntimeException $e) {
            throw $e;
        }
    }
    
    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        //return $this->isWritable;
        $writable = false;
        
        foreach ($this->writableModes as $mode) {
            if($mode === $this->meta['mode']) {
                $writable = true;
                break;
            }
        }
        
        return $writable;
    }
    
    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     *
     * @return int Returns the number of bytes written to the stream.
     * @throws \InvalidArgumentException if input is not string
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }
    
        if (!$this->isWritable()) {
            throw new \RuntimeException('Stream is not writable');
        }
        
        if (!is_string($string)) {
            throw new \InvalidArgumentException('Argument must be of type string');
        }
        
        $this->size = null; // Size changes after writing
        $result = fwrite($this->stream, $string);
        
        if ($result === false) {
            throw new \RuntimeException('Cannot write to stream');
        }
        
        return $result;
    }
    
    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        //return $this->isReadable;
        $readable = false;
        
        foreach ($this->readableModes as $mode) {
            if ($mode === $this->meta['mode']) {
                $readable = true;
                break;
            }
        }
        
        return $readable;
    }
    
    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     *
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \InvalidArgumentException if the length is not a positive integer
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length)
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }
    
        if (!$this->isReadable()) {
            throw new \RuntimeException('Stream is not readable');
        }
        
        if (!is_integer($length)) {
            throw new \InvalidArgumentException('Length is not an integer');
        }
    
        if ($length < 0) {
            throw new \InvalidArgumentException('Length cannot be negative');
        }
        
        if ($length === 0) {
            $result = '';
        } else {
            $result = fread($this->stream, $length);
            
            if ($result === false) {
                throw new \RuntimeException('Cannot read from stream');
            }
        }
        
        return $result;
    }
    
    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents()
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }
        
        if (!$this->isReadable() || ($contents = stream_get_contents($this->stream)) === false) {
            throw new \RuntimeException('Cannot read the content of stream');
        }
        
        return $contents;
    }
    
    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     *
     * @param string $key Specific metadata to retrieve.
     *
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        if (!isset($this->stream)) {
            return null;
        }
        
        if (!is_null($key)) {
            return isset($this->meta[$key]) ? $this->meta[$key] : null;
        } else {
            return $this->meta;
        }
    }
}