<?php
use app\specs\Output;
use app\openid\FacebookConnect;
?>

<h1>Login</h1>

<form action="<?=Output::url('pages/post_login')?>" method="post">
    OpenID: <input type="text" name="identity" id="identity" /> <button>Submit</button>
</form>
<br />
OR<br />
<br />

<button onclick="(function(id){ id.value='<?=$this::javascript($google)?>'; id.form.submit(); })(document.getElementById('identity'));">Login with Google</button>

<button onclick="FB.login();">Login with FriendFace</button>

<div id="fb-root"></div>
<script src="http://connect.facebook.net/en_US/all.js"></script>
<script>
FB.init({
	appId: '<?= FacebookConnect::$appId ?>',
	status: true,
	cookie: true,
	xfbml: true
});
FB.Event.subscribe('auth.login', function(response) {
	window.location = '<?=Output::url('pages/post_facebook')?>';
});
</script>