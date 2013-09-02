<?php
echo '"ID","Bill ID","Date","Passed","Other Count","Yes Count","No Count"'. "\n";

require_once('./export/database.php'); global $db; global $bills;

$query = json_decode(file_get_contents('./export/votes.json'), true);

$c = $bills->find($query);
foreach ($c as $id => $bill){
	$bill_id = $bill['bill_id'];
	$title = $bill['title'];
	$votes = $bill['votes'];
	foreach ($votes as $vote){
		$id = $vote['vote_id'];
		$date = $vote['date'];
		$passed = $vote['passed'] ? 'Yes' : 'No';
		$counts = array(
			'other' => $vote['other_count'],
			'yes' => $vote['yes_count'],
			'no' => $vote['no_count']
		);
		?>
"<?php echo $id; ?>","<?php echo $bill_id; ?>","<?php echo $date; ?>","<?php echo $passed; ?>",<?php echo $counts['other']; ?>,<?php echo $counts['yes']; ?>,<?php echo $counts['no']; ?>
<?php echo "\n";
	}
}
?>