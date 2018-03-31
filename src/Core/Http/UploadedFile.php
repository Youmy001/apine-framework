<?php
/**
 * UploadedFile
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */

declare(strict_types=1);

namespace Apine\Core\Http;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

use const \UPLOAD_ERR_OK;

class UploadedFile implements UploadedFileInterface
{
    /**
     * @var int[]
     */
    private static $errors = [
        UPLOAD_ERR_OK,
        UPLOAD_ERR_INI_SIZE,
        UPLOAD_ERR_FORM_SIZE,
        UPLOAD_ERR_PARTIAL,
        UPLOAD_ERR_NO_FILE,
        UPLOAD_ERR_NO_TMP_DIR,
        UPLOAD_ERR_CANT_WRITE,
        UPLOAD_ERR_EXTENSION,
    ];
    
    /**
     * @var StreamInterface
     */
    private $stream;
    
    private $file;
    
    private $clientMediaType;
    
    private $clientFilename;
    
    private $error;
    
    private $moved = false;
    
    private $sapi = false;
    
    public function __construct($resource, int $size, int $errorStatus = UPLOAD_ERR_OK, $clientFilename = null, $clientMediaType = null, $sapi = false)
    {
        $this->size = $size;
        $this->error = $errorStatus;
        $this->sapi = $sapi;
        $this->setClientFilename($clientFilename);
        $this->setClientMediaType($clientMediaType);
        
        if ($this->isValid()) {
            if (is_string($resource)) {
                $this->file = $resource;
            } else if (is_resource($resource)) {
                $this->stream = new Stream($resource);
            } else if ($resource instanceof StreamInterface) {
                $this->stream = $resource;
            } else {
                throw new \InvalidArgumentException("Invalid resource provided");
            }
        }
    }
    
    private function setClientFilename($filename)
    {
        if (false === in_array(gettype($filename), ['string', 'NULL'])) {
            throw new \InvalidArgumentException("Uploaded file filename must be string");
        }
        
        $this->clientFilename = $filename;
    }
    
    private function setClientMediaType($mediaType)
    {
        if (false === in_array(gettype($mediaType), ['string', 'NULL'])) {
            throw new \InvalidArgumentException("Uploaded file media type must be string");
        }
    
        $this->clientMediaType = $mediaType;
    }
    
    private function isValid () : bool
    {
        return ($this->error === UPLOAD_ERR_OK);
    }
    
    private function isMoved () : bool
    {
        return ($this->moved === true);
    }
    
    /**
     * Retrieve a stream representing the uploaded file.
     * This method MUST return a StreamInterface instance, representing the
     * uploaded file. The purpose of this method is to allow utilizing native PHP
     * stream functionality to manipulate the file upload, such as
     * stream_copy_to_stream() (though the result will need to be decorated in a
     * native PHP stream wrapper to work with such functions).
     * If the moveTo() method has been called previously, this method MUST raise
     * an exception.
     *
     * @return StreamInterface Stream representation of the uploaded file.
     * @throws \RuntimeException in cases when no stream is available or can be
     *     created.
     */
    public function getStream() : StreamInterface
    {
        if (!$this->isValid()) {
            throw new \RuntimeException("Cannot retrieve stream due to upload error");
        }
        
        if ($this->isMoved()) {
            throw new \RuntimeException("Cannot retrieve stream after it has been moved");
        }
        
        if ($this->stream instanceof StreamInterface) {
            return $this->stream;
        }
        
        return new Stream(fopen($this->file, 'r+'));
    }
    
