<?php

include_once("config.php");

function getStoreNames(){
	global $conn;
	$storeNames = array();
	$sql='select * from Stores'; //counting on this being an index
	$stmt=$conn->prepare($sql);
	//$stmt->bind_param("s","getStoreNames");
	if($stmt->execute()){
		$stmt->bind_result($sid,$name);
	} else{
		echo "Nope";
	}

	//dictionary with name as value, store sid as key
	while($stmt->fetch()){
		// $storeNames[$sid] = $name;
		$storeNames[$name] = $sid;
	}

	return $storeNames;
}

//Useful debug function
function debug_to_console($data){
  $output = $data;
  if (is_array($output))
      $output = implode(',', $output);

  echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

?>