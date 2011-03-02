#!/usr/bin/env php
<?php

// Configuration:
$apiKey   = 'YOUR API KEY HERE';
$username = 'user@example.com';
// End of configuration

require_once('../NutshellApi.php');
$api = new NutshellApi($username, $apiKey);

/**
 * Example: Retrieving entities from the Nutshell API using find* and get* methods
 * 
 * Relevant documentation:
 * http://www.nutshell.com/api/retrieving-editing.html
 * http://www.nutshell.com/api/finding-searching.html
 */

// Retrieve a contact
echo "getContact example\n------------------------------------------------\n";
$params = array( 'contactId' => 132 );
$result = $api->call('getContact', $params);
var_dump($result);

// Find contacts attached to lead #1209
echo "\nfindContacts example\n------------------------------------------------\n";
$params = array(
		'query' => array(
			'leadId' => 1209,
		),
);
$result = $api->findContacts($params);
var_dump($result);

echo "\n";
