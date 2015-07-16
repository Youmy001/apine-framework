
    <div class="form-box well no-shadow">
        <small class="pull-right text-right">
            <a class="effect" href="<?php echo URL_Helper::path("register");?>"><?php echo LOGIN_SUBSCRIBE;?></a>
            <br>
            <br>
            <a class="effect" href="<?php echo URL_Helper::path("login/restore");?>"><?php echo LOGIN_LOST;?></a>
        </small>
        <h3 class="xt-big title_font">Login</h3>
        <div class="row-fluid">
            <form id="content" class="span12" action="<?php echo URL_Helper::path("login",false);?>" method="post">
                <div class="control-group">
                    <label class="control-label">Username</label>
                    <div class="controls">
                        <input class="span12" type="text" name="user" placeholder="Username" required />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Password</label>
                    <div class="controls">
                        <input class="span12" type="password" name="pwd" placeholder="Password" required />
                    </div>
                </div>
                <input type="hidden" name="redirect" value="<?php echo $action;?>">
                <div class="control-group">
                    <div class="controls">
                            <label class="checkbox pull-left">
                                <input type="checkbox" name="perm" value="on">
                                 Remember me
                            </label>
                            <button type="submit" class="btn btn-primary pull-right">Login</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
