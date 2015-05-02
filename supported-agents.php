<?php 
$pageTitle = "Supported Agents";
$pageSubTitle = "(Supported Agents)";
include "header.php"; 
include "main-nav.php"; 
include "functions.php";
?>

<?php
$apiRequest = "/agents";

$result = dudaAPIRequest("GET", $apiRequest);

if(strpos($result, "error_code") !== false)
{
print "<section id=\"error\">" . getValue("error_code", $result) . "</section>";
}
else
{
/*Print the result*/
print "<section id=\"formResponse\">" .
"<ul id=\"userAgentList\">";
$agentList = preg_replace("/\((.+)\)/i", "$1" , $result);
$agentArray = explode("|", $agentList);
sort($agentArray);
for($gix = 0; $gix < count($agentArray); $gix++)
{
print "<li>" . preg_replace("/\(\.\*(.+?)\.\*\)/i", "$1 " , $agentArray[$gix]) . "</li>";
}
print "</ul></section>";
}

?>

<?php 
include "footer.php";
?>	