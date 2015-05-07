<?php
//ini_set('display_errors', 'On');
//error_reporting(E_ALL);

//includes
include "functions.php";

//set type of generated file
header('Content-Type: application/octetstream; name=bug-priority-extract.txt"');
header('Content-Type: application/octet-stream; name="bug-priority-extract.txt"');
header('Content-Disposition: attachment; filename="bug-priority-extract.txt"');
?>
<?php

//get API request if user inputted one
if(md5($_GET['sec']) == "#####example#####")
{
	$_GET['query'] = "type:ticket%20status:open%20group:qa";
	$apiRequest = "/api/v2/search.json?sort_order=desc&sort_by=updated_at&query={$_GET['query']}";
	$apiRequest = $apiRequest . "&page=" . getIfGet('page') . "&query=" . getIfGet('query');
	if($_GET['number'])
	{
		$apiRequest = $apiRequest . "+" . $_GET['number'];
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
	//decode the result
	$result = json_decode($result);

	//process the result
	generateFileData($result);
}

//prints the data gathered from the call
function generateFileData($result)
{
	//include variables
	include "bug-config.php";
	include "bug-functions.php";

	//print column titles
	print "\"" . "Ticket ID" . "\",";
	print "\"" . "Subject" . "\",";
	print "\"" . "Creation Date" . "\",";
	print "\"" . "Old Issue" . "\",";
	//start of array variables
	foreach($priorityFields as $field => $value)
	{
		print "\"" . $value['name'] . "\",";
	}
	//end of array variables
	print "\"" . "Priority" . "\",";
	print "\"" . "Priority #" . "\"\r\n";

	//handle queries with multiple pages of results
	$pages = floor(($result->count / 100)) + 1;
	for($gix = 0; $gix < $pages; $gix++)
	{
		//don't bother if it's the first time through or there's only one page
		if(isset($result->next_page) && $gix != 0)
		{
			$apiRequest = $result->next_page;
			$apiRequest = parse_url($apiRequest);
			if(isset($apiRequest['path']) && isset($apiRequest['query']))
			{
				$apiRequest = $apiRequest['path'] . "?" . $apiRequest['query'] . "\n";
			}
			$result = zendeskAPIRequest("GET", $apiRequest);
			$result = json_decode($result);
		}
		foreach($result->results as $r)
		{
			$thisPriority = 0;
			//reset the timeout limit to 30 seconds
			set_time_limit(30);

			//print ticket data
			print "\"" . $r->id . "\",";
			print "\"" . $r->subject . "\",";
			print "\"" . $r->created_at . "\",";

			//get days since created
			$createdAt = strtotime($r->created_at);
			$eras = calcEras($createdAt, $era);

			//check to see if it's an old ticket
			if($eras > 0)
			{
				print "\"$eras\",";
				$thisPriority += ($eraWeight * $eras); 
			}
			else
			{
				print "\"\",";
			}

			//print ticket category
			foreach($r->custom_fields as $cf)
			{
				//calculate priority fields
				foreach($priorityFields as $field => $value)
				{
					if($cf->id === $field)
					{
						if($cf->value === true)
						{
							print "\"true\",";
							$thisPriority += $value['weight']; 
						}
						else
						{
							print "\"\",";
						}
					}
				}	
				//deal with user status
				if($cf->id === 24201548)
				{
					//t4
					if($cf->value === "autodetect_4_partner")
					{
						$thisPriority += $tierWeight['4']; 	
					}
					//t3
					if($cf->value === "autodetect_3_partner")
					{
						$thisPriority += $tierWeight['3']; 	
					}
					//t2
					if($cf->value === "autodetect_2_partner")
					{
						$thisPriority += $tierWeight['2']; 	
					}
					//t1
					if($cf->value === "autodetect_1_partner")
					{
						$thisPriority += $tierWeight['1']; 	
					}
				}
			}
		//translate priority to value
		$priorityValue = calcPriority($thisPriority);
		//print priority
		print "\"$thisPriority\",\"$priorityValue\"\r\n";
		}
	}
}
?>