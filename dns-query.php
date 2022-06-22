<?php
	// ======================================================
	// user config area
	// doh server(s) to proxy, set at least 1.
	$doh_servers = [
		'https://dns.adguard.com/dns-query',
		'https://mozilla.cloudflare-dns.com/dns-query',
		'https://public.dns.iij.jp/dns-query'
	];
	// set to true if u want to randomly use one of above set servers.
	// if set to false, the first one will always be used.
	$use_random = false;
	// end user config area
	// ======================================================
	// define area
	function doGet($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Chrome');
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/dns-message"));
		$response = curl_exec($ch);
		$statuscode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close($ch);
		return Array($statuscode, $response);
	}

	function doPost($url, $postdata){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Chrome');
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/dns-message"));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		$response = curl_exec($ch);
		$statuscode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close($ch);
		return Array($statuscode, $response);
	}
	// end define area
	// ======================================================
	// get POST data
	$post_data = file_get_contents('php://input'); 
	$get_data = null;
	// get GET data
	if ( isset( $_REQUEST['dns'] ) )  $get_data = $_REQUEST['dns'];
	// check
	$server_count = sizeof($doh_servers);
	if ( !( $post_data || $get_data ) || $server_count < 1 ) 
	{
		//no data found
		@http_response_code(400);
		exit();
	}
	// select server randomly if enabled
	$doh_servers_select = rand(0,$server_count-1);
	$real_doh_url = $use_random ? $doh_servers[$doh_servers_select] : $doh_servers[0];
	if ( $get_data )
	{
		$doh_resp = doGet($real_doh_url."?dns={$get_data}");
	}
	else if ($post_data)
	{
		$doh_resp = doPost($real_doh_url, $post_data);
	}
	else
	{
		@http_response_code(503);
		exit();
	}
	// if fail set response code to 503
	if ( !$doh_resp[0] )
	{
		$doh_resp[0] = 503;
	}
	@http_response_code($doh_resp[0]);
	header('Content-Type: application/dns-message');
	echo($doh_resp[1]);
	exit();

