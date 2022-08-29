<?php
	session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="description" content="Rate and review the YouTube and Twitch channels you watch. RateRanker is your destination for social media leaderboards, statistics, and rankings!">
		<meta name="keywords" content="YouTube ratings, YouTube reviews, YouTube stats">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="google-signin-client_id" content="">
		<link rel="icon" href="/img/rateranker-favicon.png" type="image/png" sizes="32x32">
		<link type="text/css" rel="stylesheet" href="/css/style.css" media="screen">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
		<link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">
		<script type="text/javascript" src="/js/jquery-3.1.1.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
		<script data-ad-client="" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<script type="text/javascript" src="/js/g-signin.js"></script>
		<script src="https://apis.google.com/js/platform.js" async defer></script>
		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
		<script async src=""></script>

	</head>
	<script src="/js/header.js"></script>
	<body class="bg-gray-100 overflow-x-hidden">
		<header class="w-full fixed z-40">
			<div class="flex shadow items-center justify-center bg-white h-16">
				<div class="w-full mx-4">
					<div class="items-center justify-between flex">	
					    <div class="items-center flex">
    						<button id="sidemenu-toggle" class="flex-none mr-4">	
    							<img src="/img/togglesidemenu.svg" height="22px" width="22px">
    						</button>
    						<div>
    							<a href="/youtube">
    								<!--<b class="text-lg lg:mx-4 md:mx-2">RateRanker</b>-->
    								<img class="lg:mx-4 md:mx-2" src="/img/rateranker.svg" width="150px" alt="RateRanker">
    							</a>
    						</div>
						</div>
						<form id="searchform" class="flex flex-grow max-w-2xl items-center justify-center">
							<input id="search" class="bg-gray-200 appearance-none rounded-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-gray-200 w-full mx-4 my-2" type="text" placeholder="Search for any channel..."></input>
						</form>
						<div>
						  <?php
							if (isset($_SESSION['userID'])) {
								echo'<div>
										<button id="nem" class="normal-case flex-initial text-gray-700 text-center font-bold hover:text-gray-900 px-4">'.$_SESSION['userUID'].'</button>
										<ul id="options" class="hidden absolute shadow-lg rounded border w-1/5" style="top:4rem; width:200px;">
											<li class="border-b">
												<form action="/server/logout.inc.php" method="post">
													<button id="logout-btn" action="/server/logout.inc.php" method="post" class="w-full flex-initial text-left text-gray-700 bg-white hover:bg-gray-300 pl-4 py-2">Log out</button>
												</form>
											</li>
											<li>
												<button class="w-full flex-initial text-left text-gray-700 bg-white hover:bg-gray-300 pl-4 py-2">Profile (coming soon)</button>
											</li>
										</ul>
									</div>';
							}
							else {
								echo '<a href="/login.php">
									<button class="normal-case flex-initial text-gray-700 text-center font-bold hover:text-gray-900 px-4">Login</button>
									</a>';
							}
						  ?>
						</div>					
					</div>
				</div>
			</div>
		</header>
		<div id="menu-darken" class="mt-16 hidden"></div>
		<div class="pt-16 bg-white"></div>
		<div id="sidemenu" class="fixed bg-white shadow-md w-500 h-full z-30 hidden">
			<div class="font-semibold text-sm p-4">PLATFORMS</div>
			<ul class="border-b mb-4">
				<li class="pb-4">
					<a href="/youtube/index.php">
						<button class="normal-case flex-initial text-gray-700 hover:text-gray-900 px-4">YouTube</button>
					</a>
				</li>
				<li class="pb-4">
					<a href="/twitch/index.php">
						<button class="normal-case flex-initial text-gray-700 hover:text-gray-900 px-4">Twitch</button>
					</a>
				</li>
			</ul>
			<ul>
			    <li class="pb-4">
					<a href="/about.php">
						<button class="normal-case flex-initial text-gray-700 hover:text-gray-900 px-4">About</button>
					</a>
				</li>
				<li class="pb-4">
					<a href="/privacy.php">
						<button class="normal-case flex-initial text-gray-700 hover:text-gray-900 px-4">Privacy</button>
					</a>
				</li>
				<li class="pb-4">
					<a href="/tos.php">
						<button class="normal-case flex-initial text-gray-700 hover:text-gray-900 px-4">Terms of Service</button>
					</a>
				</li>
			</ul>
		</div>
	</body>	
</html>		