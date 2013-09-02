<?php
echo '"Bill ID","Referred-Type","Date","Vote-Type","Voter-Name"'. "\n";
require_once('./export/database.php'); global $db; global $bills;
$query = json_decode(file_get_contents('./export/actions.json'), true);
define ("REFTO_BRK", 'The votes in ');
define('VT_BRK', ' were as follows: ');
define('SENLIST', 'Senator(s) ');
define('DOPARSE', 'recommend(s) that the measure be');

function printAct($bill_id, $ref, $date, $type, $name){
?>
"<?php echo $bill_id; ?>","<?php echo $ref; ?>","<?php echo $date; ?>","<?php echo $type; ?>","<?php echo $name; ?>"
<?php
}

$c = $bills->find($query);
foreach ($c as $id => $bill){
	$bill_id = $bill['bill_id'];
	$actions = $bill['actions'];
	foreach ($actions as $action){
		$date = $action['date'];
		$fulltext = $action['action'];
		if (strpos($fulltext, DOPARSE) === -1) continue;
		$ref = explode(REFTO_BRK, $fulltext); $ref = $ref[1]; $ref = explode(' ', $ref); $ref = trim($ref[0]);
		if (empty($ref)) continue;
		$past_text = explode(VT_BRK, $fulltext);
		$past_text = $past_text[1];
		$past_text = explode(";", $past_text);

		foreach ($past_text as $cats) {
			$title = explode(':', $cats);
			$results = $title[1];
			$title = $title[0];
			if (trim($results) !== 'none') {
				$type = str_replace('and  ', '', trim(str_replace(range(0,9), '', $title))); //$type = array_pop($type);
				$results = explode(SENLIST, $results); $results = $results[1];
				$results = str_replace(" ", '', $results);
				$names = explode(",",$results);
				foreach ($names as $name) { if (!empty($name)) { printAct($bill_id, $ref, $date, $type, $name); } }
			}
		}
	}
}
?>	