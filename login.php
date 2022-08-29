<?php
	require "header.php";
	
	$fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>	
	<title>Log in to RateRanker</title>
	<main>
		<div class="flex justify-center">
			<div class="w-full max-w-xs">
			  <form action="server/login.inc.php" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mt-8">
				<div class="mb-4">
				  <div class="text-xl text-gray-800 mb-4">Log in to RateRanker</div>
				  <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
					Email
				  </label>
					<?php					
					if (strpos($fullUrl, "nouser") == true) {
						echo "<input class='shadow appearance-none border border-red-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline' id='username' type='text' name='email' placeholder='Email'>";
						echo "<p class='text-red-500 text-xs italic'>User does not exist.</p>";
					}
					else {
						if (isset($_GET['email'])) {
							$email = $_GET['email'];
							echo "<input class='shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline' id='username' type='text' name='email' placeholder='Email' value='".$email."'>";
						}
						else {
							echo "<input class='shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline' id='username' type='text' name='email' placeholder='Email'>";
						}
					}
					?>
				</div>
				<div class="mb-6">
				  <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
					Password
				  </label>
					<?php
					if (strpos($fullUrl, "emptyfields") == true) {
						echo "<input class='shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline' id='password' type='password' name='pwd' placeholder='******************'>";
						echo "<p class='text-red-500 text-xs italic'>Please fill out all fields.</p>";
					}
					else if (strpos($fullUrl, "incorrect_password") == true) {
						echo "<input class='shadow appearance-none border border-red-500 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline' id='password' type='password' name='pwd' placeholder='******************'>";
						echo "<p class='text-red-500 text-xs italic'>Incorrect password.</p>";
					}
					else {
						echo "<input class='shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline' id='password' type='password' name='pwd' placeholder='******************'>";
					}
					?>
				</div>
				<div class="flex items-center justify-between">
				  <button type="submit" name="login-submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
					Log In
				  </button>
				  <!--<a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="#">
					Forgot Password?
				  </a>-->
				  <a href="register.php"class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="#">
					Create account
				  </a>
				</div>
			  </form>
			  <!--<p class="text-center text-gray-600 text-sm">
				Don't have an account?
				<a class="text-gray-800" href="register.php">Register</a>
			  </p>-->
			  <center class="text-gray-800 my-2">OR</center>
			  <div class="g-signin2" data-onsuccess="onSignIn" data-width="320" data-height="50" data-longtitle="true" data-theme="dark"></div>  
			  <p class="text-center text-gray-600 text-xs my-2">
				By continuing, you agree to our 
				<a href="/tos.php" class="text-gray-800">Terms of Service</a>
				and 
				<a href="/privacy.php" class="text-gray-800">Privacy Policy</a>
			  </p>
			</div>
		</div>
	</main>
