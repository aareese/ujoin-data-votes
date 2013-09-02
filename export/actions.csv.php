<?php
echo '"Action","Bill ID","Date","Actor"'. "\n";
require_once('./export/database.php'); global $db; global $bills;
$query = json_decode(file_get_contents('./export/actions.json'), true);

$c = $bills->find($query);
foreach ($c as $id => $bill){
	$bill_id = $bill['bill_id'];
	$actions = $bill['actions'];
	foreach ($actions as $action){
		$title = $action['action'];
		$actor = $action['actor'];
		$date = $action['date'];
		?>
"<?php echo $title; ?>","<?php echo $bill_id; ?>","<?php echo $date; ?>","<?php echo $actor; ?>"
<?php
	}
}
?>	