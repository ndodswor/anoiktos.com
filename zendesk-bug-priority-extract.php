<?php
//ini_set('display_errors', 'On');
//error_reporting(E_ALL);
include "functions.php";

//set type of generated file
header('Content-Type: application/octetstream; name=bug-priority-extract.txt"');
header('Content-Type: application/octet-stream; name="bug-priority-extract.txt"');
header('Content-Disposition: attachment; filename="bug-priority-extract.txt"');
?>
<?php

//set default request
if(empty($_GET))
{
$_GET['query'] = "type:ticket%20status:open%20group:qa";
	$apiRequest = "/api/v2/search.json?sort_order=desc&sort_by=updated_at&query={$_GET['query']}";
}
else
{
	//get API request if user inputted one
	$apiRequest = "/api/v2/search.json?sort_order=desc&sort_by=updated_at";
	$apiRequest = $apiRequest . "&page=" . getIfGet('page') . "&query=" . rawurlencode(getIfGet('query'));
	if($_GET['from_date'])
	{
		$apiRequest = $apiRequest . "+solved>" . $_GET['from_date'];
	}
	if($_GET['to_date'])
	{
		$apiRequest = $apiRequest . "+solved<" . $_GET['to_date'];
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
	print "\"" . "Creation Date" . "\",";
	print "\"" . "Old Issue" . "\",";
	//start of array variables
	print "\"" . "No Customer Workaround" . "\",";
	print "\"" . "No Workaround" . "\",";
	print "\"" . "Business Impact" . "\",";
	print "\"" . "Vocal" . "\",";
	print "\"" . "Common" . "\",";
	//end of array variables
	print "\"" . "Productive" . "\",";
	print "\"" . "Priority" . "\",";
	print "\"" . "Priority #" . "\"\r\n";

	//decode the result
	$result = json_decode($result);

	//process the result
	extractApiData($result);
}

//prints the data gathered from the call
function extractApiData($result)
{
	//set variables

	//used to determine whether a ticket is old
	$oldAge = 60;

	//used to label priority fields
	//if you add a new one, be sure to also add it to the column titles above
	$priorityFields = array(
		"25676897" => array("name" => "No Customer Workaround", "weight" => 1),
		"24126186" => array("name" => "No Workaround", "weight" => 1),
		"25676907" => array("name" => "Business Impact", "weight" => 1),
		"25676917" => array("name" => "Vocal", "weight" => 1),
		"25676927" => array("name" => "Common", "weight" => 1),
	);

	//set weight for non-array priority fields
	$oldAgeWeight = 1;
	$productiveWeight = 1;

	//start function

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
			$age = time() - $createdAt; 
			$age = floor($age/(60*60*24));

			//check to see if it's an old ticket
			if($age > $oldAge)
			{
				print "\"true\",";
				$thisPriority += $oldAgeWeight; 
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
							$thisPriority += $value[weight]; 
						}
						else
						{
							print "\"\",";
						}
					}
				}	
				//deal with reseller status
				if($cf->id === 24201548)
				{
					if($cf->value === "autodetect_1_partner"||
						$cf->value === "autodetect_2_partner"||
						$cf->value === "autodetect_3_partner")
					{
						print "\"true\",";
						$thisPriority += $productiveWeight; 	
					}
					else
					{
						print "\"\",";
					}
				}	
			}
			//translate priority to value
			$priorityValue = "medium";
			if($thisPriority < 3)
			{
				$priorityValue = "low";
			}
			if($thisPriority > 4)
			{
				$priorityValue = "high";
			}
		//print priority
		print "\"$thisPriority\",\"$priorityValue\"\r\n";
		}
	}
}
?>