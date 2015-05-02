<?php
$pageTitle    = "Zendesk Satisfaction";
$pageCSS      = "/css/zendeskSatisfaction.css";
$pageSubTitle = "(Zendesk Satisfaction)";
include "header.php";
include "main-nav.php";
include "functions.php";
?>

<!-- introduction -->
<section id="intro">
<p>Satisfaction Ratings</p>

<!-- request information from user -->
<form action="zendesk-satisfaction.php" method="get">
    From:
    <input type="text" name="from_date" placeholder="<?php print "YYYY-MM-DD"; ?>">
    To:
    <input type="text" name="to_date" placeholder="<?php print date('Y-m-d'); ?>">
    <br>
	Assignee:
    <select name="assignee_id">
    	<option value="any">Any</option>
		<option value="397994078">Example</option>
		<option value="399153571">Example</option>
		<option value="921195813">Example</option>
		<option value="189268483">Example</option>
		<option value="244329411">Example</option>
		<option value="695637057">Example</option>
		<option value="385435681">Example</option>
		<option value="272862992">Example</option>
		<option value="347464611">Example</option>
		<option value="326050602">Example</option>
		<option value="219829091">Example</option>
		<option value="400035156">Example</option>
		<option value="937857796">Example</option>
		<option value="28670807">Example(QA)</option>
		<option value="105350927">Example(Bulk)</option>
	</select>
	Rating:
	<select name="query">
		<option value="satisfaction:goodwithcomment">Good</option>
		<option value="satisfaction:badwithcomment">Bad</option>
	</select>
	<br>
    <input type="submit" value="Search">
  </form>
</section>


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
	//decode the result
	$result = json_decode($result);

	//create a ticket section
	print "<section id=\"ticketList\">";

	//create an admin paragraph
	print "<p id=\"adminPararaph\">";

	//display page & ticket number information
	print "<span class=\"numTickets\">Total matching tickets: " . $result->count . ".<br>";
	print " Showing " . ((($_GET['page'] - 1) * 100) + 1) . " to ";
	if(($_GET['page'] * 100) > $result->count){print $result->count;}else{print ($_GET['page']) * 100;}
	print ".</span>";

	//give link to extract
	print "<a class=\"extractLink\" target=\"_blank\" href=\"/zendesk-satisfaction-extract.php?sort_order=desc";
	print "&from_date=" . getIfGet('from_date'); 
	print "&to_date=" . getIfGet('to_date');
	print "&query=" . getIfGet('query');
	print "&assignee_id=" . getIfGet('assignee_id');
	if($_GET['page']){print "&page=" . ($_GET['page']);}
	print "\">Extract Data</a>";
	print "</p>";

	//if relevant, give links to next/previous pages
	if($_GET['page'] > 1 || count($result->results) > 99)
	{
		print "<p id=\"linkSection\">";
		if($_GET['page'] > 1)
		{
			print "<span class=\"prevLink\"><a href=\"/zendesk-satisfaction.php?sort_order=desc";
			print "&from_date=" . getIfGet('from_date'); 
			print "&to_date=" . getIfGet('to_date');
			print "&query=" . getIfGet('query');
			print "&assignee_id=" . getIfGet('assignee_id');
			if($_GET['page']){print "&page=" . ($_GET['page'] - 1);}
			print "\">Previous Page</a></span>";
		}

		if(count($result->results) > 99)
		{
			print "<span class=\"nextLink\"><a href=\"/zendesk-satisfaction.php?sort_order=desc";
			print "&from_date=" . getIfGet('from_date');
			print "&to_date=" . getIfGet('to_date');
			print "&query=" . getIfGet('query');
			print "&assignee_id=" . getIfGet('assignee_id');
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
	$gix = $gix + 1;
	print "<li class=\"ticket\">";
	print "<div class=\"ticketNameNumberDiv\">";
	print "<span class=\"ticketNumber\">" . ((($_GET['page'] - 1) * 100) + $gix) . "</span>";
	print "<a class=\"ticketLink\" href=\"https://dudamobile.zendesk.com/agent/#/tickets/" . $r->id . "\">" . $r->subject . "</a>";
	print "</div>";
	print "<ul class=\"ticketInfo\"><li class=\"createDate\">created: " . $r->created_at . "</p>";
	print "<li class=\"updateDate\">update: " . $r->updated_at . "</p>";
	print "<li class=\"assignee\">assignee: " . $r->assignee_id . "</p></ul>";
	print "<p class=\"comment\">" . $r->satisfaction_rating->comment . "</p>";
	print "</li>";
}
print "</ul></section>";
}
?>

<?php
include "footer.php";
?>