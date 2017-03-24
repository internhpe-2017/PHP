


<html>
<body>

<div id="login-controls">
<h2>Login</h2>
<?php if(@$_GET['err']==1){?>
<div class="error-text">unsuccessfull</div>
<?php  }   ?>
<form method="POST" action="index.php">
<p>Username: <input type="text" name="user"> </p>
<p>Particular: <input type="text" name="part"> </p>
<p>Date: <input type="text" name="datee"> </p>
<p>Amout: <input type="text" name="amount"> </p>
<input type="submit" name="op" value="login">
</form>
</body>
</html>

