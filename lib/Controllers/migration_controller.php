<?php

class MigrationController extends ApineController {
	
	private $available_migration = ['rc1' => '1.0.0-dev.8.6 (rc1)', 'rc2' => '1.0.0-dev.11.9 (rc2)'];
	
	public function index ($params) {
		
		$this->_view->set_title('Migrations');
		$this->_view->set_view('migration/form');
		$this->_view->set_param('versions', $this->available_migration);
		
		if (ApineRequest::is_get()) {
			if (is_null(ApineConfig::get('runtime', 'encryption_key'))) {
				$this->_view->set_param('check_reset', true);
			} else {
				$this->_view->set_param('check_reset', false);
			}
			
			$this->_view->set_response_code(200);
		} else if (ApineRequest::is_post()) {
			$action = $params['version'];
			
			if (method_exists($this, $action)) {
				$this->$action($params);
			} else {
				$this->_view->set_param('error_code', 404);
				$this->_view->set_param('error_message', 'Migration not found');
				//throw new ApineException('Migration not found', 404);
			}
		}
		
		return $this->_view;
		
	}
	
	public function rc1($params) {
		
		try {
			if (ApineRequest::is_post()) {
				if (isset($params['reset']) && $params['reset'] === 'false') {
					$reset = false;
				} else {
					$reset = true;
				}
			
				$database = new ApineDatabase();
				$message = "";
			
				// Drop useless apine_image table
				$database->exec('DROP TABLE apine_images;');
			
				// Create new tables an relations
				$database->exec('CREATE TABLE IF NOT EXISTS `apine_api_users_tokens` (
				`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
				`user_id` int(11) NOT NULL,
				`token` varchar(64) NOT NULL,
				`origin` varchar(256) NOT NULL,
				`creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`last_access_date` timestamp NULL,
				`disabled` tinyint(1) NOT NULL DEFAULT 0
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
			
				$database->exec('ALTER TABLE `apine_api_users_tokens`
				ADD KEY `user_id` (`user_id`);');
			
				$database->exec('ALTER TABLE `apine_api_users_tokens`
				ADD CONSTRAINT `apine_api_users_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `apine_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;');
			
				$database->exec('CREATE TABLE IF NOT EXISTS `apine_password_tokens` (
				`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
				`user_id` int(11) NOT NULL,
				`token` varchar(64) NOT NULL,
				`creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
			
				$database->exec('ALTER TABLE `apine_password_tokens`
				ADD KEY `apine_password_token_user_id` (`user_id`);');
			
				$database->exec('ALTER TABLE `apine_password_tokens`
				ADD CONSTRAINT `apine_password_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `apine_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;');
			
				// Delete old system controllers
				unlink('controllers/session_controller.php');
				unlink('controllers/error_controller.php');
			
				// Add missing entries in the config file
				if (is_null(ApineConfig::get('application', 'default_layout'))) {
					ApineConfig::set('application', 'default_layout', 'default');
				}
			
				if (is_null(ApineConfig::get('runtime', 'mode'))) {
					if (!is_null(ApineConfig::get('apine-framework', 'mode'))) {
						ApineConfig::set('runtime', 'mode', ApineConfig::get('apine-framework', 'mode'));
					} else {
						ApineConfig::set('runtime', 'mode', 'development');
					}
				}
			
				if (is_null(ApineConfig::get('runtime', 'secure_session'))) {
					ApineConfig::set('runtime', 'secure_session', 'yes');
				}
			
				if (is_null(ApineConfig::get('runtime', 'token_lifespan'))) {
					ApineConfig::set('runtime', 'token_lifespan', '600');
				}
			
				if (is_null(ApineConfig::get('runtime', 'use_api'))) {
					ApineConfig::set('runtime', 'use_api', 'yes');
				}
			
				// Reset Password of user created before the creation of the encryption key
				// If the encryption key does not exists reset every password
				if ($reset) {
					$key_exists = (bool) ApineConfig::get('runtime', 'encryption_key');
					$new_password = PseudoCrypt::hash(intval(rand(1, 1000)), 10);
					$all_users = ApineUserFactory::create_all();
					$user_found = false;
			
					if (!is_null(ApineConfig::get('mail', 'host'))) {
						$content = '<h1 style="font-size: 36px; font-weight: 500; line-height: 39.6px; margin: 20px 0 10px 0">Password Reset</h1>
		<p>Following a major system upgrade on ' . ApineConfig::get('applicattion', 'title') . ', your password has been temporarily modified. This upgrade made major improvements on security on the system overall and a new password was necessary.</p>
		<p>Your new password is : ' . $new_password . '</p>
		<p>Unless you are using ' . ApineConfig::get('applicattion', 'title') . ', please ignore this message.</p>
		<p>Following the link bellow, it is possible change anytime this new password.</p>
		<a href="' . ApineURLHelper::path('login/restore', APINE_PROTOCOL_HTTPS) . '" style="display: block; margin-bottom: 0; text-align: center; color: white; background-color: #337AB7; border: 1px solid #2E6DA4; border-radius: 4px; padding: 15px 15px; font-size: 18px; font-weight: 400; line-height: 24px; text-decoration: none">Change Your Password Now</a>
		<hr style="border: 0px solid #000;border-top: 1px solid #EEE; margin: 20px 0 20px 0;" />
		<p style="text-align: center">
			<a href="' . ApineURLHelper::path('') . '">' . ApineConfig::get('application', 'title') . '</a> - <strong>DO NOT REPLY</strong>
		</p>';
						$content_alt = '-- ' . ApineAppTranslator::translate('restore_mail','donotreply') . ' --\r\n\r\nFollowing a major system upgrade on ' . ApineConfig::get('applicattion', 'title') . ', your password has been temporarily modified. This upgrade made major improvements on security on the system overall and a new password was necessary.\r\nYour new password is : ' . $new_password . '\r\nIt is possible change anytime this new password following this link : ' . ApineURLHelper::path('login/restore', APINE_PROTOCOL_HTTPS) . '\r\n\r\nUnless you are using ' . ApineConfig::get('applicattion', 'title') . ', please ignore this message.';
						$mail = new ApineEmailMessage();
						$mail->set_subject('APIne Password Reset Notice');
						$mail->set_content($content);
						$mail->set_alt_content($content_alt);
					} else {
						$message .= "A mail server is not configured.\r\n";
					}
			
					/*print "Passwords Successfully reinitialized.\r\n";
					print "The new password is : " . $new_password . "\r\n";*/
					$this->_view->set_param('password', $new_password);
					$view_users = new ApineCollection();
			
					if ($all_users->length() > 0) {
						foreach ($all_users as $user) {
							if (!$key_exists) {
								$user->set_password(ApineEncryption::hash_password($new_password));
								$user->save();
									
								if (isset($mail)) {
									$mail->add_bcc($user->get_email_address(), $user->get_username());
								}
									
								$user_found = true;
								//print "Will send the new password to " . $user->get_username() . ' &lt;' . $user->get_email_address() . '&gt;' . "\r\n";
								$view_users->add_item($user);
							}
						}
						
						$this->_view->set_param('users', $view_users);
							
						//print "Sending password to users\r\n";
			
						if ($user_found) {
							if (isset($mail) && !$mail->send()) {
								$message .= "You need contact the users by yourself to tell them the new password.";
								$message .= $mail->get_error() . "\r\n";
							} else if (!isset($mail)) {
								$message .= "You need contact your users to tell them the new password.";
							} else {
								$message .= 'A mail has been sent to every concerned users.';
							}
						}
					} else {
						$this->_view->set_param('users', $view_users);
					}
				} else {
					//print "Password Reinitialization cancelled\r\n";
				}
				
				$this->_view->set_param('message', $message);
			
				//print "APIne Framework assets has been successfuly upgraded. Exiting...\r\n";
				//throw new ApineException("APIne Framework assets has been successfuly upgraded.", 200);
				$this->_view->set_view('migration/success');
			} else {
				header('Location: ' . ApineURLHelper::path('migration', APINE_PROTOCOL_HTTPS));
			}
		} catch (Exception $e) {
			//print "{$e->getMessage()} in {$e->getFile()} line {$e->getLine()}.\r\n\r\n{$e->getTraceAsString()}\r\n";
			throw new ApineException($e->getMessage(), $e->getCode(), $e);
		}
		
	}
	
}