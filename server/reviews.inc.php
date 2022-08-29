<?php
include '../server/dbh.php';
//ini_set('display_errors',1); error_reporting(E_ALL);
if(isset($_POST['ytname'], $_POST['ytimage'])) {// will not work if channel name is a quote
	session_start();		
	$ytname = $_POST['ytname'];
	$ytimg = $_POST['ytimage'];	
	
	$channel_id = $_POST['channel_id'];
	$channel_rating = intval($_POST['rating']);
	$account_id = $_SESSION['userID'];
	$date = date('Y-m-d H:i:s');
	$message = htmlspecialchars($_POST['message']);
	
	$check = "SELECT * FROM reviews WHERE channel_id=? AND account_id=?";
	$check2 = "SELECT * FROM channels WHERE channel_id='$channel_id'";
	$stmt = mysqli_stmt_init($conn);
	if(!mysqli_stmt_prepare($stmt, $check)) {
		header("Location: /rate/ouplonk.php?error=101");// does not work
	} else {// if make a review and delete it it will not work FIXED
		mysqli_stmt_bind_param($stmt, "si", $channel_id, $_SESSION['userID']);
		mysqli_stmt_execute($stmt);
		$checkresult = $stmt->get_result();
		$checkresult2 = $conn->query($check2);
	}
	setReview($conn, $checkresult, $channel_rating, $message, $date, $account_id, $channel_id);
	setChannel($conn, $checkresult2, $channel_id, $ytname, $ytimg);
}

function setReview($conn, $checkresult, $channel_rating, $message, $date, $account_id, $channel_id) {
	$stmt = mysqli_stmt_init($conn);
	if(mysqli_num_rows($checkresult) > 0) {
		if ($channel_rating >= 1 && $channel_rating <= 5 && is_int($channel_rating)) {
			$sql = "UPDATE reviews SET `rating` = ?, `message` = ? WHERE `reviews`.`account_id` = ? AND `reviews`.`channel_id` = ?";
			if(!mysqli_stmt_prepare($stmt, $sql)) {
				echo "error";
			} else {
				mysqli_stmt_bind_param($stmt, "isis", $channel_rating, $message, $account_id, $channel_id);
				if(!$stmt->execute()) {
					echo("Server error");
					//trigger_error("there was an error....".$conn->error, E_USER_WARNING);
				}
			}
		}
	} else {
		if ($channel_rating >= 1 && $channel_rating <= 5 && is_int($channel_rating)) {
			$sql = "INSERT INTO reviews (channel_id, rating, account_id, date, message) VALUES (?, ?, ?, ?, ?)";
			if(!mysqli_stmt_prepare($stmt, $sql)) {
				header("Location: /rate/ouplonk.php?error=103");
			} else {
				mysqli_stmt_bind_param($stmt, "siiss", $channel_id, $channel_rating, $account_id, $date, $message);
				if(!$stmt->execute()) {
					trigger_error("there was an error....".$conn->error, E_USER_WARNING);
				}
			}
		}
	}
}

function setChannel($conn, $checkresult2, $channel_id, $ytname, $ytimg) {
	$stmt = mysqli_stmt_init($conn);
	if (mysqli_num_rows($checkresult2) > 0) {
		$sql = "UPDATE channels SET `channel_name` = ?, `channel_avatar` = ? WHERE `channels`.`channel_id` = ?";
		if(!mysqli_stmt_prepare($stmt, $sql)) {
			header("Location: /rate/ouplonk.php?error=102");
		} else {
			mysqli_stmt_bind_param($stmt, "sss", $ytname, $ytimg, $channel_id);
			mysqli_stmt_execute($stmt);
			recalculate($conn, $channel_id);
		}
	}
	else {
		$sql = "INSERT INTO channels (channel_id, channel_name, channel_avatar) VALUES (?, ?, ?)";
		if(!mysqli_stmt_prepare($stmt, $sql)) {
			header("Location: /rate/ouplonk.php?error=104");
		} else {
			mysqli_stmt_bind_param($stmt, "sss", $channel_id, $ytname, $ytimg);
			mysqli_stmt_execute($stmt);
			recalculate($conn, $channel_id);
		}
	}
}

