
	<!-- content section -->
	<div class="col-md-4 col-md-offset-4">

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?= apine_app_translator()->translate('restore','alt_title'); ?></h3>
			</div>
			<?php if ($this->_params->get_item('error_code')) :?>
			<?php 	if ($this->_params->get_item('error_code') == 200) :?>
			<div class="alert alert-block alert-success" style="margin:0;border-radius:0;">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4><strong><span class="glyphicon glyphicon-ok"></span>&nbsp;<?= apine_app_translator()->translate('form','success'); ?></strong></h4>
				<span><?php echo $this->_params->get_item('error_message'); ?></span>
			</div>
			<?php else :?>
			<div class="alert alert-block alert-warning" style="margin:0;border-radius:0;">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4><strong><span class="glyphicon glyphicon-warning-sign"></span>&nbsp;<?= apine_app_translator()->translate('form','warning'); ?></strong></h4>
				<span><?php echo $this->_params->get_item('error_message'); ?></span>
			</div>
			<?php endif;?>
			<?php endif;?>
			<div class="panel-body">
				<form id="content" action="<?= apine_url_helper()->path("login/restore");?>"
					method="post">
					<div class="form-group">
						<label class="control-label"><?= apine_app_translator()->translate('restore','username'); ?></label>
						<input class="form-control" type="text" name="user"
								placeholder="<?= apine_app_translator()->translate('restore','username'); ?>" required="required" />
					</div>
					<div class="form-group">
						<label class="control-label"><?= apine_app_translator()->translate('restore','email'); ?></label>
						<input class="form-control" type="email" name="email"
								placeholder="<?= apine_app_translator()->translate('restore','email'); ?>" required="required" />
					</div>
					<div class="checkout">
						<input type="hidden" name="action" value="code" />
						<a class="btn btn-default pull-left" href="<?= apine_url_helper()->path('login'); ?>"><?= apine_app_translator()->translate('form', 'cancel'); ?></a>
						<button type="submit" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-envelope"></span> <?= apine_app_translator()->translate('form','send'); ?></button>
					</div>
				</form>
			</div>
		</div>

	</div>