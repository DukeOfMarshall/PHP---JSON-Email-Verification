<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

<title>Sample</title>

<script>
$(function(){
	$("#js_verify").click(function(){
		$.getJSON("EmailVerify.class.php", { "address_to_verify" : $("#email").val() }, function(data){
			alert(data.message);
			return false;
		});
		return false;
	});
});
</script>

</head>
<body>
<?php
if($_POST['email']){
	require_once("EmailVerify.class.php");
	$verify = new EmailVerify();
	
	if($verify->verify_formatting($_POST['email'])){
		echo "Email is formated correctly<BR>\r\n";
	}else{
		echo "Email is NOT formated correctly<BR>\r\n";
	}
	
	if($verify->verify_domain($_POST['email'])){
		echo "Domain has been verified<BR>\r\n";
	}else{
		echo "Domain has NOT been verified<BR>\r\n";
	}
	
	echo "<BR>\r\n<BR>\r\n";
}
?>
<form method="post">
<input type="text" name="email" id="email">
<input type="submit" value="Verify through PHP" />
<button id="js_verify">Verify through JavaScript</button>
</form>

</body>
</html>
