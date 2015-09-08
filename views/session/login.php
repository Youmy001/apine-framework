
	<!-- content section -->
	<div class="col-md-4 col-md-offset-4">

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?= ApineTranslator::translate('login','title'); ?></h3>
			</div>
			<?php if ($this->_params->get_item('error_code')) :?>
			<div class="alert alert-block alert-warning" style="margin:0;border-radius:0;">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4><strong><?= ApineTranslator::translate('form','warning'); ?></strong></h4>
				<span><?php echo $this->_params->get_item('error_message'); ?></span>
			</div>
			<?php endif;?>
			<div class="panel-body">
				<form id="content" action="<?= URL_Helper::path("login",false);?>"
					method="post">
					<div class="form-group">
						<label class="control-label"><?= ApineTranslator::translate('login','username_email'); ?></label>
						<input class="form-control" type="text" name="user"
								placeholder="<?= ApineTranslator::translate('login','username'); ?>" required />
					</div>
					<div class="form-group">
						<label class="control-label"><?= ApineTranslator::translate('login','password'); ?></label>
						<input class="form-control" type="password" name="pwd"
								placeholder="<?= ApineTranslator::translate('login','password'); ?>" required />
					</div>
					<div class="checkout">
						<label>
							<input type="checkbox" name="perm" value="on"> <?= ApineTranslator::translate('login','remember'); ?>
						</label>
						<button type="submit" class="btn btn-primary pull-right"><?= ApineTranslator::translate('login','submit'); ?></button>
					</div>
					<a href="<?= URL_Helper::path("login/restore");?>"><?= ApineTranslator::translate('login','forgot'); ?></a>
				</form>
			</div>
		</div>

	</div>