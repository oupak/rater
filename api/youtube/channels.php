<?php
require '../../server/dbh.php';

$cid = $_GET['channel'];

$response = array();

$sql = "SELECT * FROM channels WHERE channel_id=?";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
	exit();
}
else {
	mysqli_stmt_bind_param($stmt, "s", $cid);
	mysqli_stmt_execute($stmt);
	$result = $stmt->get_result();
	if ($result) {
		header("Content-Type: JSON");
		$i = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			$response[$i]['id'] = $row['channel_id'];
			$response[$i]['reviewCount'] = $row['review_count'];
			$response[$i]['averageRating'] = $row['average_rating'];
			$i++;
		}
		echo json_encode($response, JSON_PRETTY_PRINT);
	}
}
?>