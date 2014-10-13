<?php
require('../includes/common/common.php');
header('Cache-Control: no-cache, must-revalidate');
//header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-Type: application/json');
//header('Content-Type: application/xml');
//header('Content-Type: text/html');

echo getData();


function getData() {

	if (isset($_GET['api']))
	{

		$clean_api = sanitize::db($_GET['api']);

		$dbc = dbconnector::getConnection();
		$q = "SELECT * FROM users WHERE token='$clean_api'";
		//echo $q . "\n\n";
		$r = @mysqli_query($dbc,$q);

		if (@mysqli_num_rows($r) == 0) {
			@mysqli_close($dbc);
			return json_encode(array("status" => false,
				         "message" => "Invalid API key. Members will find their API key on their People->Account page."));
		} 
			
		$user = mysqli_fetch_assoc($r);

		// check isactive?

		// record info for the user's use of the API

		switch($_GET['action'])
		{
			case 'getKey':

				$out = peopleClass::getUserDataFromEmail($_GET['email']);


			break;
			default:
				@mysqli_close($dbc);
				return json_encode(array("status" => false,
					                     "message" => "Unrecognized action."));
		}

		@mysqli_close($dbc);
		return json_encode($out); // xmlrpc_encode

	} else {
		return json_encode(array("status" => false,
			                     "message" => "No API key. Members will find their API key on their People->Account page."));
	}


}





?>