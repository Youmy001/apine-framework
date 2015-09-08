
	<!-- content section -->
	<div class="col-md-4 col-md-offset-4">

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?= ApineTranslator::translate('register','title'); ?></h3>
			</div>
			<?php if ($this->_params->get_item('error_code')) :?>
			<div class="alert alert-block alert-warning" style="margin:0;border-radius:0;">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4><strong><?= ApineTranslator::translate('form','warning'); ?></strong></h4>
				<span><?php echo $this->_params->get_item('error_message'); ?></span>
			</div>
			<?php endif;?>
			<div class="panel-body">
				<form id="content" action="<?php echo URL_Helper::path("register",false);?>"
					method="post">
					<div class="form-group">
						<label class="control-label"><?= ApineTranslator::translate('register','username'); ?></label>
						<input class="form-control" type="text" name="user" placeholder="<?= ApineTranslator::translate('register','username'); ?>" required />
					</div>
					<div class="form-group">
						<label class="control-label"><?= ApineTranslator::translate('register','password'); ?></label>
						<input class="form-control" type="password" name="pwd" placeholder="<?= ApineTranslator::translate('register','password'); ?>" required />
					</div>
					<div class="form-group">
						<label class="control-label"><?= ApineTranslator::translate('register','password_confirm'); ?></label>
						<input class="form-control" type="password" name="pwd_confirm" placeholder="<?= ApineTranslator::translate('register','password'); ?>" required />
					</div>
					<div class="form-group">
						<label class="control-label"><?= ApineTranslator::translate('register','email'); ?></label>
						<input class="form-control" type="email" name="email" placeholder="example@example.com"/>
					</div>
					<button type="submit" class="btn btn-primary pull-right"><?= ApineTranslator::translate('register','submit'); ?></button>
				</form>
			</div>
		</div>
	</div>