<div class="col-md-4 col-md-offset-4">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><?= apine_app_config()->get('application', 'title'); ?> Versions</h3>
		</div>
		<div class="panel-body">
			<dl>
				<dt>APIne Framework</dt>
				<dd><?= apine_application()->get_version()->framework(); ?></dd>
			</dl>
			<dl>
				<dt>Application</dt>
				<dd><?= apine_application()->get_version()->application(); ?></dd>
			</dl>
		</div>
	</div>
</div>