<!-- Functions.php
Stores common functions used in the rest of the site-->

<?php

//Generic API request handler for Duda API
function dudaAPIRequest($requestType, $requestURL, $requestData = null, $isJSON = true)
{
	$authenticationInfo = "xxxxxxxxxx:xxxxxxxxxxx";
	$apiURL = "https://api.dudamobile.com/api";
	/*Initialize API*/
	$curlHandler = curl_init(); 

	/*Set options*/
	//send Data
	if($requestData != null)
	{
		curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $requestData); 
	}
	/*Set URL */
	curl_setopt($curlHandler, CURLOPT_URL, $apiURL . $requestURL);
	/*Set method (post/get/etc)*/                                                                    
	curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, $requestType);
	/* Set the function to return content */
	curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1); 
	//tell the service we're sending a JSON object
	if($isJSON)
	{
		curl_setopt($curlHandler, CURLOPT_HTTPHEADER, array(                          
	    'Content-Type: application/json',                                                                                
	    'Content-Length: ' . strlen($requestData))                                                                       
		);
	}	    
	/* Send authentication data **FOR my-test ONLY** */
	curl_setopt($curlHandler, CURLOPT_USERPWD, $authenticationInfo);  
	/* Ensure that SSL is not used **FOR my-test ONLY** */   
	curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);

	/* Run the function*/                                                                                                                                                                                              
	$result = curl_exec($curlHandler);

	/*Close the function*/
	curl_close($curlHandler);
	return $result;
}

//zendeskAPIRequest
//Generic API request handler for Zendesk API
function zendeskAPIRequest($requestType, $requestURL, $requestData = null, $isJSON = true)
{
	$authenticationInfo = "example@email.com/token:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
	$apiURL = "https://example.zendesk.com";
	/*Initialize API*/
	$curlHandler = curl_init(); 

	/*Set options*/
	//send Data
	if($requestData != null)
	{
		curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $requestData); 
	}
	/*Set URL */
	curl_setopt($curlHandler, CURLOPT_URL, $apiURL . $requestURL);
	/*Set method (post/get/etc)*/                                                                    
	curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, $requestType);
	/* Set the function to return content */
	curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1); 
	//tell the service we're sending a JSON object
	if($isJSON)
	{
		curl_setopt($curlHandler, CURLOPT_HTTPHEADER, array(                          
	    'Content-Type: application/json',                                                                                
	    'Content-Length: ' . strlen($requestData))                                                                       
		);
	}	    
	/* Send authentication data **FOR my-test ONLY** */
	curl_setopt($curlHandler, CURLOPT_USERPWD, $authenticationInfo);  
	/* Ensure that SSL is not used **FOR my-test ONLY** */   
	curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);

	/* Run the function*/                                                                                                                                                                                              
	$result = curl_exec($curlHandler);

	/*Close the function*/
	curl_close($curlHandler);
	return $result;
}

//this function generates a SSO link
function generateSSOLink($siteName,$account) {
$editor_url = 'example.exampledomain.com';
/*Set SSO Parameters*/
$dm_sig_site = $siteName;
$dm_sig_user = $account;
$apiToken = getAPIToken($account);
$apiToken = json_decode($apiToken);
$sso_link = 'http://' . $editor_url.'/home/site/'.$dm_sig_site. '?' . $apiToken->url_parameter->name . "=" . $apiToken->url_parameter->value;
return $sso_link;
}

//This function gets an API token
function getAPIToken($account){
	$apiRequest = "/accounts/sso/" . $account . "/token";
	return dudaAPIRequest("GET", $apiRequest);
}

//This function gets a user email from Zendesk
function getUserEmail($id) {
	$url = '/api/v2/users/' . $id . '.json';
	$user = zendeskAPIRequest("GET", $url);
	$user = json_decode($user);
	return $user->user->email;
}

//This function gets $valueName's value from a JSON construct.
function getValue($valueName, $toParse)
{
	$parsedValue = preg_replace("/.+\"" . $valueName . "\":\"(.*?)\".+/", "$1", $toParse);
	return $parsedValue;
}

//This function gets $valueName's value from a JSON construct if it's not a string.
function getNotStringValue($valueName, $toParse)
{
	$parsedValue = preg_replace("/.+\"" . $valueName . "\":(.*?)[\,\}]/", "$1", $toParse);
	return $parsedValue;
}

//This function gets $valueName's value from a JSON construct if it is a string.
function targetedPrintJSON($valuesToGet, $result)
{
	for ($gix = 0; $gix < count($valuesToGet); $gix++) 
	{
		if(strpos($result, $valuesToGet[$gix]) !== false)
		{
			$getValue = getValue($valuesToGet[$gix], $result);
			print $valuesToGet[$gix] . " is: " . $getValue . "
			<br>
			";
		}
	}
}

function printError($result)
{
	print "<section id=\"error\">" . getValue("error_code", $result) . " error: " . getValue("message", $result) . "</section>";
}

function getIfGet($getString)
{
	if($_GET[$getString])
	{
		return $_GET[$getString];
	}
}

function getTemplateList()
{
	$templateJSON = dudaAPIRequest(GET, "/sites/multiscreen/templates");
	return $templateJSON;
}

function isEmail($toTest)
{
	return preg_match("/[\S]+@[\S]+\.[\S]{2,4}$/i", $toTest);
}

function ifIsset($key)
{
	if(isset($_POST[$key])){return $_POST[$key];}
}
?>