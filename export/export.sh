echo "Exporting bills"
> export/bills.csv
php export/bills.csv.php > export/bills.csv
echo "Bills export complete"

echo "Exporting votes"
> export/votes.csv
php export/votes.csv.php > export/votes.csv
echo "Votes export complete"

echo "Exporting votes with voter data"
> export/voters.csv
php export/voters.csv.php > export/voters.csv
echo "Votes with voter data export complete"

echo "Exporting actions"
> export/actions.csv
php export/actions.csv.php > export/actions.csv
echo "Actions export complete"

echo "Exporting actions with details"
> export/actions.details.csv
php export/actions.details.csv.php > export/actions.details.csv
echo "Actions with details export complete"

