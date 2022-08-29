<?php
require '../../server/dbh.php';
require '../../server/timeago.php';

$cid = $_GET['channel'];
$ro = $_GET['offset'];
$rl = $_GET['limit'];

$response = array();
$items = array();

$sql = "SELECT * FROM reviews INNER JOIN accounts ON `reviews`.`account_id`=`accounts`.`account_id` WHERE `reviews`.`channel_id`=? AND NOT (`message` LIKE '%fuck%' OR `message` LIKE '%shit%' OR `message` LIKE '%nigg%' OR `message` LIKE '%bitch%' OR `message` LIKE '%kill%' OR `message` LIKE '%dick%') ORDER BY date DESC LIMIT ?, ?";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
	exit();
}
else {
	mysqli_stmt_bind_param($stmt, "sii", $cid, $ro, $rl);
	mysqli_stmt_execute($stmt);
	$result = $stmt->get_result();
	if (mysqli_num_rows($result) > 0) {
		header("Content-Type: JSON");
		$i = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			$items[$i]['id'] = $row['channel_id'];
			$items[$i]['postedBy'] = $row['account_uid'];
			$items[$i]['rating'] = $row['rating'];
			$items[$i]['datePosted'] = timeago($row['date']);
			$items[$i]['reviewContent'] = htmlspecialchars_decode($row['message']);
			$i++;
		}
		$response['success'] = 'true';
		$response['items'] = $items;
		echo json_encode($response, JSON_PRETTY_PRINT);
	}
	else {
		$response['success'] = 'false';
		echo json_encode($response, JSON_PRETTY_PRINT);
	}
}
?>