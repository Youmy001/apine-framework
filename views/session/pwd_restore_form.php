<?php 
global $username;
global $email;
?>
<div class="span6 offset3">
    <div class="form-box well no-shadow">
        <small class="pull-right text-error">*<?php echo FORM_MANDATORY;?></small>
        <h3 class="xt-big title_font"><?php echo $name;?></h3>
        <br>
        <?php if(isset($_GET['error'])){?>
            <div class="alert alert-quote alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4><strong><?php echo APP_WARNING;?>!</strong></h4>
                <span><?php echo $error; ?></span>
            </div>
        <?php }else if(isset($_GET['code'])){?>
            <div class="alert alert-quote alert-success">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4><strong><?php echo APP_SUCCESS;?>!</strong></h4>
                <span><?php echo RESTORE_CODE_SUCCESS;?></span>
            </div>
        <?php }?>
        <div class="row-fluid">
            <form id="content" class="span12" action="<?php echo $session->path("login/restore",false);?>" method="post">
                <?php if(isset($_GET['action'])&&$_GET['action']=="reset"){?>
                <div class="control-group">
                    <label class="control-label"><?php echo USER_MAIL;?><span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="span12" type="email" name="email" placeholder="<?php echo USER_MAIL;?>" value="<?php echo $email;?>" required/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"><?php echo USER_NAME;?><span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="span12" type="text" name="user" placeholder="<?php echo USER_NAME;?>" value="<?php echo $username;?>" required />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">New <?php echo USER_PSWD;?><span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="span12" type="password" name="pwd" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" placeholder="<?php echo USER_PSWD;?>" title="Enter a password with at least one of each (Uppercase, Lowercase and Numeral) and having at least 8 characters" required />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">New <?php echo USER_PSWD_CONFIRM;?><span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="span12" type="password" name="pwd_confirm" placeholder="<?php echo USER_PSWD;?>" required />
                    </div>
                </div>
                <?php }else{?>
                <div class="control-group">
                    <label class="control-label"><?php echo USER_MAIL;?><span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="span12" type="email" name="email" placeholder="<?php echo USER_MAIL_EXEMPLE;?>" required/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"><?php echo USER_NAME;?><span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="span12" type="text" name="user" placeholder="<?php echo USER_NAME;?>" required />
                    </div>
                </div>
                <?php }?>
                <input type="hidden" name="redirect" value="<?php echo $action;?>">
                <input type="hidden" name="action" value="<?php echo (isset($_GET['action'])&&$_GET['action']=="code")?"code":"reset";?>">
                <div class="control-group">
                    <div class="controls">
                        <a href="<?php echo $session->path("login")?>" class="btn btn pull-left"><?php echo FORM_CANCEL;?></a>
                        <button type="submit" class="btn btn-primary pull-right"><?php echo FORM_RESTORE;?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>