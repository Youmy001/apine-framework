<?php
/**
 * Image File Resource Handler
 * This script contains an helper to manage image files
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\File;

use \Exception;
use Apine\Exception\GenericException;

/**
 * Image File Resource Handler
 * 
 * Manager wrapping PHP image method in an easy Object Oriented way
 */
final class FileImage extends File {

	/**
	 * Image's height in pixels
	 * 
	 * @var integer
	 */
	private $height;

	/**
	 * Image's width in pixels
	 * 
	 * @var integer
	 */
	private $width;
	
	/**
	 * Image resource
	 * 
	 * @var resource
	 */
	private $image;
	
	/**
	 * List of allowed extensions
	 * 
	 * @var array
	 */
	private $allowed_extensions = array(
					"jpeg",
					"jpg",
					"png",
					"gif",
					"PNG",
					"JPG",
					"JPEG",
					"GIF"
	);
	
	/**
	 * List of allowed mime types
	 * 
	 * @var array
	 */
	private $allowed_mime_type = array(
					"image/jpeg",
					"image/jpg",
					"image/png",
					"image/GIF",
					"image/gif"
	);

	/**
	 * Construct the FileImage handler
	 *
	 * @param string $a_path
	 * @throws Exception If the file isn't an image or does not exists
	 */
	public function __construct ($a_path = null) {

		try {
			parent::__construct($a_path);
			//$this->write();
			$this->load();
		} catch (GenericException $e) {
			throw $e;
		}

	}
	
	/**
	 * Load Image resource
	 * 
	 * @throws ApineException
	 */
	private function load () {
		
		if(!$this->is_valid_image()) {
			throw new GenericException("Invalid file format (" . $this->path . ")");
		}
		
		if ($this->type() == "image/jpeg" || $this->type() == "image/jpg") {
			$this->image = imagecreatefromjpeg($this->path);
		} else if ($this->type() == "image/png") {
			$this->image = imagecreatefrompng($this->path);
		} else if ($this->type() == "image/gif" || $this->type() == "image/GIF") {
			$this->image = imagecreatefromgif($this->path);
		}
		
	}
	
	/**
	 * Create a new empty file
	 * 
	 * @param string $a_path
	 * @throws Exception ApineFileImage can't handle empty files
	 */
	public static function create ($a_path) {
		
		throw new GenericException('ApineFileImage cannot handle empty image files');
		
	}


