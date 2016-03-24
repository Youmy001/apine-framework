<?php
/**
 * Password Restoration Controller
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Controllers\System;

use \Exception as Exception;
use Apine\Application as Application;
use Apine\Core as Core;
use Apine\Email as Email;
use Apine\MVC as MVC;
use Apine\User as User;
use Apine\Exception\GenericException as GenericException;

class RestoreController extends MVC\Controller {
	
	public function index () {
		
		if (!Core\Request::is_get()) {
			throw new GenericException('Method Not Allowed', 405);
		}
		
		$this->_view->set_title(Application\ApplicationTranslator::translate('restore', 'title'));
		$this->_view->set_view('session/restore');
		$this->_view->set_response_code(200);
		
		return $this->_view;
		
	}
	
	public function code ($params) {
		
		if (!Core\Request::is_post()) {
			throw new GenericException('Method Not Allowed', 405);
		}
		
		try{
			$this->_view->set_title(Application\ApplicationTranslator::translate('restore', 'title'));
			$this->_view->set_view('session/restore');
				
			// Validate Entries
			if (!User\Factory\UserFactory::is_name_exist($params['user']) && !User\Factory\UserFactory::is_email_exist($params['email'])) {
				// error to the reset request view
				throw new GenericException(Application\ApplicationTranslator::translate('restore', 'send_bad'), 500);
			}
				
			// Instanciate User
			$user_by_name = User\Factory\UserFactory::create_by_name($params['user']);
			$user_by_email = User\Factory\UserFactory::create_by_name($params['email']);
				
			// Validate User
			if ((is_null($user_by_name) || is_null($user_by_email)) || ($user_by_email->get_id() != $user_by_name->get_id())) {
				// error to the reset request view
				throw new GenericException(Application\ApplicationTranslator::translate('restore', 'send_bad'), 500);
			}
				
			// Generate a token for user
			$token = Core\Encryption::token();
			$restore_url = MVC\URLHelper::path('login/restore/' . $token);
			/*$mail_token_entity = new ApineEntity('apine_email_tokens');
			$mail_token_entity->set_field('user_id', $user_by_name->get_id());
			$mail_token_entity->set_field('token', $token);
			$mail_token_entity->set_field('action', 1); // Action 1 for password reset*/
			$password_token = new User\PasswordToken();
			$password_token->set_user($user_by_name);
			$password_token->set_token($token);
				
			// Mail HTML Content
			$mail_view =  new MVC\HTMLView('','session/restore_mail','mail');
			$mail_view->set_param('username', $user_by_name->get_username());
			$mail_view->set_param('link', $restore_url);
				
			// Alternate Plain Text Content
			$mail_alt_content = '-- ' . Application\ApplicationTranslator::translate('restore_mail','donotreply') . ' --
				
	' . Application\ApplicationTranslator::translate('restore_mail','description') . '
				
	 - ' . $user_by_name->get_username() . '
				
	 ' . Application\ApplicationTranslator::translate('restore_mail','warning') . '
				
	 ' . Application\ApplicationTranslator::translate('restore_mail','follow') . $restore_url;
				
			// Create the Mail message to send
			$mail = new Email\EmailMessage();
			$mail->add_recipient($params['email']);
			$mail->set_subject(Application\ApplicationTranslator::translate('restore_mail', 'subject'));
			$mail->set_content($mail_view->content());
			$mail->set_alt_content($mail_alt_content);
				
				
			// Try to send message to recipient
			if (!$mail->send()) {
				// Attempt Failed
				throw new GenericException(Application\ApplicationTranslator::translate('restore', 'send_fail'), 500);
			} else {
				// Attempt Successful
				$password_token->save();	// Save token
				throw new GenericException(Application\ApplicationTranslator::translate('restore', 'send_success'), 200);
			}
		} catch (GenericException $e) {
			// Catch Message to display in the form
			$this->_view->set_param('error_code', $e->getCode());
			$this->_view->Set_param('error_message', $e->getMessage());
		} catch (Exception $e) {
			throw new GenericException($e->getMessage(), $e->getCode(), $e);
		}
		
		$this->_view->set_response_code(200);
		return $this->_view;
		
	}
	
	public function reset ($params) {
		
		if (!Core\Request::is_get()) {
			throw new GenericException('Method Not Allowed', 405);
		}
		
		if (count($params) !== 1) {
			throw new GenericException('Bad Request', 400);
		}
		
		if (is_string($params[0]) && strlen($params[0]) !== 64) {
			throw new GenericException('Bad Request', 400);
		}
		
		$this->_view->set_title(Application\ApplicationTranslator::translate('restore', 'title'));
		$this->_view->set_view('session/restore');
		
		try {
			
			if (!User\Factory\PasswordTokenFactory::is_token_exist($params[0])) {
				throw new GenericException(Application\ApplicationTranslator::translate('restore', 'token_notfound'), 404);
			} else if (!User\Factory\PasswordTokenFactory::is_token_valid($params[0])) {
				throw new GenericException(Application\ApplicationTranslator::translate('restore', 'token_expired'), 410);
			}
			
			$this->_view->set_view('session/restore_token');
			//print "Password reset form";
			$token = User\Factory\PasswordTokenFactory::create_by_token($params[0]);
			$this->_view->set_param('token', $token);
		} catch (GenericException $e) {
			// Catch Message to display in the form
			$this->_view->set_param('error_code', $e->getCode());
			$this->_view->Set_param('error_message', $e->getMessage());
		} catch (Exception $e) {
			throw new GenericException($e->getMessage(), $e->getCode(), $e);
		}
		
		//print $params[0];
		$this->_view->set_response_code(200);
		return $this->_view;
		
	}
	
	public function restore ($params) {
		
		if (!Core\Request::is_post()) {
			throw new GenericException('Method Not Allowed', 405);
		}
		
		if (count($params) !== 5) {
			throw new GenericException('Bad Request', 400);
		}
		
		if (is_string($params[0]) && strlen($params[0]) !== 64) {
			throw new GenericException('Bad Request', 400);
		}
		
		if ($params[0] !== $params['token']) {
			throw new GenericException('Bad Request', 400);
		}
		
		$this->_view->set_title(Application\ApplicationTranslator::translate('restore', 'title'));
		$this->_view->set_view('session/restore');
		
		try{
			if (!User\Factory\PasswordTokenFactory::is_token_exist($params[0])) {
				throw new GenericException(Application\ApplicationTranslator::translate('restore', 'token_notfound'), 404);
			} else if (!User\Factory\PasswordTokenFactory::is_token_valid($params[0])) {
				throw new GenericException(Application\ApplicationTranslator::translate('restore', 'token_expired'), 410);
			}
			
			$this->_view->set_view('session/restore_token');
			
			$token = User\Factory\PasswordTokenFactory::create_by_token($params[0]);
			$user = $token->get_user();
			$this->_view->set_param('token', $token);
			
			// Verify both passwords are identical
			if (($params['password'] === $params['password_confirm'])) {
				$encoded_pwd = Core\Encryption::hash_password($params['password']);
			} else {
				throw new GenericException(Application\ApplicationTranslator::translate('restore', 'password_wrong'), 500); // Wrong password
			}
			
			// Replace user's password
			$user->set_password($encoded_pwd);
			$user->save();
			
			// Delete used token
			$token->delete();
			
			$this->_view->set_view('session/login');
			throw new GenericException(Application\ApplicationTranslator::translate('restore', 'restore_success'), 200);
		} catch (GenericException $e) {
			// Catch Message to display in the form
			$this->_view->set_param('error_code', $e->getCode());
			$this->_view->Set_param('error_message', $e->getMessage());
		} catch (Exception $e) {
			throw new GenericException($e->getMessage(), $e->getCode(), $e);
		}
		
		$this->_view->set_response_code(200);
		return $this->_view;
		
	}
	
}