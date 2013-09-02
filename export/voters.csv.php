<?php
echo '"Vote-ID","Bill-ID","Legislator-ID","Date","Vote-Type","Voter-Name"'. "\n";

define("LEG_RM_CHAR", ';');

require_once('./export/database.php'); global $db; global $bills; global $vote_types;

$vote_types = array(
	"other" => 'Other',
	"yes" => "Yes",
	"no" => "No"
);

$query = json_decode(file_get_contents('./export/voters.json'), true);

function print_vote ($type, $vote, $bill_id) { global $vote_types;
			foreach ($vote[$type . '_votes'] as $leg) { ?>
"<?php echo $vote['id']; ?>","<?php echo $bill_id; ?>","<?php echo $leg['leg_id']; ?>","<?php echo $vote['date']; ?>",<?php echo $vote_types[$type]; ?>,<?php echo trim(str_replace("\t", '', str_replace(LEG_RM_CHAR, '', $leg['name']))); ?>
<?php
		}
} 

$c = $bills->find($query);
foreach ($c as $id => $bill){
	$bill_id = $bill['bill_id'];
	$title = $bill['title'];
	$votes = $bill['votes'];
	foreach ($votes as $vote){
		foreach ($vote_types as $type => $cas) {
			print_vote($type, $vote, $bill_id);
		}
	}
}
?>