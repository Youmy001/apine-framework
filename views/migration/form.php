<div class="col-md-8 col-md-offset-2">
<h1>Migration Assistant</h1>
<p class="lead">This assistant will assist you with the process of
	updating your Apine installation. This assistant will update the
	database add missing configuration entries in the configuration file
	and, if necessary, update user passwords.</p>
<hr>
</div>
<form class="col-md-4 col-md-offset-4" method="post"
		action="<?= ApineURLHelper::path('migration', APINE_PROTOCOL_HTTPS); ?>">
		<?php if ($this->_params->get_item('error_code')) :?>
		<?php 	if ($this->_params->get_item('error_code') == 200) :?>
		<div class="alert alert-block alert-success"
			style="margin: 0; border-radius: 0;">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<h4>
				<strong><span class="glyphicon glyphicon-ok"></span>&nbsp;<?= ApineAppTranslator::translate('form','success'); ?></strong>
			</h4>
		<?php else :?>
		<div class="alert alert-block alert-warning"
			style="margin: 0; border-radius: 0;">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<h4>
				<strong><span class="glyphicon glyphicon-warning-sign"></span>&nbsp;<?= ApineAppTranslator::translate('form','warning'); ?></strong>
			</h4>
		<?php endif;?>
			<span><?php echo $this->_params->get_item('error_message'); ?></span>
		</div>
		<br>
		<?php endif;?>
		<div class="form-group">
			<select class="form-control" name="version" required>
				<option disabled selected>Select a version from which you update</option>
			<?php foreach ($this->_params->get_item('versions') as $item=>$name) {?>
			<option value="<?= $item ?>"><?= $name ?></option>
			<?php }?>
		</select>
		</div>
		<hr>
		<h4>Options :</h4>
		<div class="checkbox">
			<label> <input type="checkbox" value="reset" name="reset"
				<?= ($this->_params->get_item('check_reset')) ? 'checked="checked"' : ''; ?> />
				Check to reset passwords
			</label>
		</div>
		<hr>
		<button type="submit" class="btn btn-success btn-block btn-lg">
			<span class="glyphicon glyphicon-refresh"></span> Migrate
		</button>
	</form>