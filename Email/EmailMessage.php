<?php
/**
 * Email Message
 * Prepare and send an Email message via a mail server
 * 
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Email;

use \PHPMailer as PHPMailer;
use Apine\Application as Application;
use Apine\Exception\GenericException as GenericException;

/**
 * Prepare and send an Email message
 * 
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @uses PHPMailer
 */
class EmailMessage {

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
			throw new GenericException('PHPMailer not found. Please add "PHPMailer/PHPMailer" to your Commposer installation.', 500);
		}
		
		$application = Application\Application::get_instance();
		$config = $application->get_config();

		$this->mail = new PHPMailer();
		$this->mail->CharSet = 'utf-8';
		$this->mail->Encoding = 'base64';
		$this->mail->SMTPDebug = $application->get_mode() == APINE_MODE_DEVELOPMENT ? 3 : 0;
		
		$this->mail->isSMTP();
		$this->mail->Host = $config->get('mail', 'host');
		$this->mail->SMTPAuth = (bool) $config->get('mail', 'smtp_auth');
		$this->mail->Username = $config->get('mail', 'smtp_username');
		$this->mail->Password = $config->get('mail', 'smtp_password');
		$this->mail->SMTPSecure = $config->get('mail', 'protocol');
		$this->mail->Port = $config->get('mail', 'port');
		
		$this->mail->setFrom($config->get('mail', 'sender_address'), $config->get('mail', 'sender_name'));
		// $this->mail->WordWrap = 50; // Max word lenght
		$this->mail->isHTML(true);
	
	}
	
	/**
	 * Set sender information
	 * 
	 * @param string $a_address
	 * @param string $a_name
	 * @throws ApineException If invalid email address
	 */
	public function set_sender($a_address, $a_name = null) {
		
		if (filter_var($a_address, FILTER_VALIDATE_EMAIL)) {
			$this->mail->setFrom($a_address, $a_name);
		} else {
			throw new GenericException('Invalid Email Format', 500);
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
	 * Add a CC recipient for the email
	 * 
	 * @param string $a_mail
	 * @param string $a_name
	 */
	public function add_cc ($a_mail, $a_name = null) {
	
		$this->mail->addCC($a_mail, $a_name);
	
	}
	
	/**
	 * Add a BCC recipient for the email
	 * 
	 * @param string $a_mail
	 * @param string $a_name
	 */
	public function add_bcc ($a_mail, $a_name = null) {
		
		$this->mail->addBCC($a_mail, $a_name);
		
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
