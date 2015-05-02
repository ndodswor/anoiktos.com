<?php
include "functions.php";
//set file type
header('Content-Type: application/octetstream; name="satisfaction-extract.txt"');
header('Content-Type: application/octet-stream; name="satisfaction-extract.txt"');
header('Content-Disposition: attachment; filename="satisfaction-extract.txt"');
?>
<?php
/*create documentation*/
//standard API request
if(empty($_GET))
{
	$apiRequest = "/api/v2/search.json?sort_order=desc&sort_by=updated_at&query=satisfaction:goodwithcomment";
	$_GET['query'] = "satisfaction:goodwithcomment";
}
else
{
	//get API request if user inputted one
	$apiRequest = "/api/v2/search.json?sort_order=desc&sort_by=updated_at";
	$apiRequest = $apiRequest . "&page=" . getIfGet('page') . "&query=" . getIfGet('query');
	if($_GET['from_date'])
	{
		$apiRequest = $apiRequest . "+solved>" . $_GET['from_date'];
	}
	if($_GET['to_date'])
	{
		$apiRequest = $apiRequest . "+solved<" . $_GET['to_date'];
	}
	if($_GET['assignee_id'] && $_GET['assignee_id'] != "any")
	{
		$apiRequest = $apiRequest . "+assignee:" . $_GET['assignee_id'];
	}
}

//if no page is returned, set pages to 1
if(!$_GET['page'])
{
	$_GET['page'] = 1;
}
//make API call
$result = zendeskAPIRequest("GET", $apiRequest);

//error checking
if(strpos($result, "error_code") !== false)
{
	print "<section id=\"error\">" . getValue("error_code", $result) . "</section>";
}
else
{
	//print column titles
	print "\"" . "Ticket ID" . "\",";
	print "\"" . "Subject" . "\",";
	print "\"" . "Created At" . "\",";
	print "\"" . "Updated At" . "\",";
	print "\"" . "Assignee ID" . "\",";
	print "\"" . "Comment" . "\"\n";

	//print results of API call
	$result = json_decode($result);
	$gix = 0;
	foreach($result->results as $r)
	{
		print "\"" . $r->id . "\",";
		print "\"" . $r->subject . "\",";
		print "\"" . $r->created_at . "\",";
		print "\"" . $r->updated_at . "\",";
		print "\"" . $r->assignee_id . "\",";
		print "\"" . $r->satisfaction_rating->comment . "\"\n";
	}
}
?>