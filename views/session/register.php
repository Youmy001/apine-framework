<div class="row-fluid">
	<!-- content section -->
	<?php session()->register_form(REGISTER_TITLE,"login/registersuccess",$this->_params->get_item('error_message'),$this->_params->get_item('error_code'));?>
	<div class="span6 pull-left">
		<div class="form-box well">
			<h1 class="xt-big title_font"><?php echo REGISTER_PRESENTATION_TITLE;?></h1>
			<p class="lead"><?php echo REGISTER_PRESENTATION_PAR1;?></p>
			<p>
				<a class="effect btn btn-primary btn-xlarge btn-block"
					href="<?php echo session()->path('about')?>"><?php echo ABOUT_LINK_LEARN_MORE;?></a>
			</p>
		</div>
		<br class="hidden-phone"> <br class="hidden-phone">
	</div>
</div>
<section class="container nopage" style="position:fixed;bottom:0;right:0">
	<div class="mark pull-right" style="margin-top:-30px;margin-bottom:-10px;">
		<h3 class="back-link">
			<a id="scroll" href="#footers"><?php echo MENU;?></a>
		</h3>
	</div>
</section>