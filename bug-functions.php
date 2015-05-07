<?php
//translate priority number to value
function calcPriority($thisPriority)
{
	$priorityValue = "low";

	if($thisPriority > 5 && $thisPriority < 8)
	{
		$priorityValue = "medium";
	}
	if($thisPriority > 7 && $thisPriority < 11)
	{
		$priorityValue = "high";
	}
	if($thisPriority > 10 && $thisPriority < 16)
	{
		$priorityValue = "urgent";
	}
	if($thisPriority > 15)
	{
		$priorityValue = "critical";
	}
	return $priorityValue;
}

//translate date created to era
function calcEras($createdAt, $era)
{
	$age = time() - $createdAt; 
	$age = floor($age/(60*60*24));
	if($age >= $era)
	{
		return floor($age/$era);	
	}
}

?>