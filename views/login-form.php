
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="wrap">
	<div class="et_pb_column_1_3 clientLoginRow">
		<h2 class="ClientLoginHeading">My Project</h2>
		<form id="user-log" class="pmm-form" method="login">
			<div class="error-message"></div>
			<p class="login-username">
				<label for="user_login">Username</label>
				<input type="text" name="username" id="user_login" class="input" required="required" size="20">
			</p>
			<p class="login-password">
				<label for="user_pass">Password</label>
				<input type="password" name="password" id="user_pass" class="input" required="required" size="20">
			</p>
			<!--<p class="login-remember"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever"> Remember Me</label></p>-->
			<p class="login-submit">
				<button type="submit" name="submit" class="button-primary et_pb_button" >Log In</button>
				<img id="loading" src="<?= admin_url('images/loading.gif') ?>" title="loading" style="display:none;"/>
			</p>

		</form>
	</div>
</div>