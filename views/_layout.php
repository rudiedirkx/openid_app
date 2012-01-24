<!doctype html>
<html>

<head>
	<title><?=$this->title()?> - My app</title>
	<style>
header, footer {
	padding: 20px;
	background-color: #eee;
}
p, ul, ol, table {
	margin: 0 0 15px;
}
p:last-child, ul:last-child, ol:last-child, table:last-child {
	margin: 0 0;
}
li.error {
	color: red;
}
li.success {
	color: green;
}
li.warning {
	color: orange;
}
	</style>
</head>

<body>

	<header>
		<p>My app header</p>
		<p>
			Pages:
			<?=$this::link('Secrets!', 'pages/restricted')?>,
			<?=$this::link('Accounts', 'pages/accounts')?>,
			<?=$this::link('Log in', 'pages/login')?>,
			<?=$this::link('Log out', 'pages/logout')?>
		</p>
	</header>

<?if( $messages ):?>
	<ul id="messages">
		<?foreach( $messages AS $message ):?>
			<li class="<?=$message[1]?>"><?=$message[0]?></li>
		<?endforeach?>
	</ul>
<?endif?>

	<div id="content">
		<?=$content?>
	</div>

	<footer>
		<p>My app footer</p>
		<p>Users:</p>
		<ul>
			<?foreach( $users as $user ):?>
				<li><?=$user?></li>
			<?endforeach?>
		</ul>
	</footer>

	<pre><? print_r($_COOKIE) ?></pre>
	<pre><? print_r($_SESSION) ?></pre>
	<pre><? print_r($_POST) ?></pre>
	<pre><? print_r($_GET) ?></pre>

</body>

</html>