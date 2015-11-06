
	<!-- content section -->
	<div class="col-md-4 col-md-offset-4">

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?= ApineAppTranslator::translate('login','title'); ?></h3>
			</div>
			<?php if ($this->_params->get_item('error_code')) :?>
			<?php 	if ($this->_params->get_item('error_code') == 200) :?>
			<div class="alert alert-block alert-success" style="margin:0;border-radius:0;">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4><strong><span class="glyphicon glyphicon-ok"></span>&nbsp;<?= ApineAppTranslator::translate('form','success'); ?></strong></h4>
			<?php else :?>
			<div class="alert alert-block alert-warning" style="margin:0;border-radius:0;">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4><strong><span class="glyphicon glyphicon-warning-sign"></span>&nbsp;<?= ApineAppTranslator::translate('form','warning'); ?></strong></h4>
			<?php endif;?>
				<span><?php echo $this->_params->get_item('error_message'); ?></span>
			</div>
			<?php endif;?>
			<div class="panel-body">
				<form id="content" action="<?= ApineURLHelper::path("login",false);?>"
					method="post">
					<div class="form-group">
						<label class="control-label"><?= ApineAppTranslator::translate('login','username_email'); ?></label>
						<input class="form-control" type="text" name="user"
								placeholder="<?= ApineAppTranslator::translate('login','username'); ?>" required />
					</div>
					<div class="form-group">
						<label class="control-label"><?= ApineAppTranslator::translate('login','password'); ?></label>
						<input class="form-control" type="password" name="pwd"
								placeholder="<?= ApineAppTranslator::translate('login','password'); ?>" required />
					</div>
					<div class="checkout">
						<label>
							<input type="checkbox" name="perm" value="on"> <?= ApineAppTranslator::translate('login','remember'); ?>
						</label>
						<button type="submit" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-log-in"></span> <?= ApineAppTranslator::translate('login','submit'); ?></button>
					</div>
					<a href="<?= ApineURLHelper::path("login/restore");?>"><?= ApineAppTranslator::translate('login','forgot'); ?></a>
				</form>
			</div>
		</div>

	</div>