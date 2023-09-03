<?php


function engineResume($uniqueId)
{
	$data = array(
	  'attributes'=> array('uniqueId'=>$uniqueId),
	  'description'=>'New…',
	  'textChannel'=>false,
	  'type'=>'engineResume'
	);

	$url = "http://gegps.com:8082/api/commands/send";
	$options = array(
	  'http' => array(
		'method'  => 'POST',
		'content' => json_encode( $data ),
		'header'=>  "Content-Type: application/json\r\n" .
					"Accept: application/json\r\n"
		)
	);

	$context  = stream_context_create( $options );
	$result = file_get_contents( $url, false, $context );
	//$response = json_decode( $result );
	return $result;
}

function engineStop($uniqueId)
{
	$data = array(
	  'attributes'=> array('uniqueId'=>$uniqueId),
	  'description'=>'New…',
	  'textChannel'=>false,
	  'type'=>'engineStop'
	);

	$url = "http://gegps.com:8082/api/commands/send";
	$options = array(
	  'http' => array(
		'method'  => 'POST',
		'content' => json_encode( $data ),
		'header'=>  "Content-Type: application/json\r\n" .
					"Accept: application/json\r\n"
		)
	);

	$context  = stream_context_create( $options );
	$result = file_get_contents( $url, false, $context );
	//$response = json_decode( $result );
	return $result;
}
?>