<div class="col-md-4 col-md-offset-4">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><?= apine_app_config()->get('application', 'title'); ?> Versions</h3>
		</div>
		<div class="panel-body">
			<dl>
				<dt>APIne Framework</dt>
				<dd><?= apine_application()->get_version(); ?></dd>
			</dl>
			<dl>
				<dt>Web Application</dt>
				<dd><?= Apine\Core\Version::application(); ?></dd>
			</dl>
		</div>
	</div>
</div>