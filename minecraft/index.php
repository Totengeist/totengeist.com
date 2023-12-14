<?php

use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;
use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;
// Edit this ->
define( 'MQ_SERVER_ADDR', '10.124.0.3' );
define( 'MQ_SERVER_PORT', 25565 );
define( 'FG_SERVER_PORT', 19132 );
define( 'MQ_TIMEOUT', 1 );
// Edit this <-
// Display everything in browser, because some people can't look in logs for errors
require __DIR__ . '/mcquery/src/MinecraftPing.php';
require __DIR__ . '/mcquery/src/MinecraftPingException.php';
require __DIR__ . '/mcquery/src/MinecraftQuery.php';
require __DIR__ . '/mcquery/src/MinecraftQueryException.php';
header('Content-Type: application/json; charset=utf-8');

$Info = false;
$Query = null;
try
{
	$Query = new MinecraftPing( MQ_SERVER_ADDR, MQ_SERVER_PORT, MQ_TIMEOUT );
	$Info = $Query->Query( );
	if( $Info === false )
	{
		/*
		 * If this server is older than 1.7, we can try querying it again using older protocol
		 * This function returns data in a different format, you will have to manually map
		 * things yourself if you want to match 1.7's output
		 *
		 * If you know for sure that this server is using an older version,
		 * you then can directly call QueryOldPre17 and avoid Query() and then reconnection part
		 */
		$Query->Close( );
		$Query->Connect( );
		$Info = $Query->QueryOldPre17( );
	}
}
catch( MinecraftPingException $e )
{
	echo json_encode(['online' => false]);
	die();
}
if( $Query !== null )
{
	$Query->Close( );
} else {
	echo json_encode(['online' => false]);
	die();
}

$FGInfo = false;
$FGQuery = null;
try
{
        $FGQuery = new MinecraftQuery( );
        $FGQuery->ConnectBedrock( MQ_SERVER_ADDR, FG_SERVER_PORT );
        $FGInfo = $FGQuery->GetInfo( );
}
catch( MinecraftQueryException $e ){}
if( $FGInfo != false ) {
	if( isset( $FGInfo['HostName'] ) ) {
		$Info['floodgate_online'] = true;
		$Info['floodgate'] = $FGInfo;
	}
}

if( $Info['players']['online'] > 0 ) {
	$Info['players']['list'] = [];
	foreach($Info['players']['sample'] as $player) {
		$Info['players']['list'][] = $player['name'];
	}
}
$Info['version']['software'] = explode(" ", $Info['version']['name'])[0];
$Info['version']['number'] = explode(" ", $Info['version']['name'])[1];
$Info['online'] = true;
echo json_encode($Info);
