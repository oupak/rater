<?php
if (isset($_POST['gname'], $_POST['gemail'], $_POST['idtoken'])) {
	
	require 'dbh.php';
	require_once '../google-api/vendor/autoload.php';
	
	$googleusername = $_POST['gname'];
	$googleemail = $_POST['gemail'];
	$id_token = $_POST['idtoken'];
    $defaultouplonk = 0;

	$jwt = new \Firebase\JWT\JWT;
	$jwt::$leeway = 120;

	$client = new Google_Client(['client_id' => '710328263981-v6kamo2d6bc6f4idt7o3eicb3i67q939.apps.googleusercontent.com']);  // Specify the CLIENT_ID of the app that accesses the backend
	$payload = $client->verifyIdToken($id_token);
	if ($payload) {
	  $userid = $payload['sub'];
	} else {
	  console.log('Google sign-in failed: incorrect ID token');
	}
	
	// check if userid is already in database
	$sql = "SELECT account_email FROM accounts WHERE account_email=?";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		exit();
	}
	else {
		mysqli_stmt_bind_param($stmt, "s", $googleemail);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);
		$resultCheck = mysqli_stmt_num_rows($stmt);
		// if userid is in database, start a session with that user
		if ($resultCheck > 0) {
			$sql = "SELECT * FROM accounts WHERE account_pwd=?;";
			$stmt = mysqli_stmt_init($conn);
			if (!mysqli_stmt_prepare($stmt, $sql)) {
				//header("Location: ../login.php?error=sqlerror");
				exit();
			}
			else {
				mysqli_stmt_bind_param($stmt, "s", $userid);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				if ($row = mysqli_fetch_assoc($result)) {
					session_start();
					$_SESSION['userID'] = $row['account_id'];
					$_SESSION['userUID'] = $row['account_uid'];
					//header("Location: ../index.php?result=success"); // needs to redirect user to home
					exit();
				}
			}
		}
		// if userid does not exist in database, register account with their google information
		else {
			$sql = "INSERT INTO accounts (account_uid, account_email, account_pwd, ouplonkd) VALUES (?, ?, ?, ?)";
			$stmt = mysqli_stmt_init($conn);
			if (!mysqli_stmt_prepare($stmt, $sql)) {
				//header("Location: ../register.php?error=sqlerror");
				exit();
			}
			else {
				mysqli_stmt_bind_param($stmt, "sssi", $googleusername, $googleemail, $userid, $defaultouplonk);
				mysqli_stmt_execute($stmt);
				// sign user in
				$sql = "SELECT * FROM accounts WHERE account_pwd=?;";
				$stmt = mysqli_stmt_init($conn);
				if (!mysqli_stmt_prepare($stmt, $sql)) {
					//header("Location: ../login.php?error=sqlerror");
					exit();
				}
				else {
					mysqli_stmt_bind_param($stmt, "s", $userid);
					mysqli_stmt_execute($stmt);
					$result = mysqli_stmt_get_result($stmt);
					if ($row = mysqli_fetch_assoc($result)) {
						session_start();
						$_SESSION['userID'] = $row['account_id'];
						$_SESSION['userUID'] = $row['account_uid'];
						//header("Location: ../index.php?result=success"); // needs to redirect user to home
						exit();
					}
				}
			}
		}
	}
	
	mysqli_stmt_close($stmt);		
	mysqli_close($conn);
}
?>