<?php 
$pageTitle = "Site Creation";
$pageSubTitle = "(Create Site)";
include "header.php"; 
include "main-nav.php";
include "functions.php"; 
?>
<section id="form">
<form action="create-site.php" method="post">
Site Name: <input type="text" name="site_name"><br>
Original Site URL: http://<input type="text" name="original_site_url"><br>
Account Name:<input type="text" name="account_name"></br>
<input type="hidden" name="form_submitted" value="true">
<input type="submit" value="Create Site">
</form>
</section>
<?php
$siteData = array("site_name" => $_POST["site_name"],
		            "original_site_url" => "http://" . $_POST["original_site_url"],
                            "account_name" => $_POST["account_name"]);

$createData = array("site_data" => $siteData);

//encode the array                                                                
$createData = json_encode($createData);

/*create documentation*/
$apiRequest = "/sites/create";

if($_POST["form_submitted"])
{
$result = dudaAPIRequest("POST", $apiRequest, $createData);
if(strpos($result, "error_code") !== false)
{
print "<section id=\"error\">" . getValue("error_code", $result) . "</section>";
}
else
{
print "<section id=\"confirm\"><p>Site " . $_POST["site_name"] . " created!</section>";
print"<section id=\"previewSection\"><iframe id=\"previewFrame\" src=\"http://my.dudamobile.com/site/" . $_POST["site_name"] . "\"?dm_try_mode=true\"></iframe></section>";
}
}
?>
<?php 
include "footer.php";
?>	