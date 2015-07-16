<?php
global $username;
global $email;
?>
<div class="span6">
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
        <?php }?>
        <div class="row-fluid">
            <form id="content" class="span12" action="<?php echo $session->path("register",false);?>" method="post">
                <div class="control-group">
                    <label class="control-label"><?php echo USER_NAME;?><span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="span12" type="text" name="user" placeholder="<?php echo USER_NAME;?>" required />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"><?php echo USER_REALNAME;?></label>
                    <div class="controls">
                        <input class="span12" type="text" name="realname" placeholder="<?php echo USER_REALNAME;?>" <?php echo (isset($username))?'value="'.$username.'"':"";?>/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"><?php echo USER_PSWD;?><span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="span12" type="password" name="pwd" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" placeholder="<?php echo USER_PSWD;?>" title="Enter a password with at least one of each (Uppercase, Lowercase and Numeral) and having at least 8 characters" required />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"><?php echo USER_PSWD_CONFIRM;?><span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="span12" type="password" name="pwd_confirm" placeholder="<?php echo USER_PSWD;?>" required />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"><?php echo USER_MAIL;?></label>
                    <div class="controls">
                        <input class="span12" type="email" name="email" placeholder="<?php echo USER_MAIL_EXEMPLE;?>" <?php echo (isset($email))?'value="'.$email.'"':"";?>/>
                        <p class="text-right"><small><?php echo USER_MAIL_DESC;?></small></p>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"><?php echo REGISTER_HUMAN;?><br><?php echo REGISTER_CHARACTER_QUESTION;?><span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="span12" type="text" name="question" required />
                    </div>
                </div>
                <?php if(!isset($_GET['mailid'])){?>
                <div class="control-group">
                    <div class="controls">
                        <label class="checkbox pull-left span12">
                            <input type="checkbox" name="maillist">
                             <?php echo CONTACT_CATEGORY_MAILLIST.' - '.REGISTER_MAILLIST_SUBSCRIBE;?>
                        </label>
                    </div>
                </div>
                <?php } ?>
                <?php if(isset($_GET['mailid'])){ ?>
                    <input type="hidden" name="mailid" value="<?php echo $_GET['mailid'];?>">
                <?php } ?>
                <input type="hidden" name="language" value="<?php echo $session->get_session_language_id();?>">
                <input type="hidden" name="redirect" value="<?php echo $action;?>">
                <div class="control-group">
                    <div class="controls">
                        <button type="reset" class="btn btn-danger pull-left"><?php echo FORM_RESET;?></button>
                        <button type="submit" class="btn btn-primary pull-right"><?php echo FORM_REGISTER;?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <br>
</div>