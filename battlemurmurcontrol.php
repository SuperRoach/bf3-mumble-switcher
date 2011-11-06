<html>
<head>
<title>Userlist</title>
</head>
<body>
<?php
// Proof of concept code to allow for a Murmur server to be controlled based on what server a player is in.


// 	Todo: Grab the battlelog data. 
// Currently to do this will mean a key / name array will need to be stored somewhere, 
// as we don't have a way to get battlelog id's based on a name in mumble.

$debug = true;

// It helps to get users to register the name you hardcode, so they can't be spoofed.
$HardCodedUsers = Array("SuperRoach"=>"235161522", "FuLlTiM3KiLlA"=>"174575108");



Ice_loadProfile();  // This requires you to have ice installed on your server which is running murmur.

try {
  $base = $ICE->stringToProxy("Meta:tcp -h 127.0.0.1 -p 6502");
  $meta = $base->ice_checkedCast("::Murmur::Meta");

  $servers = $meta->getBootedServers();
  $default = $meta->getDefaultConf();
  foreach($servers as $s) {
    $name = $s->getConf("registername");

    if (! $name) {
      $name =  $default["registername"];
    }

	var_dump($s);
    // $s->sendMessageChannel(0,true,"User <b>SuperRoach</b> has joined BF3 Server, preparing to move");
  
    // $s->addChannel("Test New BF3 Server Connection Guid #",46);  // 46 is the "servers" one.

// TODO: Make this get the users of a channel instead of globally.
$allplayers = $s->getUsers();
$allchannels = $s->getChannels();
$chan = $channels[$state->channel];

$newroom = 46;
$scanchannel = "Servers";
$afkroom = "afk";
$idle = 240;

//$testusername = "SuperRoach";

// UserID in murmur=>NewChannelID
$tomove = array();

foreach ($allplayers as $u) {

	$worthscanning = false;

	if ($debug) printf("%s: %s\n", $u->name, $u->idlesecs);
	if ($debug) var_dump($u->channel);
	if ($debug) echo $u->session . "<br>";

		
	// Get the persons information in murmur
	$state = $s->getState($u->session);
	
	$chan = $allchannels[$state->channel];

	// First level check for people
	echo $chan->name . "   <b>..-..</b>   " . $scanchannel . " ";	
	
	//echo "<h6>testing channel: </h6>" . $u->channel . " | ".  $chan->name ;
	if ($u->idlesecs < $idle && $chan->name == $scanchannel && $u->channel != $afk) {

		if ($debug) echo "About to move $u->name <br>" ; 
		
		
		
		// Set their room
		if ($state) {	
			if (empty($testusername))
			{
				echo "Moving";
				$tomove[$u->session] = $newroom;
				
				//To Move Immediately.
				//$state->channel = $newroom;
				//$s->setState($state);
			} else
			{
				if ($testusername == $u->name)
				{
					$tomove[$u->session] = $newroom;
				}
			}
		}
	}
}

	// Moving list
	echo "<h2>This is the list of ID's / People to move (from <i>$scanchannel)</i></h2>" . print_r($tomove);

	// We have a userlist, now lets move them.

	foreach ($tomove as $key => $value)
	{

		//print_r($allplayers[$key]);
		echo "Looking at: " . $allplayers[$key]->name;
		echo "<br>" . $key . " " . $value; // Gives me value of 10	
		
	}

	// Debug information.

    echo "<h1>SERVER #" . $s->id() . " " .$name ."</h1>\n";
    echo "<table><tr><th>Name</th><th>Channel</th></tr>\n";
    $channels = $s->getChannels();
	
    $players = $s->getUsers();

	//var_dump($channels);

	//print_r($players);
	print( count($players) );
	
    foreach($players as $id => $state) {
	//var_dump($id);
      $chan = $channels[$state->channel];
      echo "<tr><td>".$state->name."</td><td>".$chan->name." <i>(id: ". $chan->id .")</i></td></tr>\n";
    }
    echo "</table>\n";
	
	
  // Per server option finished
  }

  // We've tried but found a problem to connect to the ice server!
  } catch (Ice_LocalException $ex) {
  print_r($ex);
}

?>
</body>
</html>