
<h1>Login</h1>

<form action="<?=$this::url('pages/post_login')?>" method="post">
    OpenID: <input type="text" name="identity" id="identity" /> <button>Submit</button>
</form>
<br />
OR<br />
<br />

<button onclick="(function(id){ id.value='<?=$this::javascript($google)?>'; id.form.submit(); })(document.getElementById('identity'));">Login with Google</button>

<button onclick="document.location = '<?=$this::javascript($Facebook->login())?>';">Login with FriendFace</button>
