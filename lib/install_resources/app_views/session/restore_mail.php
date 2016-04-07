<h1 style="font-size: 36px; font-weight: 500; line-height: 39.6px; margin: 20px 0 10px 0">Password Reset Confirmation</h1>
<p>A request to reset the password of the following user has been recieved lately :</p>
<ul>
	<li><?= $this->_params->get_item('username') ?></li>
</ul>
<p>A restoration link has been created for this user. The link is valid for the next 24 hours following the restoration request. Unless you issued the request yourself, please ignore this message.</p>
<a href="<?= $this->_params->get_item('link') ?>" style="display: block; margin-bottom: 0; text-align: center; color: white; background-color: #337AB7; border: 1px solid #2E6DA4; border-radius: 4px; padding: 15px 15px; font-size: 18px; font-weight: 400; line-height: 24px; text-decoration: none">Reset Your Password Now!</a>
<hr style="border: 0px solid #000;border-top: 1px solid #EEE; margin: 20px 0 20px 0;" />
<p style="text-align: center">
	<a href="<?= url_helper()->path(''); ?>"><?= apine_app_config()->get('application', 'title') ?></a> - <strong>DO NOT REPLY</strong>
</p>