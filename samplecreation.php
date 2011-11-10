<html>
<head>
<title>Sample Script</title>
</head>
<body>
<?php

// the Battlelog Class
require 'userstatus.php';
// Proof of concept code to allow for a Murmur server to be controlled based on what server a player is in.

$email	= $_GET['email'];
$password = $_GET['password'];

// If there are times we don't need to login to battlelog, then avoid doing it.
$battlelogLoggedIn = false;
$battleLog = null;




// 	Todo: Grab the battlelog data. 
// Currently to do this will mean a key / name array will need to be stored somewhere, 
// as we don't have a way to get battlelog id's based on a name in mumble.

// Should we try to search on their nickname if it isn't in the HardCodedUserNames list.
$allowRoughSearch = true;
// Once we've made the channels, should we move people.
$allowMove = true;
// Should the new channels that are made be temporary
$makeTemp = true;
$debug = true;

// It helps to get users to register the name you hardcode, so they can't be spoofed.
$HardCodedUsers = Array("SuperRoach"=>"235161522", "FuLlTiM3KiLlA"=>"174575108");
// By name.
$HardCodedUserNames = Array("SuperRoach"=>"SuperRoach", "FuLlTiM3KiLlA"=>"FuLlTiM3KiLlA", "Nero"=>"NeroMercury", "SgtShag89"=>"SgtShag89", "Woody"=>"woodymuppet", "cozzie"=>"cozzie125", "mansonitefirefox"=>"mansonitefirefox", "homeskool2"=>"homeskool2", "kongkurr"=>"kongkurr", "Sumwhatkrazy"=>"Sumwhatkrazy", "SK1TZ"=>"pvt_SK1TZO", "GUNZ"=>"Random_Noob_007", "WidgeMan"=>"Widge_Man", "Nemesis_Ace"=>"NemisisAce", "-=T-BIRD=-"=>"I-THUNDERBIRD-I", "aeonalpha"=>"aeonalpha86", "Genesis"=>"Genesiscodee", "Daddster"=>"TheWomp", "ohhbeeee"=>"oakesybot", "jimdangles"=>"jimdangles101", "MonkeyCrumpets"=>"MonkeyCrumpets");


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

// TODO also: Set a minimum threshold for moving users (a single user may not want to be isolated)

// TODO - Create channels as temporary, or set them as temporary once people are in there. That'll handle deletion automaticly!

$allplayers = $s->getUsers();
$allchannels = $s->getChannels();
$chan = $channels[$state->channel];
$scanchannel = "Servers";
$serverRoomsContainer = 46;

$afkroom = "afk";

// To save on resources, we won't bother checking people who have been away for longer than X seconds.
$idle = 240;

//$testusername = "SuperRoach";

// UserID in murmur=>NewChannelID

// Users list of people to move
$toMove 			= array();
// Unique list of servers w/playercount
$playerCount		= array();
// Unique list of players and what server they should be in
$gameServer			= array();
// Rooms that already have been made in the past, to avoid duplication.
$existingChannels 	= array();

// Populate the previously made channels
foreach ($allchannels as $chan)
{
	if ($chan->parent == $serverRoomsContainer)
	{
		$existingchannels[$chan->name] = $chan->id;
	}
}

foreach ($allplayers as $u) {

	$worthscanning = false;
	$thisUserRoom  = null;

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
		
		// Check their Status on Battlelog
		if ($debug) echo " Do we have a hardcoded version of this name? " . $HardCodedUserNames[$u->name];
		
		if ( isset($HardCodedUserNames[$u->name]) )
		{
			$allowLookup = true;
			$soldier = $HardCodedUserNames[$u->name];
		}
		elseif ($allowRoughSearch)
		{
			$allowLookup = true;
			$soldier = $u->name;
		}
		else
		{
			$allowLookup = $false;
		}
		
		
		if ($allowLookup)
		{
			if (!$battlelogLoggedIn) 
			{ 
				$battleLog = new battleLog($email, $password); 
				$battlelogLoggedIn = true; 
			}
		
			$soldierStatus = $battleLog->SoldierOverview($soldier);
		
			$newServer = null;
			if ($battleLog->isPlaying)
			{
				$newServer = $battleLog->serverName;

				// Should abstract the creation/deletion of channels.
				// If the server hasn't been seen before, add it to the array. Playercount for server is at the end.
				if (isset($gameServer[$newServer]))
				{
					$playerCount[$newServer]++;
					
					// Put the user in an already existing room if one exists.
					$thisUserRoom = ( isset($existingchannels[$newServer]) ) ?  $existingchannels[$newServer] : $gameServer[$newServer];
					if ($debug) echo "<br>Does the room exist in previous scans? ".isset($existingchannels[$newServer])." Attempting to find previous room for $newServer, which should be $gameServer[$newServer]";
				}
				else
				{
					$playerCount[$newServer] = 1;
					

					//$gameServer[$newServer] = $gameServer[$newServer];
					// Make a room! if it's never been done before.
					
					$thisUserRoom = ( isset($existingchannels[$newServer]) ) ?  $existingchannels[$newServer] : $s->addChannel($newServer,$serverRoomsContainer);
					
					// New rooms should be made temporary.
					if (!isset($existingchannels[$newServer]) && $makeTemp) 
					{
						$thisRoomInfo =  $s->getChannelState($thisUserRoom);
						$thisRoomInfo->temporary = true;
						$s->setChannelState($thisRoomInfo);
						if ($debug) echo " Made the channel temporary ";
					}
					
					$gameServer[$newServer] = $thisUserRoom;
					if ($debug) echo "<br>Attempting to make room for $newServer, id made for $thisUserRoom";
				}
				
			}
		}
		
		
		// Set their room
		if ($state) {	
			if (empty($testusername))
			{
			
				echo "Moving";
				if ($battleLog->isPlaying)
				{
					$toMove[$u->session] = $thisUserRoom;
				}
				
				//To Move Immediately.
				//$state->channel = $thisUserRoom;
				//$s->setState($state);
			} else
			{
			
			
				if ($testusername == $u->name)
				{
					$toMove[$u->session] = $thisUserRoom;
				}
			}
		}
	}
}

	// Channel creation
	echo "<h2>Channel creation";
	
	
	foreach ($gameServer as $key => $value)
	{
		$thisroom = 0;
		
		//$thisroom =	$s->addChannel($key,46);  // 46 is the "servers" one.
		
		// Keep a record of where those rooms  are.
	}
	
	// Moving list
	echo "<h2>This is the list of ID's / People to move (from <i>$scanchannel)</i></h2>" . print_r($toMove);

	// We have a userlist, now lets move them.
	foreach ($toMove as $key => $value)
	{


		//print_r($allplayers[$key]);
		echo "Looking at: " . $allplayers[$key]->name;
		echo "<br>" . $key . " " . $value; // Gives me value of 10	
		
		// To clean up redundant state
		$userstate = $s->getState($key);
		echo "<br>Moving uid $key to $value";
		$userstate->channel = $value;
		$s->setState($userstate);
		
		// don't spam them :o
		//$s->sendMessageChannel($value, false, "Shifted you guys to new room.")
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