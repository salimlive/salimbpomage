<?php
$client = new SoapClient('http://salim.bpopower.com/api/soap/?wsdl');
// If some stuff requires api authentification,
// then get a session token
$session = $client->login('admin', 'Admin786!@#');

// get attribute set
$result = $client->call($session, 'catalog_product.list');
var_dump($result);
// If you don't need the session anymore
//$client->endSession($session);
?>
