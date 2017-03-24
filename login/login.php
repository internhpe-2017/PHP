


<html>
<body>

<div id="login-controls">
<h2>Login</h2>
<?php if(@$_GET['err']==1){?>
<div class="error-text">Login incorrect</div>
<?php  }   ?>
<form method="POST" action="index.php">
<p>Username: <input type="text" name="user"> </p>
<p>Password: <input type="text" name="pass"> </p>
<input type="submit" name="op" value="login">
</form>
</body>
</html>

