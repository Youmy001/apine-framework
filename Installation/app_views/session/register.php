
	<!-- content section -->
	<div class="col-md-4 col-md-offset-4">

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Sign Up</h3>
			</div>
			<?php if ($this->_params->get_item('error_code')) :?>
			<div class="alert alert-block alert-warning" style="margin:0;border-radius:0;">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4><strong><span class="glyphicon glyphicon-warning-sign"></span>&nbsp;Warning !</strong></h4>
				<span><?php echo $this->_params->get_item('error_message'); ?></span>
			</div>
			<?php endif;?>
			<div class="panel-body">
				<form id="content" action="<?= apine_url_helper()->path("register");?>"
					method="post">
					<div class="form-group">
						<label class="control-label">Username</label>
						<input class="form-control" type="text" name="user" placeholder="Username" required />
					</div>
					<div class="form-group">
						<label class="control-label">Password</label>
						<input class="form-control" type="password" name="pwd" placeholder="Password" required />
					</div>
					<div class="form-group">
						<label class="control-label">Confirm Password</label>
						<input class="form-control" type="password" name="pwd_confirm" placeholder="Password" required />
					</div>
					<div class="form-group">
						<label class="control-label">Email Address</label>
						<input class="form-control" type="email" name="email" placeholder="example@example.com"/>
					</div>
					<button type="submit" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-check"></span> Sign Up</button>
				</form>
			</div>
		</div>
	</div>