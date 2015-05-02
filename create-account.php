<?php 
$pageTitle = "Account Creation";
$pageSubTitle = "(Create Account)";
include "header.php"; 
include "main-nav.php"; 
include "functions.php";
?>

<section id="form">
<form action="create-account.php" method="post">
Account Name: <input type="text" name="account_name"><br>
Email: <input type="text" name="email"></br>
First Name: <input type="text" name="first_name"><br>
Last Name: <input type="text" name="last_name"><br>
Language: <select name="lang">
			<option value="en">English</option>
			<option value="ja">Japanese</option>
			<option value="es">Spanish</option>
		</select><br>
<input type="hidden" name="form_submitted" value="true">
<input type="submit" value="Create Account">
</form>
</section>
<?php
//create an array to encode using json_encode
$accountData = array("account_name" => $_POST["account_name"],
					 "email" => $_POST["email"],
					 "first_name" => $_POST["first_name"],
					 "last_name" => $_POST["last_name"],
					 "lang" => $_POST["lang"]);  
//encode the array                                                                
$accountData = json_encode($accountData);

//create documentation
$apiRequest = "/accounts/create";

if($_POST["form_submitted"])
{
if(isEmail($_POST["email"]) !== 1)
{
print "<section id=\"error\">Error: Invalid email format.</section>";
}
else
{
$result = dudaAPIRequest("POST", $apiRequest, $accountData);
if(strpos($result, "error_code") !== false)
{
print "<section id=\"error\">" . getValue("error_code", $result) . "</section>";
}
else
{
print "<section id=\"confirm\"><p>Account " . $_POST["site_name"] . " created!</section>";
}
}
}
?>


<?php 
include "footer.php";
?>