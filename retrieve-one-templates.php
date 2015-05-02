<?php 
//retrieve One templates
//generates a list of DudaOne templates 
//for the user to choose from and passes
//the selected template to create-one-site.php

$pageTitle = "Select One Templates";
$pageSubTitle = "(Select a Template)";
include "header.php"; 
include "main-nav.php"; 
include "functions.php";
?>


<?php
/*get template list*/
$templateList = getTemplateList();
$shellTemplatePattern = "/({.+?})/";
preg_match_all($shellTemplatePattern, $templateList, $templateArray);
$templateDataArray;

/*for each template*/
foreach ($templateArray[1] as $gix => $template){
print "<section class=\"templateSection\">";
print "<form action=\"create-one-site.php\" method=\"post\">";

/*put template info into array*/
$templateDataArray[$gix][0] = getValue("template_name", $template);
$templateDataArray[$gix][1] = getValue("preview_url", $template);
$templateDataArray[$gix][2] = getValue("thumbnail_url", $template);
$templateDataArray[$gix][3] = getNotStringValue("template_id", $template);

/*print array information as selection dialog*/
print "<p class=\"templateName\">" . $templateDataArray[$gix][0] . "</p>";
print "<a  href=\"" . $templateDataArray[$gix][1] . "\" target=\"_blank\">";
print "<img class=\"templateThumbnail\" src=\"" . $templateDataArray[$gix][2] . "\"></img><br></a>";
print "<input type=\"hidden\" name=\"template_id\" value=\"" . $templateDataArray[$gix][3] . "\">";
print "<input type=\"submit\" value=\"Select Template\">";
print "</form>";
print "</section>";
}
?>

<?php 
include "footer.php";
?>