    /**
     * Move the uploaded file to a new location.
     * Use this method as an alternative to move_uploaded_file(). This method is
     * guaranteed to work in both SAPI and non-SAPI environments.
     * Implementations must determine which environment they are in, and use the
     * appropriate method (move_uploaded_file(), rename(), or a stream
     * operation) to perform the operation.
     * $targetPath may be an absolute path, or a relative path. If it is a
     * relative path, resolution should be the same as used by PHP's rename()
     * function.
     * The original file or stream MUST be removed on completion.
     * If this method is called more than once, any subsequent calls MUST raise
     * an exception.
     * When used in an SAPI environment where $_FILES is populated, when writing
     * files via moveTo(), is_uploaded_file() and move_uploaded_file() SHOULD be
     * used to ensure permissions and upload status are verified correctly.
     * If you wish to move to a stream, use getStream(), as SAPI operations
     * cannot guarantee writing to stream destinations.
     *
     * @see http://php.net/is_uploaded_file
     * @see http://php.net/move_uploaded_file
     *
     * @param string $targetPath Path to which to move the uploaded file.
     *
     * @throws \InvalidArgumentException if the $targetPath specified is invalid.
     * @throws \RuntimeException on any error during the move operation, or on
     *     the second or subsequent call to the method.
     */
    public function moveTo($targetPath)
    {
        if ($this->isMoved()) {
            throw new \RuntimeException("File has already been moved once");
        }
        
        if (!is_string($targetPath)) {
            throw new \InvalidArgumentException("The specified path is invalid");
        }
        
        /*try {
            if ($this->file) {
                $this->moved = (php_sapi_name() == 'cli')
                    ? rename($this->file, $targetPath)
                    : move_uploaded_file($this->file, $targetPath);
            } else {
                $newfile = new Stream(fopen($targetPath, 'w'));
        
                while (!$this->stream->eof()) {
                    if (!$newfile->write($this->stream->read(8192))) {
                        break;
                    }
                }
                
                $this->moved = true;
            }
    
            if (!$this->moved) {
                throw new \RuntimeException(sprintf("File could not be moved to %s", $targetPath));
            }
        } catch (\Throwable $e) {
            throw $e;
        }*/
        
        if ($this->stream) {
            try {
                $newfile = new Stream(fopen($targetPath, 'w'));
    
                while (!$this->stream->eof()) {
                    if (!$newfile->write($this->stream->read(8192))) {
                        break;
                    }
                }
            } catch (\Throwable $e) {
                throw new \RuntimeException(sprintf("File %1s could not be moved to %2s", $this->file, $targetPath));
            }
        } else if ($this->sapi) {
            if (!is_uploaded_file($this->file)) {
                throw new \RuntimeException(sprintf('%s is not a valid uploaded file', $this->file));
            }
            
            if(!move_uploaded_file($this->file, $targetPath)) {
                throw new \RuntimeException(sprintf("File %1s could not be moved to %2s", $this->file, $targetPath));
            }
        } else {
            if(!rename($this->file, $targetPath)) {
                throw new \RuntimeException(sprintf("File %1s could not be moved to %2s", $this->file, $targetPath));
            }
        }
        
        $this->moved = true;
    }
    
    /**
     * Retrieve the file size.
     * Implementations SHOULD return the value stored in the "size" key of
     * the file in the $_FILES array if available, as PHP calculates this based
     * on the actual size transmitted.
     *
     * @return int|null The file size in bytes or null if unknown.
     */
    public function getSize()
    {
        return $this->size;
    }
    
    /**
     * Retrieve the error associated with the uploaded file.
     * The return value MUST be one of PHP's UPLOAD_ERR_XXX constants.
     * If the file was uploaded successfully, this method MUST return
     * UPLOAD_ERR_OK.
     * Implementations SHOULD return the value stored in the "error" key of
     * the file in the $_FILES array.
     *
     * @see http://php.net/manual/en/features.file-upload.errors.php
     * @return int One of PHP's UPLOAD_ERR_XXX constants.
     */
    public function getError()
    {
        return $this->error;
    }
    
    /**
     * Retrieve the filename sent by the client.
     * Do not trust the value returned by this method. A client could send
     * a malicious filename with the intention to corrupt or hack your
     * application.
     * Implementations SHOULD return the value stored in the "name" key of
     * the file in the $_FILES array.
     *
     * @return string|null The filename sent by the client or null if none
     *     was provided.
     */
    public function getClientFilename()
    {
        return $this->clientFilename;
    }
    
    /**
     * Retrieve the media type sent by the client.
     * Do not trust the value returned by this method. A client could send
     * a malicious media type with the intention to corrupt or hack your
     * application.
     * Implementations SHOULD return the value stored in the "type" key of
     * the file in the $_FILES array.
     *
     * @return string|null The media type sent by the client or null if none
     *     was provided.
     */
    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }
}