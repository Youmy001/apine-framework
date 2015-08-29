<?php
/**
 * Image File Resource Handler
 * This script contains an helper to manage image files
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

/**
 * Image File Resource Handler
 * Manager wrapping PHP image method in an easy Object Oriented way
 */
class ApineFileImage extends ApineFile {

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
	 * Construct the FileImage handler
	 *
	 * @param string $a_path
	 * @throws Exception If the file isn't an image or does not exists
	 */
	public function __construct($a_path = null) {

		if (is_file($a_path)) {
			// Open File
			//$this->file = fopen($a_path, "c+");
			$this->path = $a_path;
			$this->name = basename($a_path);
			$this->location = substr($this->path, 0, strripos($a_path, "/") + 1);
				
			try {
				$this->write();
			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}
				
		} else {
			throw new Exception('File not found');
		}

	}


	/**
	 * Verify the image is of a valid format
	 *
	 * @return boolean
	 */
	public function is_valid_image() {

		$allowedExts = array(
						"jpeg",
						"jpg",
						"png",
						"PNG",
						"JPG",
						"JPEG",
						"gif",
						"GIF"
		);
		$extension = $this->extention();

		if ((($this->type() == "image/jpeg") || ($this->type() == "image/jpg") || ($this->type() == "image/png") || ($this->type() == "image/GIF") || ($this->type() == "image/gif")) && in_array($extension, $allowedExts)) {
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
	public function width() {

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
	public function height() {

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

		$crop_w = $this->width;
		$crop_h = ceil($crop_w / $ratio);

		// Crop image if size is different
		if (($crop_h != $this->height()) || ($crop_w != $this->width())) {

			if ($crop_h < $this->height) {						// If current heigth is bigger
				$new_image = imagecreatetruecolor($crop_w, $crop_h);
				$x = 0;
				$y = floor(0.5 * ($this->height() - $crop_h));
				imagecopyresized($new_image, $this->file, 0, 0, $x, $y, $crop_w, $crop_h, $crop_w, $crop_h);
			} else if($crop_h > $this->height()) {				// If current height is smaller
				$crop_h = $this->height();
				$crop_w = ceil($ratio * $crop_h);
				$new_image = imagecreatetruecolor($crop_w, $crop_h);
				$y = 0;
				$x = floor(0.5 * ($this->width() - $crop_w));
				imagecopyresized($new_image, $this->file, 0, 0, $x, $y, $crop_w, $crop_h, $crop_w, $crop_h);
			}

			if (isset($new_image)) {
				write($new_image);
			}

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
	public function crop($n_width, $n_height, $x, $y) {
	
		$new_image = imagecreatetruecolor($n_width, $n_height);
		imagecopyresized($new_image, $this->file, 0, 0, $x, $y, $n_width, $n_height, $this->width(), $this->height());
		
		if (isset($new_image)) {
			write($new_image);
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
	public function filter($filtertype, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null) {
	
		imagefilter($this->file, $filtertype, $arg1, $arg2, $arg3, $arg4);
		write($this->file);
		
	}
	
	/**
	 * Flips the image follwing given mode
	 * 
	 * @param integer $mode
	 *        Flip mode. See php manual for a list of available Flip
	 *        modes.
	 * @link http://php.net/manual/en/function.imageflip.php
	 */
	public function flip($mode) {
	
		imageflip($this->file, $mode);
		write($this->file);
		
	}

	/**
	 * Destructor
	 */
	public function __destruct () {

		imagedestroy($this->file);

	}
	
	/**
	 * Write changes to the file
	 * 
	 * @param Resource $a_image Modified Image Resource Stream
	 */
	public function write ($a_image = null) {
		
		if (is_null(a_image)) {
			if (($this->get_type() == "image/png") || ($this->get_type() == "image/PNG")) {
				imagepng(a_image, $this->path);
			} else if (($this->get_type() == "image/jpg") || ($this->get_type() == "image/jpeg") || ($this->get_type() == "image/JPG") || ($this->get_type() == "image/JPEG")) {
				imagejpeg(a_image, $this->path);
			} else if (($this->get_type() == "image/GIF") || ($this->get_type() == "image/gif")) {
				imagegif(a_image, $this->path);
			} else {
				throw new Exception('Invalid file format');
			}
		} else {
			if ($this->is_valid_image()) {
				if (($this->get_type() == "image/png") || ($this->get_type() == "image/PNG")) {
					$this->file = imagecreatefrompng($this->path);
				} else if (($this->get_type() == "image/jpg") || ($this->get_type() == "image/jpeg") || ($this->get_type() == "image/JPG") || ($this->get_type() == "image/JPEG")) {
					$this->file = imagecreatefromjpeg($this->path);
				} else if (($this->get_type() == "image/GIF") || ($this->get_type() == "image/gif")) {
					$this->file = imagecreatefromgif($this->path);
				}
				$this->write();
			} else {
				throw new Exception('Invalid file format');
			}
		}
		
	}

}

/**
 * Compute a ratio from a multiplier
 *
 * @param double $n
 *        Ratio multiplier
 * @param real $tolerance
 *        Precision level of the procedure
 * @return string
 */
function float2rat($n, $tolerance = 1.e-6) {

	$h1 = 1;
	$h2 = 0;
	$k1 = 0;
	$k2 = 1;
	$b = 1 / $n;

	do {
		$b = 1 / $b;
		$a = floor($b);
		$aux = $h1;
		$h1 = $a * $h1 + $h2;
		$h2 = $aux;
		$aux = $k1;
		$k1 = $a * $k1 + $k2;
		$k2 = $aux;
		$b = $b - $a;
	} while(abs($n - $h1 / $k1) > $n * $tolerance);

	return "$h1/$k1";

}
