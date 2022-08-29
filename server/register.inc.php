<?php
//ini_set('display_errors',1); error_reporting(E_ALL);
if (isset($_POST['register-submit'])) {
	
	require 'dbh.php';
	
	$username = $_POST['uid'];
	$email = $_POST['email'];
	$password = $_POST['pwd'];
	$defaultouplonk = 0;
	
	$secretKey = "6LcGPF0aAAAAAFK_rAia1DjLFHE38K-kHMkkLr4a";
	$responseKey = $_POST['g-recaptcha-response'];
	$userIP = $_SERVER['REMOTE_ADDR'];
	$url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responseKey&remoteip=$userIP";
	
	$response = file_get_contents($url);
	$response = json_decode($response);
	
	if (empty($username) || empty($email) || empty($password)) {
		header("Location: ../register.php?error=emptyfields&uid=".$username."&email=".$email);
		exit();
	}
	else if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Z0-9_ -]*$/", $username)) {
		header("Location: ../register.php?error=invalid_uid_and_email");
		exit();
	}
	else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		header("Location: ../register.php?error=invalid_email&uid=".$username);
		exit();
	}
	else if (!preg_match("/^[a-zA-Z0-9_ -]*$/", $username)) {
		header("Location: ../register.php?error=invalid_uid&email=".$email);
		exit();
	}
	else if (!$response->success) {
		header("Location: ../register.php?error=captcha_failed&uid=".$username."&email=".$email);
	}
	else {
		$sql = "SELECT account_email FROM accounts WHERE account_email=?";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)) {
			header("Location: ../register.php?error=error");
			exit();
		}
		else {
			mysqli_stmt_bind_param($stmt, "s", $email);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_store_result($stmt);
			$resultCheck = mysqli_stmt_num_rows($stmt);
			if ($resultCheck > 0) {
				header("Location: ../register.php?error=existingaccount&uid=".$username);
				exit();
			}
			else {
				$sql = "INSERT INTO accounts (account_uid, account_email, account_pwd, ouplonkd) VALUES (?, ?, ?, ?)";
				$stmt = mysqli_stmt_init($conn);
				if (!mysqli_stmt_prepare($stmt, $sql)) {
					header("Location: ../register.php?error=sqlerror");
					exit();
				}
				else {
					$hashedpwd = password_hash($password, PASSWORD_DEFAULT);
					mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $hashedpwd, $defaultouplonk);
					mysqli_stmt_execute($stmt);
					header("Location: ../login.php?result=success");
					exit();
				}
			}
		}
	}	
	mysqli_stmt_close($stmt);
	mysqli_close($conn);

}
else {
	header("Location: ../index.php");
	exit();
}