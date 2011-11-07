<?php

$email	= $_GET['email'];
$password = $_GET['password'];

class battleLog
{
	public $baseUrl;
	public $debug;
	private $loginResult;
	public $serverName;
	public $serverGuid;
	public $isPlaying;
	
	
	
	public function __construct($email, $password)
	{
		$this->baseUrl = 'https://battlelog.battlefield.com/bf3/';
		$this->debug = true;
		
		// Create required post fields
		$postchars = http_build_query(array(
			'redirect' => '|bf3|',
			'email' => $email,
			'password' => $password,
			'submit' => 'Sign+in'
		), '', '&');
		
		echo $postchars;
		
		$url = $this->baseUrl . 'gate/login/';

		// First we need to do a login for the commands we will be doing. 
		// This will give us some session data.

		if ($this->debug) echo "<h3>the post url will be:</h3> " . $url ."<br>";

		// Do a login using your information.
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postchars);
		curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:8.0) Gecko/20100101 Firefox/8.0');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$this->loginresult = curl_exec($ch);
		
		// Some way to verify login?

		if ($this->debug) echo "<br> Curl Error for " . $info['url'] . " : " . curl_errno($ch) . " <br>";
		if ($this->debug) echo "<br><h3>Attempting login</h3>" . curl_getinfo ($ch, CURLINFO_HTTP_CODE) . " - Is the resulting code. 302 is a redirect, 200 is ok.";
		curl_close($ch);
	}
 

 public function SoldierOverview($soldierID)
 {
 //http://battlelog.battlefield.com/bf3/user/overviewBoxStats/2832660534553355381/
	$url = $this->baseUrl . "user/superroach/";
	$url = $this->baseUrl . "user/MonkeyCrumpets/";
	$url = $this->baseUrl . "user/SumwhatKrazy/";
	$url = $this->baseUrl . "user/bb_turn/";
	$url = $this->baseUrl . "user/DrMon/";
	$url = $this->baseUrl . "user/-Vultur3z-/";
	echo $url;
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($ch, CURLOPT_POST, 1);
	//curl_setopt($ch, CURLOPT_POSTFIELDS, $postchars);
	curl_setopt($ch, CURLOPT_HTTPHEADER,array('X-Requested-With: XMLHttpRequest','X-AjaxNavigation: 1'));
	curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
	curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:8.0) Gecko/20100101 Firefox/8.0');
	$soldierResult = curl_exec($ch);
	
	if ($this->debug) echo "<br><h3>Attempting soldier request.</h3>" . curl_getinfo ($ch, CURLINFO_HTTP_CODE) . " - Is the resulting code. 302 is a redirect, 200 is ok.";
	$info = curl_getinfo($ch);

	if ($this->debug) echo "<br> Curl Error for " . $info['url'] . " : " . curl_errno($ch) . " <br>";
	//echo "<br>" . $soldierData;
	//var_dump($soldierData);
	//$jsonSoldier = json_encode( $soldierData );
	

	$soldierData = json_decode($soldierResult, true);
	
	$this->isPlaying = (bool) $soldierData['context']['profileCommon']['user']['presence']['isPlaying'];
	$this->serverName = $soldierData['context']['profileCommon']['user']['presence']['serverName'];
	$this->serverGuid = $soldierData['context']['profileCommon']['user']['presence']['serverGuid'];
	
	if ($this->debug) var_dump( $this->isPlaying );
	if ($this->debug) echo "<br> Are they playing?: <b>" . $this->isPlaying . "</b><br>";
	if ($this->debug) echo "<br> Checking their server name: <b>" . $soldierData['context']['profileCommon']['user']['presence']['serverName'] . "</b><br><br>";
	
	// if ($this->debug) echo "<br> And :".var_dump( $soldierData['context']['profileCommon']['user'] )."</br>";

	echo "<pre>";
	// Uncomment this for the whole shebang.
	// if ($this->debug) print_r($soldierData);
	echo "</pre>";

	curl_close($ch);
	return $soldierData;		 
 }

public function SoldierStats($soldierID)
{

/*
	if (empty($this->loginResult))
	{
		echo "<Br>The returned result from logining in is empty";
		return false;
	}
*/

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

	$url = $this->baseUrl . "overviewPopulateStats/$soldierID/None/1/";
	echo $url;
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($ch, CURLOPT_POST, 1);
	//curl_setopt($ch, CURLOPT_POSTFIELDS, $postchars);
	curl_setopt($ch, CURLOPT_HTTPHEADER,array('X-Requested-With: XMLHttpRequest','X-AjaxNavigation: 1'));
	curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
	curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:8.0) Gecko/20100101 Firefox/8.0');
	$soldierResult = curl_exec($ch);
	
	echo "<br><h3>Attempting soldier request.</h3>" . curl_getinfo ($ch, CURLINFO_HTTP_CODE) . " - Is the resulting code. 302 is a redirect, 200 is ok.";
	$info = curl_getinfo($ch);

	echo "<br> Curl Error for " . $info['url'] . " : " . curl_errno($ch) . " <br>";
	//echo "<br>" . $soldierData;
	//var_dump($soldierData);
	//$jsonSoldier = json_encode( $soldierData );
	

	$soldierData = json_decode($soldierResult, true);

	//echo "<br>" . $soldierData[user][prescence]['userId'] ;
	//echo "<br>" . print_r($soldierData['data']);
	//echo "<br>" . $soldierData['user']['username']	;

	//var_dump($soldierData);

//	$soldierInfoData = json_decode( $jsonSoldier , true );
	
	//print_r( $soldierInfoData);
//	echo $soldierInfoData['type'];
	
//	echo $soldierData['type']  . " ";
//	echo $jsonSoldier.user.username  . " ";

	curl_close($ch);
	return $soldierData;			
}

}


// SuperRoach
//The battlelog soldier id.
$soldier = "235161522";
if (!empty($_GET['soldier']))
{
	$soldier = $_GET['soldier'];
}



//$ret = file_get_contents($url, false, $context); 

/*
$test = new battleLog($email, $password);
$soldierStatus = $test->SoldierOverview($soldier);

$newServer = null;
if ($test->isPlaying)
{
	$newServer = $test->serverName;
}

echo $newServer;
*/

?>