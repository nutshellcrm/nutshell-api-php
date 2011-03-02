#!/usr/bin/env php
<?php

// Configuration:
$apiKey   = 'YOUR API KEY HERE';
$username = 'user@example.com';
// End of configuration

require_once('../NutshellApi.php');
$api = new NutshellApi($username, $apiKey);

/**
 * Example: creating an account, contact, and lead
 * 
 * We will create a new contact, then add a new account associated with that contact,
 * then finally create a new lead involving that account/contact.
 * 
 * Relevant documentation:
 * http://www.nutshell.com/api/detail/class_nut___api___core.html
 */

// Create a new contact and save its ID to $newContactId
$params = array(
	'contact' => array(
		'name' => 'Joan Smith',
		'phone' => array(
			'734-555-9090',
			'cell' => '734-555-6711',
		),
		'email' => array(
			'jsmith@example.com',
			'blackberry' => 'jsmith@att.blackberry.com',
		),
	),
);
$newContact = $api->call('newContact', $params);
$newContactId = $newContact->id;

// Create a new account that includes the contact we just added
$params = array(
	'account' => array(
		'name' => 'Arbor Medical LLC',
		'industryId' => 1,
		'url' => array(
			'http://example.com',
			'http://suppliers.example.com',
		),
		'phone' => array(
			'734-234-9990',
		),
		'contacts' => array(
			array(
				'id' => $newContactId,
				'relationship' => 'Purchasing Manager'
			),
		),
		'address' => array(
			'office' => array(
				'address_1'  => '220 Depot St',
				'city'       => 'Ann Arbor',
				'state'      => 'MI',
				'postalCode' => '48104',
			),
		),
	),
);
$newAccount = $api->newAccount($params);
$newAccountId = $newAccount->id;

// Finally, create a lead that includes the account we just added
$params = array(
	'lead' => array(
		'primaryAccount' => array('id' => $newAccountId),
		'confidence' => 70,
		'market'   => array('id' => 1),
		'contacts' => array(
			array(
				'relationship' => 'First Contact',
				'id'           => $newContactId,
			),
		),
		'products' => array(
			array(
				'relationship' => '',
				'quantity'     => 15,
				'price'        => array(
					'currency_shortname' => 'USD',
					'amount'   => 1000,
				),
				'id'           => 4,
			),
		),
		'sources' => array(
			array('id' => 2),
		),
		'assignee' => array(
			'entityType' => 'Teams',
			'id' => 1000,
		),
	),
);
$result = $api->newLead($params);
var_dump($result);

echo "\n";