<?php
//ini_set('display_errors', 'On');
//error_reporting(E_ALL);
$pageTitle    = "Zendesk Bug Priority";
$pageCSS      = "css/zendeskBugPriority.css";
$pageSubTitle = "(Zendesk Bug Priority)";
//$debug = true;

//includes
include "bug-config.php";
include "header.php";
include "main-nav.php";
include "functions.php";
include "bug-functions.php";
?>

<!-- introduction -->
<section id="intro">
<p>Check a ticket</p>

<!-- request information from user -->
<form action="zendesk-bug-priority.php" method="get">
    Number:
    <input type="number" name="number" placeholder="<?php print "000001"; ?>">
    <input type="submit" value="Prioritize">
  </form>
</section>

<?php

//start results list

//set default request
if(empty($_GET))
{
	print "<section>security token not found</section>";
}
else
{
	if(md5($_GET['sec']) == "#####example#####")
	{
		//get API request if user inputted one
		$_GET['query'] = "type:ticket%20status:open%20group:qa";
		$apiRequest = "/api/v2/search.json?sort_order=desc&sort_by=updated_at&query={$_GET['query']}";
		$apiRequest = $apiRequest . "&page=" . getIfGet('page') . "&query=" . getIfGet('query');
		if($_GET['number'])
		{
			$apiRequest = $apiRequest . "+" . $_GET['number'];
		}
	}
}

//if no page is returned, set pages to 1
if(!$_GET['page'])
{
	$_GET['page'] = 1;
}
//make API call
$result = zendeskAPIRequest("GET", $apiRequest);

//debug stuff
if($debug){print "query = " . $_GET['query'];}
if($debug){print "<br>request = " . $apiRequest;}
if($debug){print "<br>result = " . $result;}

//error checking
if(strpos($result, "error_code") !== false)
{
print "<section id=\"error\">" . getValue("error_code", $result) . "</section>";
}
else
{
	//decode the result
	$result = json_decode($result);
	if($debug){print "<br>decoded result = " . $result;}

	//create a ticket section
	print "<section id=\"ticketList\">";

	//create an admin paragraph
	print "<p id=\"adminPararaph\">";

	//display page & ticket number information
	print "<span class=\"numTickets\">Total open bug tickets: " . $result->count . ".<br>";
	print " Showing " . ((($_GET['page'] - 1) * 100) + 1) . " to ";
	if(($_GET['page'] * 100) > $result->count){print $result->count;}else{print ($_GET['page']) * 100;}
	print ".</span>";

	//give link to extract
	print "<a class=\"extractLink\" target=\"_blank\" href=\"zendesk-bug-priority-extract.php?sort_order=desc";
	print "&from_date=" . getIfGet('from_date'); 
	print "&to_date=" . getIfGet('to_date');
	print "&query=" . getIfGet('query');
	if($_GET['page']){print "&page=" . ($_GET['page']);}
	print "\">Extract Data</a>";
	print "</p>";

	//if relevant, give links to next/previous pages
	if($_GET['page'] > 1 || count($result->results) > 99)
	{
		print "<p id=\"linkSection\">";
		if($_GET['page'] > 1)
		{
			print "<span class=\"prevLink\"><a href=\"zendesk-bug-priority.php?sort_order=desc";
			print "&from_date=" . getIfGet('from_date'); 
			print "&to_date=" . getIfGet('to_date');
			print "&query=" . getIfGet('query');
			print "&assignee_id=" . $assignee[getIfGet('assignee_id')];
			if($_GET['page']){print "&page=" . ($_GET['page'] - 1);}
			print "\">Previous Page</a></span>";
		}

		if(count($result->results) > 99)
		{
			print "<span class=\"nextLink\"><a href=\"zendesk-bug-priority.php?sort_order=desc";
			print "&from_date=" . getIfGet('from_date');
			print "&to_date=" . getIfGet('to_date');
			print "&query=" . getIfGet('query');
			print "&assignee_id=" . $assignee[getIfGet('assignee_id')];
			if($_GET['page']){print "&page=" . ($_GET['page'] + 1);} else { print "&page=2"; }
			print "\">Next Page</a></span>";
		}
	}
	print "</p>";

//print results of API call:
	
//number and display ticket results
print "<ul id=\"tickets\">";

$gix = 0;
foreach($result->results as $r)
{
	$thisPriority = 0;
	$gix = $gix + 1;
	print "<li class=\"ticket ";
	print "\">";
	print "<div class=\"ticketNameNumberDiv\">";
	print "<span class=\"ticketNumber\">" . ((($_GET['page'] - 1) * 100) + $gix) . "</span>";
	print "<a class=\"ticketLink\" href=\"https://example.zendesk.com/agent/#/tickets/" . $r->id . "\">" . $r->subject . "</a>";
	print "</div>";
	print "<ul class=\"ticketInfo\"><li class=\"createDate\">created: " . $r->created_at . "</p>";

	//handle custom fields, iterate thisPriority to output total priority of ticket
	print "<ul class='priorityList'>";

	//get days since created
	$createdAt = strtotime($r->created_at);
	$eras = calcEras($createdAt, $era);

	//adjust priority based on age
	if($eras > 0)
	{
		print "<li class='priorityItem'>$eras eras old</li>";
		$thisPriority += ($eraWeight * $eras); 
	}
	
	foreach($r->custom_fields as $cf)
	{
		//calculate priority fields
		foreach($priorityFields as $field => $value)
		{
			if($cf->id === $field)
			{
				if($cf->value === true)
				{
					print "<li class='priorityItem'>{$value['name']}</li>";
					$thisPriority += $value['weight']; 
				}
			}
		}	
		//deal with user status
		if($cf->id === 24201548)
		{
			//t4
			if($cf->value === "autodetect_4_partner")
			{
				print "<li class='User'>T4 Partner</li>";
					$thisPriority += $tierWeight["4"]; 	
			}
			//t3
			if($cf->value === "autodetect_3_partner")
			{
				print "<li class='User'>T3 Partner</li>";
					$thisPriority += $tierWeight["3"]; 	
			}
			//t2
			if($cf->value === "autodetect_2_partner")
			{
				print "<li class='User'>T2 Partner</li>";
					$thisPriority += $tierWeight["2"]; 	
			}
			//t1
			if($cf->value === "autodetect_1_partner")
			{
				print "<li class='User'>T1 Partner</li>";
					$thisPriority += $tierWeight["1"]; 	
			}
		}
	}
	print "</ul>";

	//translate priority to value
	$priorityValue = calcPriority($thisPriority);
		
	//print priority
	print "<div class='ticketPriority $priorityValue'>Priority:$thisPriority ($priorityValue)</div>";
	print "</li>";
	print "</ul>";
}
print "</section>";
}
?>

<?php
include "footer.php";
?>