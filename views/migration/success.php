<div class="col-md-8 col-md-offset-2">
	<h1>APIne Framework is now ready</h1>
	<p class="lead">Your installation of APIne Framework has been successfully upgraded.</p>
	<hr>
	<?php
		if (!is_null($this->_params->get_item('users')) && $this->_params->get_item('users')->length() > 0) {
			foreach ($this->_params->get_item('users') as $user) {
	?>
	<p>The new password for the following users is : <strong><?= $this->_params->get_item('password'); ?></strong></p>
	<p><?= $this->_params->get_item('message'); ?></p>
	<p>Users whom the password was modified :</p>
	<ul>
		<li><?= $user->get_username(); ?></li>
	</ul>
	<?php
			}
		}
	?>
</div>
<a href="<?= ApineURLHelper::path('') ?>" class="btn btn-primary btn-lg col-md-4 col-md-offset-4"><span class="glyphicon glyphicon-home"></span>&nbsp;Return to Home</a>