function recalculate($conn, $channel_id) {
	$query = "SELECT * FROM channels WHERE channel_id=?";
	$stmt = mysqli_stmt_init($conn);
	if(!mysqli_stmt_prepare($stmt, $query)) {
		header("Location: /rate/ouplonk.php?error=105");
	} else {
		mysqli_stmt_bind_param($stmt, "s", $channel_id);
		mysqli_stmt_execute($stmt);
		$channel = $stmt->get_result();
	}
	
	$sqlcount = "SELECT COUNT(*) as `coung` FROM reviews WHERE channel_id=?";
	$sqlavg = "SELECT avg(rating) as `avgrating` FROM reviews WHERE channel_id=?";
	if(!mysqli_stmt_prepare($stmt, $sqlcount)) {
		header("Location: /rate/ouplonk.php?error=106");
	} else {
		mysqli_stmt_bind_param($stmt, "s", $channel_id);
		mysqli_stmt_execute($stmt);
		$rowcount = mysqli_fetch_array($stmt->get_result());
	}
	if(!mysqli_stmt_prepare($stmt, $sqlavg)) {
		header("Location: /rate/ouplonk.php?error=107");
	} else {
		mysqli_stmt_bind_param($stmt, "s", $channel_id);
		mysqli_stmt_execute($stmt);
		$rowavg = mysqli_fetch_array($stmt->get_result());
		$avg = number_format($rowavg['avgrating'], 1);
	}
	//$rowcount = countRatings($conn);
	//$avg = averageRating($conn);
	
	$scoresql = "SELECT (SELECT COUNT(*) FROM reviews WHERE rating>3 AND channel_id=?) - (SELECT COUNT(*) FROM reviews WHERE rating<3 AND channel_id=?) AS score";
	if(!mysqli_stmt_prepare($stmt, $scoresql)) {
		header("Location: /rate/ouplonk.php?error=105");
	} else {
		mysqli_stmt_bind_param($stmt, "ss", $channel_id, $channel_id);
		mysqli_stmt_execute($stmt);
		$row = mysqli_fetch_array($stmt->get_result());
		echo $row['score'];
	}
	
	if (mysqli_num_rows($channel) != 1) {// should work when deleting
		echo "ouPAKD";
	} else {
		$sqlupdate = "UPDATE channels SET `review_count` = ?, `average_rating` = ?, `score` = ? WHERE `channels`.`channel_id` = ?";
		echo "sdgdg";
		if(!mysqli_stmt_prepare($stmt, $sqlupdate)) {
			echo "error";
		} else {
			mysqli_stmt_bind_param($stmt, "idis", $rowcount['coung'], $avg, $row['score'], $channel_id);
			mysqli_stmt_execute($stmt);
		}
	}
}

function deleteReview($conn) {// possible sql injection
	if (isset($_POST['deletereview'])) {
		$channel_id = $_POST['channel_id'];
		$account_id = $_POST['account_id'];
		
		$stmt = mysqli_stmt_init($conn);

		$sql = "DELETE FROM reviews WHERE channel_id=? AND account_id=?"; // delete review
		if(!mysqli_stmt_prepare($stmt, $sql)) {
			echo "error";
		} else {
			mysqli_stmt_bind_param($stmt, "si", $channel_id, $account_id);
			if(!$stmt->execute()) {
				trigger_error("there was an error....".$conn->error, E_USER_WARNING);
			}
		}
		recalculate($conn, $channel_id);
	}	
}	

