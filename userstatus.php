<?php

$email	= $_GET['email'];
$password = $_GET['password'];

$baseUrl = 'https://battlelog.battlefield.com/bf3/';
 
// First we need to do a login for the commands we will be doing. 
// This will give us some session data.

// Create required post fields
$postchars = http_build_query(array(
	'redirect' => '|bf3|',
	'email' => $email,
	'password' => $password,
	'submit' => 'Sign+in'
), '', '&');


// Your post string

echo "Post string<br>". $postchars;
	
	
$url = $baseUrl . 'gate/login/';
echo "<h3>the post url will be:</h3> " . $url ."<br>";

/*
$cha = curl_init("http://battlelog.battlefield.com/bf3/gate");
$aloginresult = curl_exec($cha);

curl_close($cha);
*/

// Do a login using your information.
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postchars);
curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:8.0) Gecko/20100101 Firefox/8.0');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$loginresult = curl_exec($ch);


echo "<br><h3>Attempting login</h3>" . curl_getinfo ($ch, CURLINFO_HTTP_CODE) . " - Is the resulting code. 302 is a redirect, 200 is ok.";


if (empty($loginresult))
{
echo "The returned result from logining in is empty";
}
curl_close($ch);

// I've got rid of stream usage while debugging this script.
$context = stream_context_create(array( 
    'http' => array(
		'method'  => 'POST',
		'header'  => 'content-type: application/x-www-form-urlencoded\r\n' .
					'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:8.0) Gecko/20100101 Firefox/8.0' .
					'Connection: keep-alive' .
					'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',					
		'content' => $dataUrl,
		'timeout' => 3,
		)
));


function BF3SoldierInfo($soldierId)
{
		$ch = curl_init($baseUrl . "overviewPopulateStats/$soldierId/None/1/");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
		curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:8.0) Gecko/20100101 Firefox/8.0');
		$soldierData = curl_exec($ch);
		
		echo "<br><h3>Attempting soldier request.</h3>" . curl_getinfo ($ch, CURLINFO_HTTP_CODE) . " - Is the resulting code. 302 is a redirect, 200 is ok.";
		echo $soldierData;
		var_dump($soldierData);
		
		
		curl_close($ch);
		
		
}

// SuperRoach
//The battlelog soldier id.
$soldier = "235161522";
if (!empty($_GET['soldier']))
{
	$soldier = $_GET['soldier'];
}

BF3SoldierInfo($soldier);

/*
$context = stream_context_create(array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"Accept-language: en\r\n" .
              "Cookie: foo=bar\r\n"
  )
));
*/


//$ret = file_get_contents($url, false, $context); 

?>