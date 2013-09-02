<?php
echo '"Title","ID","State","Session","Chamber","Introduction Date"'."\n";

require_once('./export/database.php'); global $db; global $bills;

$query = json_decode(file_get_contents('./export/bills.json'), true);

$c = $bills->find($query);
foreach ($c as $id => $bill){
	$bill_id = $bill['bill_id'];
	$state = $bill['state'];
	$session = $bill['session'];
	$chamber = $bill['chamber'];
	$title = $bill['title'];
	$intro = $bill['actions'][0]['date'];
?>"<?php echo $title; ?>","<?php echo $bill_id; ?>","<?php echo $state; ?>","<?php echo $session; ?>","<?php echo $chamber; ?>","<?php echo $intro; ?>"
<?php
}
?>