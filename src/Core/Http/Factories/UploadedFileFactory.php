<?php
/**
 * UploadedFileFactory
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Http\Factories;

use Apine\Core\Http\UploadedFile;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class UploadedFileFactory
 *
 * @package Apine\Core\Http\Factories
 */
class UploadedFileFactory
{
    /**
     * Create a new uploaded file.
     *
     * If a string is used to create the file, a temporary resource will be
     * created with the content of the string.
     *
     * If a size is not provided it will be determined by checking the size of
     * the file.
     *
     * @see http://php.net/manual/features.file-upload.post-method.php
     * @see http://php.net/manual/features.file-upload.errors.php
     *
     * @param string|resource $file
     * @param int $size in bytes
     * @param int $error PHP file upload error
     * @param string $clientFilename
     * @param string $clientMediaType
     *
     * @return UploadedFileInterface
     *
     * @throws \InvalidArgumentException
     *  If the file resource is not readable.
     */
    public function createUploadedFile(
        $file,
        $size = null,
        $error = \UPLOAD_ERR_OK,
        $clientFilename = null,
        $clientMediaType = null
    ) : UploadedFileInterface
    {
        return new UploadedFile(
            $file,
            $size,
            $error,
            $clientFilename,
            $clientMediaType
        );
    }
}