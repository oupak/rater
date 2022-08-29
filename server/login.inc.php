<?php

if (isset($_POST['login-submit'])) {
	
	require 'dbh.php';
	
	$emailaddress = $_POST['email'];
	$password = $_POST['pwd'];
	
	if (empty($emailaddress) || empty($password)) {
		header("Location: ../login.php?error=emptyfields&email=".$emailaddress);
		exit();
	}
	else {
		$sql = "SELECT * FROM accounts WHERE account_email=?;";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)) {
			header("Location: ../login.php?error=sqlerror");
			exit();
		}
		else {
			mysqli_stmt_bind_param($stmt, "s", $emailaddress);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if ($row = mysqli_fetch_assoc($result)) {
				$pwdCheck = password_verify($password, $row['account_pwd']);
				if ($pwdCheck == false) {
					header("Location: ../login.php?error=incorrect_password&email=".$emailaddress);
					exit();
				}
				else if ($pwdCheck == true) {
					session_start();
					$_SESSION['userID'] = $row['account_id'];
					$_SESSION['userUID'] = $row['account_uid'];
					
					header("Location: ../youtube/index.php?login=success");
					exit();
				}
				else {
					header("Location: ../login.php?error=incorrect_password&email=".$emailaddress);
					exit();
				}
			}
			else {
				header("Location: ../login.php?error=nouser");
				exit();
			}
		}
	}
}
else {
	header("Location: ../youtube/index.php?error=");
	exit();
}