
	<!-- content section -->
	<div class="col-md-4 col-md-offset-4">

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?= Translator::translate('en-US', 'login', 'title') ?></h3>
			</div>
			<?php if ($this->_params->get_item('error_code')) :?>
			<div class="alert alert-block alert-warning" style="margin:0;border-radius:0;">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4><strong><?= Translator::translate('en-US', 'form', 'warning') ?></strong></h4>
				<span><?php echo $this->_params->get_item('error_message'); ?></span>
			</div>
			<?php endif;?>
			<div class="panel-body">
				<form id="content" action="<?php echo URL_Helper::path("login",false);?>"
					method="post">
					<div class="form-group">
						<label class="control-label"><?= Translator::translate('en-US', 'login', 'username_email') ?></label>
						<input class="form-control" type="text" name="user"
								placeholder="<?= Translator::translate('en-US', 'login', 'username') ?>" required />
					</div>
					<div class="form-group">
						<label class="control-label"><?= Translator::translate('en-US', 'login', 'password') ?></label>
						<input class="form-control" type="password" name="pwd"
								placeholder="<?= Translator::translate('en-US', 'login', 'password') ?>" required />
					</div>
					<div class="checkout">
						<label>
							<input type="checkbox" name="perm" value="on"> <?= Translator::translate('en-US', 'login', 'remember') ?>
						</label>
						<button type="submit" class="btn btn-primary pull-right"><?= Translator::translate('en-US', 'login', 'submit') ?></button>
					</div>
					<a href="<?php echo URL_Helper::path("login/restore");?>"><?= Translator::translate('en-US', 'login', 'forgot') ?></a>
				</form>
			</div>
		</div>

	</div>