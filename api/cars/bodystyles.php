<?php
require 'dbh.php';

$bs = $_GET['body_style'];

$response = array();

$sql2 = "SELECT make FROM cars WHERE body_styles LIKE '%$bs%'";
$result = mysqli_query($conn, $sql2);


if ($result) {
	header("Content-Type: JSON");
	$i = 0;
	while ($row = mysqli_fetch_assoc($result)) {
		$response[$i] = $row['make'];
		$i++;
	}
	//print_r(array_unique($response));
	echo json_encode(array_values(array_unique($response)), JSON_PRETTY_PRINT);
}
else {
    echo("error");
}

?>