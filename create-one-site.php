<?php 
$pageTitle = "Site Creation";
$pageSubTitle = "(Create One Site)";
include "header.php"; 
include "main-nav.php";
include "functions.php"; 
?>
<section id="form">
<form action="create-one-site.php" method="post">
Domain <input type="text" name="default_domain_prefix">.multiscreensite.com<br>
Original Site URL: http://<input type="text" name="url"><br>
<input type="hidden" name="form_submitted" value="true">

<?php
/*check if a template was selected*/
if(!isset($_POST["template_id"]))
{
echo "<script type=\"text/javascript\">document.location.href=\"retrieveOneTemplates.php\";</script>";
}
else
{
print "<input type=\"hidden\" name=\"template_id\" value=\"" . $_POST["template_id"] . "\">";
}
?>

<input type="submit" value="Create Site">
</form>
</section>
<?php

$createData = array("template_id" => $_POST["template_id"],
                                "url" => "http://" . $_POST["url"],
                                "default_domain_prefix" => $_POST["default_domain_prefix"]);

//encode the array                                                                
$createData = json_encode($createData);

/*create documentation*/
$apiRequest = "/sites/multiscreen/create";

if($_POST["form_submitted"])
{
$result = dudaAPIRequest("POST", $apiRequest, $createData);
if(strpos($result, "error_code") !== false)
{
print "<section id=\"error\">" . getValue("error_code", $result) . "</section>";
}
else
{
print "<section id=\"confirm\"><p class=\"onePreview\">Site " . $_POST["site_name"] . " created! ";
print"<a href=\"http://banzai.mobilewebsiteserver.com/preview/" . getValue("site_name", $result) . "\">Preview your site</a></p></section>";
}
}
?>
<?php 
include "footer.php";
?>	