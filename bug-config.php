<?php
//configuration file for bug priority

//if you add a new one, be sure to also add it to the column titles above
$priorityFields = array(
	"33333333" => array("name" => "No Customer Workaround", "weight" => 3),
	"33333333" => array("name" => "No Workaround", "weight" => 2),
	"33333333" => array("name" => "Business Impact", "weight" => 2),
	"33333333" => array("name" => "Vocal", "weight" => 1),
	"33333333" => array("name" => "Common", "weight" => 6),
);

//set weight for non-array priority fields
$eraWeight = 1;

//set weight for user status
$tierWeight = array(
	"1" => 4,
	"2" => 2,
	"3" => 1,
	"4" => 1,
);

$era = 21; //number of days per 'era' (point added for age)

?>