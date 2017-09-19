<?php
/**
 * Password Restoration Controller
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Controllers\User;

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
        
        if (null == ($title = Application\Translator::translate('restore', 'title'))) {
            $title = 'Reset Password';
        }
        
        $view = new MVC\HTMLView();
        $view->set_title($title);
        $view->set_view('session' . DIRECTORY_SEPARATOR . 'restore');
        $view->set_response_code(200);
        
        return $view;
        
    }
    
    public function code ($params) {
        
        if (!Core\Request::is_post()) {
            throw new GenericException('Method Not Allowed', 405);
        }
        
        $view = new MVC\HTMLView();
        
        try{
            if (null == ($title = Application\Translator::translate('restore', 'title'))) {
                $title = 'Reset Password';
            }
            
            $view->set_title($title);
            $view->set_view('session/restore');
            
            // Validate Entries
            if (!User\Factory\UserFactory::is_name_exist($params['user']) && !User\Factory\UserFactory::is_email_exist($params['email'])) {
                // error to the reset request view
                if (null == ($message = Application\Translator::translate('restore', 'send_bad'))) {
                    $message = 'Bad username or email';
                }
                
                throw new GenericException($message, 500);
            }
            
            // Instanciate User
            $user_by_name = User\Factory\UserFactory::create_by_name($params['user']);
            $user_by_email = User\Factory\UserFactory::create_by_name($params['email']);
            
            // Validate User
            if ((is_null($user_by_name) || is_null($user_by_email)) || ($user_by_email->get_id() != $user_by_name->get_id())) {
                // error to the reset request view
                if (null == ($message = Application\Translator::translate('restore', 'send_bad'))) {
                    $message = 'Bad username or email';
                }
                
                throw new GenericException($message, 500);
            }
            
            // Generate a token for user
            $token = Core\Encryption::token();
            $restore_url = MVC\URLHelper::path('login/restore/' . $token);
            $password_token = new User\PasswordToken();
            $password_token->set_user($user_by_name);
            $password_token->set_token($token);
            
            // Mail HTML Content
            $mail_view =  new MVC\HTMLView('','session' . DIRECTORY_SEPARATOR . 'restore_mail','mail');
            $mail_view->set_param('username', $user_by_name->get_username());
            $mail_view->set_param('link', $restore_url);
            
            // Alternate Plain Text Content
            $mail_alt_content = '-- DO NOT REPLY --
				
	A request to reset the password of the following user has been recieved lately :
		
	 - ' . $user_by_name->get_username() . '
		
	 A restoration link has been created for this user. The link is valid for the next 24 hours following the restoration request. Unless you issued the request yourself, please ignore this message.
		
	 Use the following link to reset your password:' . $restore_url;
            
            // Create the Mail message to send
            $mail = new Email\EmailMessage();
            $mail->add_recipient($params['email']);
            $mail->set_subject("APIne Password Reset Confirmation");
            $mail->set_content($mail_view->content());
            $mail->set_alt_content($mail_alt_content);
            
            
            // Try to send message to recipient
            if (!$mail->send()) {
                // Attempt Failed
                if (null == ($message = Application\Translator::translate('restore', 'send_fail'))) {
                    $message = 'Failed to connect to mail server';
                }
                
                throw new GenericException($message, 500);
            } else {
                // Attempt Successful
                $password_token->save();	// Save token
                if (null == ($message = Application\Translator::translate('restore', 'send_success'))) {
                    $message = 'A confirmation message has been sent to your inbox. Follow the link inside to reset your password. It may arrive within few minutes.';
                }
                
                throw new GenericException($message, 200);
            }
        } catch (GenericException $e) {
            // Catch Message to display in the form
            $view->set_param('error_code', $e->getCode());
            $view->set_param('error_message', $e->getMessage());
        } catch (Exception $e) {
            throw new GenericException($e->getMessage(), $e->getCode(), $e);
        }
        
        $view->set_response_code(200);
        return $view;
        
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
        
        if (null == ($title = Application\Translator::translate('restore', 'title'))) {
            $title = 'Reset Password';
        }
        
        $view = new MVC\HTMLView();
        
        $view->set_title($title);
        $view->set_view('session' . DIRECTORY_SEPARATOR . 'restore');
        
        try {
            
            if (!User\Factory\PasswordTokenFactory::is_token_exist($params[0])) {
                if (null == ($message = Application\Translator::translate('restore', 'token_notfound'))) {
                    $message = 'Token not found';
                }
                
                throw new GenericException($message, 404);
            } else if (!User\Factory\PasswordTokenFactory::is_token_valid($params[0])) {
                if (null == ($message = Application\Translator::translate('restore', 'token_expired'))) {
                    $message = 'Token is expired';
                }
                
                throw new GenericException($message, 410);
            }
            
            $view->set_view('session' . DIRECTORY_SEPARATOR . 'restore_token');
            //print "Password reset form";
            $token = User\Factory\PasswordTokenFactory::create_by_token($params[0]);
            $view->set_param('token', array("token" => $token->get_token(), "username" => $token->get_user()->get_username(), "email" => $token->get_user()->get_email_address()));
        } catch (GenericException $e) {
            // Catch Message to display in the form
            $view->set_param('error_code', $e->getCode());
            $view->set_param('error_message', $e->getMessage());
        } catch (Exception $e) {
            throw new GenericException($e->getMessage(), $e->getCode(), $e);
        }
        
        //print $params[0];
        $view->set_response_code(200);
        return $view;
        
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
        
        if (null == ($title = Application\Translator::translate('restore', 'title'))) {
            $title = 'Reset Password';
        }
        
        $view = new MVC\HTMLView();
        $view->set_title($title);
        $view->set_view('session' . DIRECTORY_SEPARATOR . 'restore');
        
        try{
            if (!User\Factory\PasswordTokenFactory::is_token_exist($params[0])) {
                if (null == ($message = Application\Translator::translate('restore', 'token_notfound'))) {
                    $message = 'Token not found';
                }
                
                throw new GenericException($message, 404);
            } else if (!User\Factory\PasswordTokenFactory::is_token_valid($params[0])) {
                if (null == ($message = Application\Translator::translate('restore', 'token_expired'))) {
                    $message = 'Token is expired';
                }
                
                throw new GenericException($message, 410);
            }
            
            $view->set_view('session' . DIRECTORY_SEPARATOR . 'restore_token');
            
            $token = User\Factory\PasswordTokenFactory::create_by_token($params[0]);
            $user = $token->get_user();
            $view->set_param('token', $token);
            
            // Verify both passwords are identical
            if (($params['password'] === $params['password_confirm'])) {
                $encoded_pwd = Core\Encryption::hash_password($params['password']);
            } else {
                if (null == ($message = Application\Translator::translate('restore', 'password_wrong'))) {
                    $message = 'The password does not match the confirmation.';
                }
                
                throw new GenericException($message, 500); // Wrong password
            }
            
            // Replace user's password
            $user->set_password($encoded_pwd);
            $user->save();
            
            // Delete used token
            $token->delete();
            
            $view->set_view('session' . DIRECTORY_SEPARATOR . 'login');
            if (null == ($message = Application\Translator::translate('restore', 'restore_success'))) {
                $message = 'Password restoration successful. You can now sign in with your new password.';
            }
            
            throw new GenericException($message, 200);
        } catch (GenericException $e) {
            // Catch Message to display in the form
            $view->set_param('error_code', $e->getCode());
            $view->set_param('error_message', $e->getMessage());
        } catch (Exception $e) {
            throw new GenericException($e->getMessage(), $e->getCode(), $e);
        }
        
        $view->set_response_code(200);
        return $view;
        
    }
    
}