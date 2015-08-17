<?php
/**
 * Image Ressource files gestion
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package apine-framework
 * @subpackage system
 */

/**
 * Compute a ratio from a multiplier
 * @param double $n
 *        Ratio multiplier
 * @param real $tolerance
 *        Precision level of the procedure
 * @return string
 */
function float2rat($n, $tolerance = 1.e-6){

	$h1 = 1;
	$h2 = 0;
	$k1 = 0;
	$k2 = 1;
	$b = 1 / $n;
	do{
		$b = 1 / $b;
		$a = floor($b);
		$aux = $h1;
		$h1 = $a * $h1 + $h2;
		$h2 = $aux;
		$aux = $k1;
		$k1 = $a * $k1 + $k2;
		$k2 = $aux;
		$b = $b - $a;
	}while(abs($n - $h1 / $k1) > $n * $tolerance);
	return "$h1/$k1";

}

/**
 * Image Ressource files gestion
 *
 * The File_Image class manages JPEG, PNG and GIF image formats. It
 * contains the tools to crop, resize and apply basic filters to the
 * ressource image.
 */
class FileImage extends File{

	/**
	 * Image's height in pixels
	 * @var integer
	 */
	private $height;

	/**
	 * Image's width in pixels
	 * @var integer
	 */
	private $width;

	/**
	 * File_Image's class constructor
	 * @param string $location
	 *        Image's location
	 * @throws Exception If the file isn't an image
	 * @return boolean
	 */
	public function __construct($location = null){

		parent::__construct($location);
		try{
			if(($this->get_type() == "image/png") || ($this->get_type() == "image/PNG")){
				$this->file = imagecreatefrompng($this->get_location());
			}else if(($this->get_type() == "image/jpg") || ($this->get_type() == "image/jpeg") || ($this->get_type() == "image/JPG") || ($this->get_type() == "image/JPEG")){
				$this->file = imagecreatefromjpeg($this->get_location());
			}else if(($this->get_type() == "image/GIF") || ($this->get_type() == "image/gif")){
				$this->file = imagecreatefromgif($this->get_location());
			}else{
				throw new Exception("This file (".$this->get_location().") has not a valid file format : " . $this->get_type() . ".");
			}
			return true;
		}catch(Exception $e){
			print "Error : " . $e->getMessage();
			return false;
		}
	
	}

	/**
	 * Fetch Image width in pixels
	 * @return integer
	 */
	public function get_width(){

		$sizearray = getimagesize($this->get_location());
		$this->width = $sizearray[0];
		return $this->width;
	
	}

	/**
	 * Fetch Image height in pixels
	 * @return integer
	 */
	public function get_height(){

		$sizearray = getimagesize($this->get_location());
		$this->height = $sizearray[1];
		return $sizearray[1];
	
	}

	/**
	 * Verify the image is of a valid format
	 * @return boolean
	 */
	public function is_valid_image(){

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
		$extension = $this->get_extention();
		if((($this->get_type() == "image/jpeg") || ($this->get_type() == "image/jpg") || ($this->get_type() == "image/png") || ($this->get_type() == "image/GIF") || ($this->get_type() == "image/gif")) && in_array($extension, $allowedExts)){
			if($this->get_error_code() > 0){
				// Error during file transfert
				return false;
			}else{
				// Image OK
				return true;
			}
		}else{
			// Invalid file
			return false;
		}
	
	}

