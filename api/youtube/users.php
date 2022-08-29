<?php
require '../../server/dbh.php';

$uid = $_GET['id'];

$response = array();

$sql = "SELECT COUNT(*) AS reviewcount FROM `accounts` INNER JOIN reviews ON `reviews`.`account_id`=`accounts`.`account_id` WHERE `accounts`.`account_id`=?";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
	exit();
}
else {
	mysqli_stmt_bind_param($stmt, "s", $uid);
	mysqli_stmt_execute($stmt);
	$result = $stmt->get_result();
	if ($result) {
		header("Content-Type: JSON");
		$i = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			$response[$i]['reviewCount'] = $row['reviewcount'];
			$i++;
		}
		echo json_encode($response, JSON_PRETTY_PRINT);
	}
}
?>