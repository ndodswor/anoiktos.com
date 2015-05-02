<?php 
//fizzbuzz
//a quick solution to the fizzbuzz problem
$pageTitle = "FizzBuzz";
$pageSubTitle = "(FizzBuzz)";
include "header.php"; 
include "main-nav.php"; 
include "functions.php";

//for numbers between 1 and 100, print per the fizzbuzz principle:
//if divisible by 3, print fizzbuzz
//if divisible by 5, print buzz
//if divisible by both, print fizzbuzz
//otherwise print the number
for($gix = 1; $gix <= 100; $gix++)
{
	$override = false;
	print "<br>";
	if($gix % 3 === 0)
	{
		print "Fizz";
		$override = true;
	}
	if($gix % 5 === 0)
	{
		print "Buzz";
		$override = true;
	}
	if($override === false)
	{
		print $gix;
	}
}

include "footer.php";
?>