#!/usr/bin/env php
<?php

// Configuration:
$apiKey   = 'YOUR API KEY HERE';
$username = 'user@example.com';
// End of configuration

require_once('../NutshellApi.php');
$api = new NutshellApi($username, $apiKey);

/**
 * Example: editing a lead
 * 
 * We will add a contact and change the confidence for a lead.
 * To keep the existing contacts, we need to include them in our request.
 * 
 * Relevant documentation:
 * http://nutshell.com/api/retrieving-editing.html
 * http://www.nutshell.com/api/revs-etags.html
 */

// Get the lead so we have an up-to-date rev and the current contacts array
$leadId      = 1600;
$params      = array( 'leadId' => $leadId );
$oldLead     = $api->call('getLead', $params);
$rev         = $oldLead->rev;
$oldContacts = $oldLead->contacts;

// Build new contacts array containing the old contacts plus the one we want to add
$contacts = array();
foreach ($oldContacts as $contact) {
	$contacts[] = array(
		'id' => $contact-> id,
		'relationship' => $contact->relationship,
		'entityType' => 'Contacts',
		// entityType is required for updating multivalue keys when editing a lead.
		// this requirement will be removed in a future API release.
	);
}
$contacts[] = array(
	'relationship' =>'additional contact',
	'id'           => 17,
	'entityType' => 'Contacts',
	// entityType is required for updating multivalue keys when editing a lead.
	// this requirement will be removed in a future API release.
);

// edit the lead
$params = array(
	'leadId' => $leadId,
	'rev'    => $rev,
	'lead'   => array(
		'confidence' => 75,
		'contacts'   => $contacts,
	),
);
$result = $api->editLead($params);
var_dump($result);

echo "\n";
