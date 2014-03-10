PHP---JSON-Email-Verification
=============================

This is a short, simple, and straight forward script to perform some basic verification on a submitted email address. Can be used either as a PHP include/require or as a straight up URL with one GET variable so that one can use it with just about any other language such as JavaScript, etc. When calling through a URL with another language such as JavaScript, the output is returned as a JSON string.

```TEXT
{"format_verified":1,"error":0,"domain_verified":1,"message":"Formatting and domain have been verified"}
```

```TEXT
["format_verified"] => 1
["error"] => 0
["domain_verified"] => 1
["message"] => "Formatting and domain have been verified"
```

This has only been developed and tested on a CentOS Apache setup with PHP 5.4.21 .

<html>
<head>

<title> </title>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-48664139-1', 'github.com');
  ga('send', 'pageview');

</script>

</head>
<body>

</body>
</html>

JavaScript Example:
===================
Using jQuery:
```JAVASCRIPT
$(function(){
	$("#js_verify").click(function(){
		$.getJSON("EmailVerify.class.php", { "address_to_verify" : $("#email").val() }, function(data){
			alert(data.message);
			return false;
		});
		return false;
	});
});
```

PHP Example:
============
```PHP
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
```
