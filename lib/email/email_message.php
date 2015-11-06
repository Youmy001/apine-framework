<?php
/**
 * Email Message
 * Prepare and send an Email message via a mail server
 * 
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

/**
 * Prepare and send an Email message
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @uses PHPMailer
 */
class ApineEmailMessage {

	/**
	 * PHPMailer instance
	 * 
	 * @var PHPMailer
	 */
	private $mail;
	
	/**
	 * ApineEmailMessage class' constructor
	 */
	public function __construct() {
		
		if (!class_exists('PHPMailer')) {
			throw new ApineException('PHPMailer not found. Please add "PHPMailer/PHPMailer" to your Commposer installation.', 500);
		}

		$this->mail = new PHPMailer();
		$this->mail->CharSet = 'utf-8';
		$this->mail->Encoding = 'base64';
		$this->mail->SMTPDebug = Config::get('runtime', 'mode') == "development" ? 3 : 0;
		//$this->mail->SMTPDebug = 0;
		
		$this->mail->isSMTP();
		$this->mail->Host = Config::get('mail', 'host');
		$this->mail->SMTPAuth = (bool) Config::get('mail', 'smtp_auth');
		$this->mail->Username = Config::get('mail', 'smtp_username');
		$this->mail->Password = Config::get('mail', 'smtp_password');
		$this->mail->SMTPSecure = Config::get('mail', 'protocol');
		$this->mail->Port = Config::get('mail', 'port');
		
		//$this->mail->From = Config::get('mail', 'sender_address');
		//$this->mail->FromName = Config::get('mail', 'sender_name');
		$this->mail->setFrom(Config::get('mail', 'sender_address'), Config::get('mail', 'sender_name'));
		// $this->mail->WordWrap = 50; // Max word lenght
		$this->mail->isHTML(true);
	
	}
	
	public function set_sender($a_address, $a_name = null) {
		
		if (filter_var($a_address, FILTER_VALIDATE_EMAIL)) {
			$this->mail->setFrom($a_address, $a_name);
		} else {
			throw new ApineException('Invalid Email Format', 500);
		}
		
	}

	/**
	 * Set email's subject
	 * 
	 * @param string $a_subject
	 */
	public function set_subject ($a_subject) {

		$this->mail->Subject = $a_subject;
	
	}

	/**
	 * Set email's body
	 * 
	 * @param string $a_content
	 */
	public function set_content ($a_content) {

		$this->mail->Body = $a_content;
	
	}

	/**
	 * Set email's alternative  body
	 * 
	 * @param string $a_content
	 */
	public function set_alt_content ($a_content) {

		$this->mail->AltBody = $a_content;
	
	}
	
	/**
	 * Set a reply address
	 * 
	 * @param string $a_reply
	 * @param string $a_name
	 */
	public function add_reply_address ($a_reply, $a_name = null) {
	
		if (filter_var($a_reply, FILTER_VALIDATE_EMAIL)) {
			$this->mail->addReplyTo($a_reply, $a_name);
		}
	
	}
	
	/**
	 * Add a recipient for the email
	 * 
	 * @param string $a_mail
	 * @param string $a_name
	 */
	public function add_recipient ($a_mail, $a_name = null) {
		
		$this->mail->addAddress($a_mail, $a_name);
		
	}

	/**
	 * Send email to recipent
	 * 
	 * @return boolean
	 */
	public function send () {

		ob_start();
		
		if (!$this->mail->send()) {
			$return = false;
		} else {
			$return = true;
		}
		
		ob_end_clean();
		return $return;
	
	}

	/**
	 * Get error message if an error occured
	 * 
	 * @return string
	 */
	public function get_error () {

		return $this->mail->ErrorInfo;
	
	}

}