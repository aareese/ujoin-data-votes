<?php
	$config = json_decode(file_get_contents('../config.json'), true);
	global $db; global $bills; $u = !empty($config['mongo_uri']) ? $config['mongo_uri'] : getenv ('MONGOHQ_URL');
	$m = new MongoClient($u);
	$u = explode('/', $u); $u = array_pop($u);
	$db = $m->selectDB($u);
	$bills = $db->bills;
?>