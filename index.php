<?php session_start(); ?>
<html>
<head><title>Gallery</title>\
<style type="text/css">
tst {
background-image: url("https://s3.amazonaws.com/itm444/scarlet+hawk.png");
text-align: center;
text-size: 150px;
}

</style>


</head>
<body>


<form   enctype="multipart/form-data" action="submit.php" method="POST">
	<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
	Enter Username: <input type="uname" name="username"><br />
	Enter User E-mail: <input type="email" name="useremail"><br />
	Enter User Phone(1-XXX-XXX-XXXX): <input type="phone" name="phone">
	Chooose File: <input name="userfile" type="file" /><br />
	<input type="submit" value="Send File" />
</form>

<br><br><hr/></br><br>

<form enctype="multipart/form-data" action="gallery.php" method="POST">
	User E-mail connected to gallery: <input type="email" name="email">
	<input type="submit" value="Load Gallery" />
</form>


</body>
</html>