function getTags($conn) {
	$ouasdg = $_GET['c'];
	$sql = "SELECT * FROM tags_connections INNER JOIN tags ON tags_connections.tag_id=tags.tag_id WHERE tag_channels=?";
	$stmt = mysqli_stmt_init($conn);
	if(!mysqli_stmt_prepare($stmt, $sql)) {
		echo "error";
	} else {
		mysqli_stmt_bind_param($stmt, "s", $ouasdg);
		mysqli_stmt_execute($stmt);
		$row = $stmt->get_result();
		while ($tagnames = $row->fetch_assoc()) {
			echo "<span class='m-1 bg-gray-200 hover:bg-gray-300 rounded-full px-2 font-bold text-sm leading-loose cursor-pointer'>".$tagnames['tag_name']."</span>";
		}
	}
}

function averageRating($conn) {// possible sql injection
	$ouasdg = $_GET['c'];
	$sql = "SELECT avg(rating) as `avgrating` FROM reviews INNER JOIN accounts ON reviews.account_id=accounts.account_id WHERE NOT ouplonkd=1 AND channel_id=?";
	$stmt = mysqli_stmt_init($conn);
	if(!mysqli_stmt_prepare($stmt, $sql)) {
		echo "error";
	} else {
		mysqli_stmt_bind_param($stmt, "s", $ouasdg);
		mysqli_stmt_execute($stmt);
		$row = mysqli_fetch_array($stmt->get_result());
		$formatted = number_format($row['avgrating'], 1);
		echo $formatted;
	}
}

function countRatings($conn) {// possible sql injection
	$ouasdg = $_GET['c'];
	$sql = "SELECT COUNT(*) as `coung` FROM reviews WHERE channel_id=?";
	$stmt = mysqli_stmt_init($conn);
	if(!mysqli_stmt_prepare($stmt, $sql)) {
		echo "error";
	} else {
		mysqli_stmt_bind_param($stmt, "s", $ouasdg);
		mysqli_stmt_execute($stmt);
		$row = mysqli_fetch_array($stmt->get_result());
		//echo $row['coung'];
		return $row['coung'];
	}
}

function getLogin($conn) {
	$uid = $_POST['uid'];
	$pwd = $_POST['pwd'];
	
	$sql = "SELECT * FROM accounts WHERE uid='$uid' AND pwd='$pwd'";
	$result = $conn->query($sql);
	if(mysqli_num_rows($result) > 0) {
		if ($row = $result->fetch_assoc()) {
			$_SESSION['userID'] = $row['account_id'];
			header("Location: ../index.php?login=success");
			exit();
		}
	}
	else {
		header("Location: ../index.php?login=failed");
		exit();
	}
	
}
function timeago($timestamp) {
	$time_ago = strtotime($timestamp);
	$current_time = time();
	$time_difference = $current_time - $time_ago;
	$seconds = $time_difference;
	$minutes = round($seconds/60);
	$hours = round($seconds/3600);
	$days = round($seconds/86400);
	$weeks = round($seconds/604800);
	$months = round($seconds/2629440);
	$years = round($seconds/31553280);
	
	if($seconds <= 60) {
		return "Just now";
	}
	else if($minutes <= 60) {
		if($minutes==1) {
			return "1 minute ago";
		}
		else {
			return "$minutes minutes ago";
		}
	}
	else if($hours <= 24) {
		if($hours==1) {
			return "1 hour ago";
		}
		else {
			return "$hours hours ago";
		}
	}
	else if($days <= 7) {
		if($days==1) {
			return "1 day ago";
		}
		else {
			return "$days days ago";
		}
	}
	else if($weeks <= 4.3) {
		if($weeks==1) {
			return "1 week ago";
		}
		else {
			return "$weeks weeks ago";
		}
	}
	else if($months <= 12) {
		if($months==1) {
			return "1 month ago";
		}
		else {
			return "$months months ago";
		}
	}
	else {
		if($years==1) {
			return "1 year ago";
		}
		else {
			return "$years years ago";
		}
	}
}