<?php
/**
 * This file contains the image class
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package bokaro
 * @subpackage system
 */

/**
 * Database representation of uploaded images
 */
class ApineImage extends ApineEntityModel {

	/**
	 * Image identifier in database
	 * @var integer
	 */
	protected $id;

	/**
	 * Image external identifier
	 * @var string
	 */
	protected $access_id;

	/**
	 * Image owner
	 * @var User
	 */
	protected $user;

	/**
	 * Privacy level of the image
	 * @var integer
	 */
	protected $privacy;

	/**
	 * Image folder
	 * @var string
	 */
	protected $folder;

	/**
	 * Image file
	 * @var File_Image
	 */
	protected $file;

	/**
	 * Image class' contructor
	 * @param integer $a_id
	 *        Image identifier on database
	 */
	public function __construct ($a_id = null) {

		$this->_initialize('apine_images', $a_id);
		
		if (!is_null($a_id)) {
			$this->id = $a_id;
		}
	
	}

	/**
	 * Fetch image's identifier on database
	 * @return integer
	 */
	public function get_id () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->id;
	
	}

	/**
	 * Set image's identifier on database
	 * @param integer $a_id        
	 */
	public function set_id ($a_id) {

		$this->id = $a_id;
		$this->_set_id($a_id);
		$this->_set_field('id', $a_id);
	
	}

	/**
	 * Fetch image's external identifier
	 * @return string
	 */
	public function get_access_id () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->access_id;
	
	}

	/**
	 * Set image's external id
	 * @param string $a_access_id
	 *        Image's external identifier
	 */
	public function set_access_id ($a_access_id) {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		$this->access_id = $a_access_id;
		$this->_set_field('access_id', $a_access_id);
	
	}

	/**
	 * Fetch image's privacy level
	 * @return interger
	 */
	public function get_privacy () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->privacy;
	
	}

	/**
	 * Set image's privacy level
	 * @param integer $a_privacy
	 *        Image's privacy level
	 */
	public function set_privacy ($a_privacy) {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		$this->privacy = $a_privacy;
		$this->_set_field('privacy', $a_privacy);
	
	}

	/**
	 * Fetch image's folder
	 * @return string
	 */
	public function get_folder () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->folder;
	
	}

	/**
	 * Set image's folder
	 * @param string $a_folder
	 *        Image folder
	 */
	public function set_folder ($a_folder) {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		$this->folder = $a_folder;
		$this->_set_field('folder', $a_folder);
	
	}

	/**
	 * Fetch image's owner
	 * @return User
	 */
	public function get_user () {

		if($this->loaded == 0) {
			$this->load();
		}
		
		return $this->user;
	
	}

	/**
	 * Set image's owner
	 * @param integer|User $a_user
	 *        Image's owner
	 */
	public function set_user ($a_user) {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		if (is_numeric($a_user)) {
			if (UserFactory::is_id_exist($a_user)) {
				$this->user = UserFactory::create_by_id($a_user);
			}
		} else if (get_class($a_user) == 'User') {
			$this->user = $a_user;
		}
		
		$this->_set_field('user_id', $this->user->get_id());
	
	}

	/**
	 * Fetch image's file resource
	 * @return File_Image
	 */
	public function get_file () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		return $this->file;
	
	}

	/**
	 * Set image's file resource
	 * @param string|File_Image $a_file
	 *        File location or file ressource
	 */
	public function set_file ($a_file) {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		if (is_string($a_file)) {
			$this->file = new ApineFileImage($a_file);
		} else if (get_class($a_file) == 'ApineFileImage') {
			$this->file = $a_file;
		}
		
		// Move the file to the image folder
		$this->file->save('resources/public/uploads/');
		$this->_set_field('file', 'upload/' . $this->file->get_file_name());
	
	}

	/**
	 * (non-PHPdoc)
	 * @see ApineEntityInterface::load()
	 */
	public function load () {

		if (!is_null($this->id)) {
			$this->access_id = $this->_get_field('access_id');
			$this->privacy = $this->_get_field('privacy');
			$this->folder = $this->_get_field("folder");
			$this->user = UserFactory::create_by_id($this->_get_field('user_id'));
			$this->file = new FileImage($this->_get_field('file'));
			$this->loaded = 1;
		}
	
	}

	/**
	 * (non-PHPdoc)
	 * @see ApineEntityInterface::save()
	 */
	public function save () {

		parent::_save();
		// Save
	}

	/**
	 * (non-PHPdoc)
	 * @see ApineEntityInterface::delete()
	 */
	public function delete () {

		if ($this->loaded == 0) {
			$this->load();
		}
		
		$this->file->delete(); // Delete the file from file system
		parent::_destroy();
		// Remove from the database
	}

}