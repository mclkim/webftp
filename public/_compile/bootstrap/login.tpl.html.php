<?php /* Template_ 2.2.8 2015/12/21 12:06:25 D:\phpdev\workspace\webftp\public\_template\bootstrap\login.tpl.html 000001590 */ ?>
<div class="container">
	<form id="login_form" class="form-signin" action="?login.auth"
		method=post role="form" onsubmit="return false;">
		<input type="hidden" name="authenticity_token" value="<?php echo $TPL_VAR["authenticity_token"]?>" /> 
		<input type="hidden" name="returnURI" value="<?php echo $TPL_VAR["returnURI"]?>" />
		<h2 class="form-signin-heading">Please sign in</h2>
		<input type="text" id="<?php echo $TPL_VAR["userField"]?>" name="<?php echo $TPL_VAR["userField"]?>"
			class="form-control" placeholder="User ID" required autofocus>
		<input type="password" id="<?php echo $TPL_VAR["passField"]?>" name="<?php echo $TPL_VAR["passField"]?>"
			class="form-control" placeholder="Password" required> <label
			class="checkbox"> <input type="checkbox" value="remember-me">
			Remember me
		</label>
		<button id='login_bt' class="btn btn-lg btn-primary btn-block"
			type="submit">Sign in</button>
	</form>
</div>

<script type="text/javascript">
$(document).on("click", '#login_bt', function() {
	$.ajax({
		url : "?login.auth",
		type : 'POST',
		data : $("#login_form").serialize(),
		success : function(res) {
			res = $.parseJSON(res);
			if (res.code == 0) {
				alert(res.message);
			} else if (res.code == 1) {
				window.location = res.value;
				//document.location.href = res.value;
			} else {
				alert('Problem with sql query');
			}
			return false;
		}
	});
	return false;
});
</script>