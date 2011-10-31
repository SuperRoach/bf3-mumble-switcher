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
//    $s->sendMessageChannel(0,true,"User <b>SuperRoach</b> has joined BF3 Server, preparing to move");
    $s->addChannel("Test New BF3 Server Connection Guid #",46);  // 46 is the "servers" one.

// TODO: Make this get the users of a channel instead of globally.
$players = $s->getUsers();
$newroom = 46;

$testusername = "SuperRoach";

foreach ($players as $u) {
	printf("%s: %s\n", $u->name, $u->idlesecs);
//	var_dump($u);
	if ($u->idlesecs > $idle && $u->channel != $afk) {
		
		// Get their current room.
		$state = $s->getState($u->session);
		if ($state && $u->name == $testusername) {

		echo "Moving";
			$state->channel = $newroom;
			$s->setState($state);
		}
	}
}


// Debug information.

    echo "<h1>SERVER #" . $s->id() . " " .$name ."</h1>\n";
    echo "<table><tr><th>Name</th><th>Channel</th></tr>\n";
echo "test time!";
    $channels = $s->getChannels();
    $players = $s->getUsers();

//	var_dump($channels);

	//print_r($players);
	print( count($players) );
	
    foreach($players as $id => $state) {
	var_dump($id);
      $chan = $channels[$state->channel];
      echo "<tr><td>".$state->name."</td><td>".$chan->name."</td></tr>\n";
    }
    echo "</table>\n";
  }
} catch (Ice_LocalException $ex) {
  print_r($ex);
}

echo "done!";

?>
</body>
</html>
