<div id="loginBar">
        <!-- Login Starts Here -->
        <div id="loginContainer">
            <a href="#" id="loginButton"><span>Login</span><em></em></a>
            <div style="clear:both"></div>
            <div id="loginBox">                
                <form id="loginForm" action='<?= $from ?>' method="post">
                    <fieldset id="body">
                        <fieldset>
                            <label for="email">UserName</label>
                            <input type="text" name="username" id="username" />
                        </fieldset>
                        <fieldset>
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" />
                        </fieldset>
                        <input type="submit" id="login" value="Sign in" />
                        <label for="checkbox"><input type="checkbox" id="checkbox" />Remember me</label>
                    </fieldset>
                    <span><a href="#">Forgot your password?</a></span>
                </form>
            </div>
        </div>
        <!-- Login Ends Here -->
</div>