	/**
	 * Verify the image is of a valid format
	 *
	 * @return boolean
	 */
	public function is_valid_image () {

		$extension = $this->extension();

		if (in_array($this->type(), $this->allowed_mime_type)) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Fetch Image width in pixels
	 *
	 * @return integer
	 */
	public function width () {

		if (is_null($this->width)) {
			$sizearray = getimagesize($this->path);
			$this->width = $sizearray[0];
		}

		return $this->width;

	}

	/**
	 * Fetch Image height in pixels
	 *
	 * @return integer
	 */
	public function height () {

		if (is_null($this->height)) {
			$sizearray = getimagesize($this->path);
			$this->height = $sizearray[1];
		}

		return $this->height;

	}

	/**
	 * Crop the image to a specified ratio.
	 * The cropped image will be centered inside the original image.
	 * 
	 * @param string $a_ratio
	 *        Desired height/width ratio
	 */
	public function crop_to_ratio ($a_ratio) {
		
		if (!$this->readonly) {
			$ratio_w;
			$ratio_h;
			$ratio;
	
			if (strpos($a_ratio, ":")) {
				$ar_ratio = explode(':', $a_ratio);
				$ratio_w = (int) $ar_ratio[0];
				$ratio_h = (int) $ar_ratio[1];
				$ratio = (double) $ratio_w / $ratio_h;
			} else if (strpos($a_ratio, "/")) {
				$ar_ratio = explode('/', $a_ratio);
				$ratio_w = (int) $ar_ratio[0];
				$ratio_h = (int) $ar_ratio[1];
				$ratio = (double) $ratio_w / $ratio_h;
			} else if (floatval($a_ratio) > 0) {
				$ratio = (double) floatval($a_ratio);
				$s_ratio = float2rat($ratio);
				$ar_ratio = explode('/', $s_ratio);
				$ratio_w = (int) $ar_ratio[0];
				$ratio_h = (int) $ar_ratio[1];
			}
	
			$crop_w = $this->width();
			$crop_h = ceil($crop_w / $ratio);
	
			// Crop image if size is different
			if (($crop_h != $this->height()) || ($crop_w != $this->width())) {
	
				if ($crop_h < $this->height()) {						// If current heigth is bigger
					$new_image = imagecreatetruecolor($crop_w, $crop_h);
					$x = 0;
					$y = floor(0.5 * ($this->height() - $crop_h));
					imagecopyresized($new_image, $this->image, 0, 0, $x, $y, $crop_w, $crop_h, $crop_w, $crop_h);
				} else if($crop_h > $this->height()) {				// If current height is smaller
					$crop_h = $this->height();
					$crop_w = ceil($ratio * $crop_h);
					$new_image = imagecreatetruecolor($crop_w, $crop_h);
					$y = 0;
					$x = floor(0.5 * ($this->width() - $crop_w));
					imagecopyresized($new_image, $this->image, 0, 0, $x, $y, $crop_w, $crop_h, $crop_w, $crop_h);
				}
	
				if (isset($new_image)) {
					$this->write($new_image);
				}
	
			}
		} else {
			throw new GenericException("Can't modify images in read-only mode");
		}

	}
	
	/**
	 * Crop the image to a desired size.
	 * 
	 * @param integer $n_width
	 *        Desired width in pixels
	 * @param integer $n_height
	 *        Desired heigth in pixels
	 * @param integer $x
	 *        Horizontal Position of the cropped image inside the
	 *        original image from the upper-left corner.
	 * @param integer $y
	 *        Vertical Position of the cropped image inside the
	 *        original image from the upp-left corner.
	 */
	public function crop ($n_width, $n_height, $x, $y) {
	
		if (!$this->readonly) {
			
			if (($n_width + $x) > $this->width() || ($n_height + $y) > $this->height()) {
				throw new ApineException("Invalid cropping dimensions");
			}
			
			$new_image = imagecreatetruecolor($n_width, $n_height);
			imagecopyresized($new_image, $this->image, 0, 0, $x, $y, $n_width, $n_height, $n_width, $n_height);
			
			if (isset($new_image)) {
				$this->write($new_image);
			}
		} else {
			throw new GenericException("Can't modify images in read-only mode");
		}
	
	}
	
	/**
	 * Resize the image to a desired size.
	 *
	 * @param integer $n_width
	 *        Desired width in pixels
	 * @param integer $n_height
	 *        Desired heigth in pixels
	 */
	public function resize ($n_width, $n_height) {
	
		if (!$this->readonly) {
			$new_image = imagecreatetruecolor($n_width, $n_height);
			imagecopyresized($new_image, $this->image, 0, 0, 0, 0, $n_width, $n_height, $this->width(), $this->height());
				
			if (isset($new_image)) {
				$this->write($new_image);
			}
		} else {
			throw new GenericException("Can't modify images in read-only mode");
		}
	
	}
	
	/**
	 * Apply a filter to the image
	 * 
	 * @param integer $filtertype
	 *        Type of filter to apply to the image. See the php manual
	 *        for available filters.
	 * @param string $arg1[optional]
	 *        See the php manual for a list of optional arguments.
	 * @param string $arg2[optional]
	 *        See the php manual for a list of optional arguments.
	 * @param string $arg3[optional]
	 *        See the php manual for a list of optional arguments.
	 * @param string $arg4[optional]
	 *        See the php manual for a list of optional arguments.
	 * @link http://www.php.net/manual/en/function.imagefilter.php
	 */
	public function filter ($filtertype, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null) {
	
		if (!$this->readonly) {
			imagefilter($this->image, $filtertype, $arg1, $arg2, $arg3, $arg4);
			$this->write($this->image);
		} else {
			throw new GenericException("Can't modify images in read-only mode");
		}
		
	}
	
	/**
	 * Flips the image follwing given mode
	 * 
	 * @param integer $mode
	 *        Flip mode. See php manual for a list of available Flip
	 *        modes.
	 * @link http://php.net/manual/en/function.imageflip.php
	 */
	public function flip ($mode) {
	
		if (!$this->readonly) {
			imageflip($this->image, $mode);
			$this->write($this->image);
		} else {
			throw new GenericException("Can't modify images in read-only mode");
		}
		
	}

	/**
	 * Destructor
	 */
	public function __destruct () {

		imagedestroy($this->image);

	}
	
	/**
	 * Write changes to the file
	 * 
	 * @param Resource $a_image Modified Image Resource Stream
	 * @param null $useless Seriously, don't try to put anything here. It won't have any effect
	 */
	public function write ($a_image = null, $useless = null) {
		
		if(!$this->is_valid_image()) {
			throw new GenericException("Invalid file format (" . $this->path . ")");
		}
		
		if (!$this->readonly) {
			if (!is_null($a_image)) {
				if (($this->type() == "image/png") || ($this->type() == "image/PNG")) {
					imagepng($a_image, $this->path);
				} else if (($this->type() == "image/jpg") || ($this->type() == "image/jpeg") || ($this->type() == "image/JPG") || ($this->type() == "image/JPEG")) {
					imagejpeg($a_image, $this->path);
				} else if (($this->type() == "image/GIF") || ($this->type() == "image/gif")) {
					imagegif($a_image, $this->path);
				} else {
					throw new GenericException('Invalid file format');
				}
			}
			
			if (($this->type() == "image/png") || ($this->type() == "image/PNG")) {
				$this->file = imagecreatefrompng($this->path);
			} else if (($this->type() == "image/jpg") || ($this->type() == "image/jpeg") || ($this->type() == "image/JPG") || ($this->type() == "image/JPEG")) {
				$this->file = imagecreatefromjpeg($this->path);
			} else if (($this->type() == "image/GIF") || ($this->type() == "image/gif")) {
				$this->file = imagecreatefromgif($this->path);
			}
			
			$this->width = null;
			$this->height = null;
		} else {
			throw new GenericException("Can't open images in read-only mode");
		}
			
	}

}