	/**
	 * Crop the image to a specified ratio.
	 * The cropped image will be centered inside the original image.
	 * @param string $a_ratio
	 *        Desired height/width ratio
	 */
	public function crop_to_ratio($a_ratio){

		$ratio_w;
		$ratio_h;
		$ratio;
		if(strpos($a_ratio, ":")){
			$ar_ratio = explode(':', $a_ratio);
			$ratio_w = (int) $ar_ratio[0];
			$ratio_h = (int) $ar_ratio[1];
			$ratio = (double) $ratio_w / $ratio_h;
		}elseif(strpos($a_ratio, "/")){
			$ar_ratio = explode('/', $a_ratio);
			$ratio_w = (int) $ar_ratio[0];
			$ratio_h = (int) $ar_ratio[1];
			$ratio = (double) $ratio_w / $ratio_h;
		}elseif(floatval($a_ratio) > 0){
			$ratio = (double) floatval($a_ratio);
			$s_ratio = float2rat($ratio);
			$ar_ratio = explode('/', $s_ratio);
			$ratio_w = (int) $ar_ratio[0];
			$ratio_h = (int) $ar_ratio[1];
		}
		$crop_w = $this->get_width();
		$crop_h = ceil($crop_w / $ratio);
		// Crop image if size is different
		if(($crop_h != $this->get_height()) || ($crop_w != $this->get_width())){
			if($crop_h < $this->get_height()){ // If current heigth is
			                                   // bigger
				$new_image = imagecreatetruecolor($crop_w, $crop_h);
				$x = 0;
				$y = floor(0.5 * ($this->get_height() - $crop_h));
				imagecopyresized($new_image, $this->file, 0, 0, $x, $y, $crop_w, $crop_h, $crop_w, $crop_h);
			}else if($crop_h > $this->get_height()){ // If current height
			                                         // is
			                                         // smaller
				$crop_h = $this->get_height();
				$crop_w = ceil($ratio * $crop_h);
				$new_image = imagecreatetruecolor($crop_w, $crop_h);
				$y = 0;
				$x = floor(0.5 * ($this->get_width() - $crop_w));
				imagecopyresized($new_image, $this->file, 0, 0, $x, $y, $crop_w, $crop_h, $crop_w, $crop_h);
			}
			if(isset($new_image)){
				$this->write($new_image); // Write image
				$this->create_by_location($this->get_location()); // Reload
					                                                  // Image
			}
		}
	
	}

	/**
	 * Crop the image to a desired size.
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
	public function crop($n_width, $n_height, $x, $y){

		$new_image = imagecreatetruecolor($n_width, $n_height);
		imagecopyresized($new_image, $this->file, 0, 0, $x, $y, $n_width, $n_height, $this->get_width(), $this->get_height());
		$this->write($new_image);
		$this->create_by_location($this->get_location());
	
	}

	/**
	 * Apply a filter to the image
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
	public function filter($filtertype, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null){

		imagefilter($this->file, $filtertype, $arg1, $arg2, $arg3, $arg4);
		$this->write($this->file); // Write image
		$this->create_by_location($this->get_location());
		// Reload Image
	}

	/**
	 * Flips the image follwing given mode
	 * @param integer $mode
	 *        Flip mode. See php manual for a list of available Flip
	 *        modes.
	 * @link http://php.net/manual/en/function.imageflip.php
	 */
	public function flip($mode){

		imageflip($this->file, $mode);
		$this->write($this->file); // Write image
		$this->create_by_location($this->get_location());
		// Reload Image
	}

	/**
	 * Reload an image resource
	 * @param string $location
	 *        Resource location
	 * @throws Exception If the resource is not a valid image format
	 */
	private function create_by_location($location){

		if(($this->get_type() == "image/png") || ($this->get_type() == "image/PNG")){
			$this->file = imagecreatefrompng($location);
		}else if(($this->get_type() == "image/jpg") || ($this->get_type() == "image/jpeg") || ($this->get_type() == "image/JPG") || ($this->get_type() == "image/JPEG")){
			$this->file = imagecreatefromjpeg($location);
		}else if(($this->get_type() == "image/GIF") || ($this->get_type() == "image/gif")){
			$this->file = imagecreatefromgif($location);
		}else{
			throw new Exception("This file has not a valid file format : " . $this->get_type() . ".");
		}
	
	}
	
	/**
	 * Write changes to image resource to the hard drive
	 * @param resource $image
	 * @throws Exception If the resource is not a valid image format
	 */
	private function write($image){

		if(($this->get_type() == "image/png") || ($this->get_type() == "image/PNG")){
			$this->file = imagepng($image, $this->get_location());
		}else if(($this->get_type() == "image/jpg") || ($this->get_type() == "image/jpeg") || ($this->get_type() == "image/JPG") || ($this->get_type() == "image/JPEG")){
			$this->file = imagejpeg($image, $this->get_location());
		}else if(($this->get_type() == "image/GIF") || ($this->get_type() == "image/gif")){
			$this->file = imagegif($image, $this->get_location());
		}else{
			throw new Exception("This file has not a valid file format : " . $this->get_type() . ".");
		}
	
	}
	
	/**
	 * (non-PHPdoc)
	 * @see File::save()
	 */
	public function save($move=false){

		$this->write($this->file);
		$this->create_by_location($this->get_location());
		return parent::save();
	
	}
	
	/**
	 * (non-PHPdoc)
	 * @see File::move()
	 */
	public function move(){
		$this->write($this->file);
		$this->create_by_location($this->get_location());
		return parent::save(true);
	}
	
	/**
	 * Image_File class' destructor
	 */
	public function __destruct(){

		imagedestroy($this->file);
	
	}

}
?>
