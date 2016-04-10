
	<!-- content section -->
	<div class="col-md-4 col-md-offset-4">

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Sign In</h3>
			</div>
			<?php if ($this->_params->get_item('error_code')) :?>
			<?php 	if ($this->_params->get_item('error_code') == 200) :?>
			<div class="alert alert-block alert-success" style="margin:0;border-radius:0;">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4><strong><span class="glyphicon glyphicon-ok"></span>&nbsp;Success !</strong></h4>
				<span><?php echo $this->_params->get_item('error_message'); ?></span>
			</div>
			<?php else :?>
			<div class="alert alert-block alert-warning" style="margin:0;border-radius:0;">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4><strong><span class="glyphicon glyphicon-warning-sign"></span>&nbsp;Warning !</strong></h4>
				<span><?php echo $this->_params->get_item('error_message'); ?></span>
			</div>
			<?php endif;?>
			<?php endif;?>
			<div class="panel-body">
				<form id="content" action="<?= apine_url_helper()->path("login");?>"
					method="post">
					<div class="form-group">
						<label class="control-label">Username or Email Address</label>
						<input class="form-control" type="text" name="user"
								placeholder="Username" required />
					</div>
					<div class="form-group">
						<label class="control-label">Password</label>
						<input class="form-control" type="password" name="pwd"
								placeholder="Password" required />
					</div>
					<div class="checkout">
						<label>
							<input type="checkbox" name="perm" value="on"> Keep Me Signed In
						</label>
						<button type="submit" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-log-in"></span> Sign In</button>
					</div>
					<a href="<?= apine_url_helper()->path("login/restore");?>">Forgot Your Password?</a>
				</form>
			</div>
		</div>

	</div>