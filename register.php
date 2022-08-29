<?php
	require "header.php";
	
	$fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>	
	<title>Register</title>
	<main>
		<div class="flex justify-center">
			<div class="w-full max-w-sm">
			  <form action="server/register.inc.php" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mt-8">
				<div class="mb-4">
				  <div class="text-xl text-gray-800 mb-4">Create an account</div>
				  <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
					Username
				  </label>				  
					<?php					
					if (strpos($fullUrl, "invalid_uid_and_email") == true) {
						echo "<input class='shadow appearance-none border border-red-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline' id='username' type='text' name='uid' placeholder='Username'>";
						echo "<p class='text-red-500 text-xs italic'>Please enter a valid username.</p>";
					}
					else if (strpos($fullUrl, "invalid_uid") == true) {
						echo "<input class='shadow appearance-none border border-red-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline' id='username' type='text' name='uid' placeholder='Username'>";
						echo "<p class='text-red-500 text-xs italic'>Please enter a valid username.</p>";
					}
					else {
						if (isset($_GET['uid'])) {
							$uid = $_GET['uid'];
							echo "<input class='shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline' id='username' type='text' name='uid' placeholder='Username' value='".$uid."'>";
						}
						else {
							echo "<input class='shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline' id='username' type='text' name='uid' placeholder='Username'>";
						}
					}
					?>
				</div>
				<div class="mb-4">
				  <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
					Email
				  </label>
					<?php					
					if (strpos($fullUrl, "invalid_uid_and_email") == true) {
						echo "<input class='shadow appearance-none border border-red-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline' id='email' type='text' name='email' placeholder='Email'>";
						echo "<p class='text-red-500 text-xs italic'>Please enter a valid email address.</p>";
					}
					else if (strpos($fullUrl, "invalid_email") == true) {
						echo "<input class='shadow appearance-none border border-red-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline' id='email' type='text' name='email' placeholder='Email'>";
						echo "<p class='text-red-500 text-xs italic'>Please enter a valid email address.</p>";
					}
					else if (strpos($fullUrl, "existingaccount") == true) {
						echo "<input class='shadow appearance-none border border-red-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline' id='email' type='text' name='email' placeholder='Email'>";
						echo "<p class='text-red-500 text-xs italic'>Please enter a different email address.</p>";
					}
					else {
						if (isset($_GET['email'])) {
							$email = $_GET['email'];
							echo "<input class='shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline' id='email' type='text' name='email' placeholder='Email' value='".$email."'>";
						}
						else {
							echo "<input class='shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline' id='email' type='text' name='email' placeholder='Email'>";
						}
					}
					?>	
				</div>
				<div class="mb-4">
				  <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
					Password
				  </label>				  
					<?php
					if (strpos($fullUrl, "emptyfields") == true) {
						echo "<input class='shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline' id='password' type='password' name='pwd' placeholder='******************'>";
						echo "<p class='text-red-500 text-xs italic'>Please fill out all fields.</p>";
					}
					else if (strpos($fullUrl, "error=error") == true) {
						echo "<input class='shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline' id='password' type='password' name='pwd' placeholder='******************'>";
						echo "<p class='text-red-500 text-xs italic'>An error occurred. Please try again later.</p>";
					}
					else if (strpos($fullUrl, "sqlerror") == true) {
						echo "<input class='shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline' id='password' type='password' name='pwd' placeholder='******************'>";
						echo "<p class='text-red-500 text-xs italic'>An error occurred. Please try again later.</p>";
					}
					else {
						echo "<input class='shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline' id='password' type='password' name='pwd' placeholder='******************'>";
					}
					?>			  
				</div>
				<div class="g-recaptcha mb-2" data-size="normal" data-sitekey=""></div>
				<?php
				if (strpos($fullUrl, "captcha_failed") == true) {
					echo "<p class='text-red-500 text-xs italic'>Captcha failed.</p>";
				}
				?>
				<div class="flex items-center justify-between mt-6">
				  <button type="submit" name="register-submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
					Register
				  </button>
				  <a href="login.php"class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="#">
					Login instead
				  </a>
				</div>
			  </form>
			  <center class="text-gray-800">
				  <div class="my-2">OR</div>
				  <div class="g-signin2" data-onsuccess="onSignIn" data-width="320" data-height="50" data-longtitle="true" data-theme="dark"></div>
			  </center>
			  <p class="text-center text-gray-600 text-xs mt-2 mb-8">
				By continuing, you agree to our 
				<a href="/tos.php" class="text-gray-800">Terms of Service</a>
				and 
				<a href="/privacy.php" class="text-gray-800">Privacy Policy</a>
			  </p>
			</div>
		</div>
	</main>