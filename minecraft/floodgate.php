<?php

use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;
// Edit this ->
define( 'MQ_SERVER_ADDR', '10.124.0.3' );
define( 'MQ_SERVER_PORT', 19132 );
define( 'MQ_TIMEOUT', 1 );
// Edit this <-
// Display everything in browser, because some people can't look in logs for errors
require __DIR__ . '/mcquery/src/MinecraftQuery.php';
require __DIR__ . '/mcquery/src/MinecraftQueryException.php';
header('Content-Type: application/json; charset=utf-8');

$Info = false;
$Players = false;
$Query = null;
try
{
	$Query = new MinecraftQuery( );
	$Query->ConnectBedrock( MQ_SERVER_ADDR, MQ_SERVER_PORT );
	$Info = $Query->GetInfo( );
}
catch( MinecraftPingException $e )
{
	echo json_encode(['online' => false]);
	die();
}
echo json_encode($Info);
