<?php
	require "header.php";
	include 'server/dbh.php';
	
	$sql = "SELECT * FROM trendingsection WHERE trendingid='1';";
	$result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_assoc($result);
	$sql2 = "SELECT * FROM trendingsection WHERE trendingid='2';";
	$result2 = mysqli_query($conn, $sql2);
	$row2 = mysqli_fetch_assoc($result2);
	$sql3 = "SELECT * FROM trendingsection WHERE trendingid='3';";
	$result3 = mysqli_query($conn, $sql3);
	$row3 = mysqli_fetch_assoc($result3);
	$sql4 = "SELECT * FROM trendingsection WHERE trendingid='4';";
	$result4 = mysqli_query($conn, $sql4);
	$row4 = mysqli_fetch_assoc($result4);
	$sql5 = "SELECT * FROM trendingsection WHERE trendingid='5';";
	$result5 = mysqli_query($conn, $sql5);
	$row5 = mysqli_fetch_assoc($result5);
	$sql6 = "SELECT * FROM trendingsection WHERE trendingid='6';";
	$result6 = mysqli_query($conn, $sql6);
	$row6 = mysqli_fetch_assoc($result6);
		
	$mostpopular = mysqli_query($conn, "SELECT * FROM channels WHERE NOT ouplonkd=1 ORDER BY review_count DESC LIMIT 6");	
	$highestrated = mysqli_query($conn, "SELECT * FROM channels WHERE NOT ouplonkd=1 ORDER BY score DESC LIMIT 6");	
	$lowestrated = mysqli_query($conn, "SELECT * FROM channels WHERE NOT ouplonkd=1 ORDER BY score ASC LIMIT 6");
?>
	<title>RateRanker — YouTube Ratings and Reviews</title>
	<main>
		
		<div class="justify-center flex p-4">
			<div class="max-w-5xl w-full justify-start overflow-hidden">
				<!-- trending/featured -->
				<div class="flex mb-4">
						<div class="w-full w-1/3 pr-2">
						<a href="youtube/channel.php?c=<?php echo $row['channelid']; ?>">
							<div class="bg-white flex h-full items-center rounded shadow-lg p-4">
								<img class="flex-none rounded-full w-24 h-24 mr-4" src="<?php echo $row['profilepic']; ?>">
								<div class="mb-8">
									<div class="whitespace-no-wrap text-gray-900 font-bold text-xl mb-2"><?php echo $row['channelname']; ?></div>
									<p class="text-gray-700 text-base">Featured</p>
								</div>
								<div class="flex items-center">
									<div class="currentRating"></div>
								</div>
							</div>
						</a>
						</div>
						<div class="w-full w-1/3 hidden sm:block md:block lg:block xl:block sm:pl-2 md:pl-2 lg:px-2">
						<a href="youtube/channel.php?c=<?php echo $row2['channelid']; ?>">
							<div class="bg-white flex h-full items-center rounded shadow-lg p-4">
								<img class="flex-none rounded-full w-24 h-24 mr-4" src="<?php echo $row2['profilepic']; ?>">
								<div class="mb-8">
									<div class="whitespace-no-wrap text-gray-900 font-bold text-xl mb-2"><?php echo $row2['channelname']; ?></div>
									<p class="text-gray-700 text-base">Featured</p>
								</div>
							</div>
						</a>
						</div>
						<div class="w-full w-1/3 hidden lg:block xl:block pl-2">
						<a href="youtube/channel.php?c=<?php echo $row3['channelid']; ?>">
							<div class="bg-white flex h-full items-center rounded shadow-lg p-4">
								<img class="flex-none rounded-full w-24 h-24 mr-4" src="<?php echo $row3['profilepic']; ?>">
								<div class="mb-8">
									<div class="whitespace-no-wrap text-gray-900 font-bold text-xl mb-2"><?php echo $row3['channelname']; ?></div>
									<p class="text-gray-700 text-base">Featured</p>
								</div>
							</div>
							</a>
						</div>		
				</div>
				<div class="flex mb-8">
						<div class="w-full w-1/3 pr-2">
						<a href="youtube/channel.php?c=<?php echo $row4['channelid']; ?>">
							<div class="bg-white flex h-full items-center rounded shadow-lg p-4">
								<img class="flex-none rounded-full w-24 h-24 mr-4" src="<?php echo $row4['profilepic']; ?>">
								<div class="mb-8">
									<div class="whitespace-no-wrap text-gray-900 font-bold text-xl mb-2"><?php echo $row4['channelname']; ?></div>
									<p class="text-gray-700 text-base">Featured</p>
								</div>
								<div class="flex items-center">
									<div class="currentRating"></div>
								</div>
							</div>
						</a>
						</div>
						<div class="w-full w-1/3 hidden sm:block md:block lg:block xl:block sm:pl-2 md:pl-2 lg:px-2">
						<a href="youtube/channel.php?c=<?php echo $row5['channelid']; ?>">
							<div class="bg-white flex h-full items-center rounded shadow-lg p-4">
								<img class="flex-none rounded-full w-24 h-24 mr-4" src="<?php echo $row5['profilepic']; ?>">
								<div class="mb-8">
									<div class="whitespace-no-wrap text-gray-900 font-bold text-xl mb-2"><?php echo $row5['channelname']; ?></div>
									<p class="text-gray-700 text-base">Featured</p>
								</div>
							</div>
						</a>
						</div>
						<div class="w-full w-1/3 hidden lg:block xl:block pl-2">
						<a href="youtube/channel.php?c=<?php echo $row6['channelid']; ?>">
							<div class="bg-white flex h-full items-center rounded shadow-lg p-4">
								<img class="flex-none rounded-full w-24 h-24 mr-4" src="<?php echo $row6['profilepic']; ?>">
								<div class="mb-8">
									<div class="whitespace-no-wrap text-gray-900 font-bold text-xl mb-2"><?php echo $row6['channelname']; ?></div>
									<p class="text-gray-700 text-base">Featured</p>
								</div>
							</div>
							</a>
						</div>		
				</div>
				<!-- popular -->	
				<h1 class="border-t-4 border-gray-300 text-2xl font-semibold py-4">Popular</h1>
				<div class="flex pb-4">				
					<?php
						//while($rows = $query->fetch_assoc()) {
						foreach (range(0, 5) as $i) {
							$rows = $mostpopular->fetch_assoc();
							echo "<a href='youtube/channel.php?c=".$rows['channel_id']."'>
									<div class='flex-none w-40 rounded overflow-hidden shadow-lg bg-white m-1'>
										<img class='w-full' src='".$rows['channel_avatar']."' width='80' height='80'>
									    <div class='px-2 py-2'>
										<div class='whitespace-no-wrap font-bold text-md'>".$rows['channel_name']."</div>
											<p class='text-gray-700 text-base'>".$rows['review_count']." reviews</p>
									    </div>
									    <div class='px-2 pt-4 pb-2'>
											<span>".$rows['average_rating']." stars</span>
									    </div>
									</div>
								</a>";
						}
					?>			
				</div>
				<!-- random -->
				<h1 class="border-t-4 border-gray-300 text-2xl font-semibold py-4">Who to rate</h1>
				<div class="flex pb-4">				
					<?php
						$random = mysqli_query($conn, "SELECT * FROM `channels` WHERE NOT ouplonkd=1 AND review_count BETWEEN 15 AND 40 ORDER BY RAND() LIMIT 6");
						foreach (range(0, 5) as $i) {
							$rows = $random->fetch_assoc();
							echo "<a href='youtube/channel.php?c=".$rows['channel_id']."'>
									<div class='flex-none w-40 rounded overflow-hidden shadow-lg bg-white m-1'>
										<img class='w-full' src='".$rows['channel_avatar']."' width='80' height='80'>
									    <div class='px-2 py-2'>
										<div class='whitespace-no-wrap font-bold text-md'>".$rows['channel_name']."</div>
											<p class='text-gray-700 text-base'>".$rows['review_count']." reviews</p>
									    </div>
									    <div class='px-2 pt-4 pb-2'>
											<span>".$rows['average_rating']." stars</span>
									    </div>
									</div>
								</a>";
						}
					?>			
				</div>
				<div class="flex pb-4">				
					<?php
						$random = mysqli_query($conn, "SELECT * FROM `channels` WHERE NOT ouplonkd=1 AND review_count BETWEEN 2 AND 14 ORDER BY RAND() LIMIT 6");
						foreach (range(0, 5) as $i) {
							$rows = $random->fetch_assoc();
							echo "<a href='youtube/channel.php?c=".$rows['channel_id']."'>
									<div class='flex-none w-40 rounded overflow-hidden shadow-lg bg-white m-1'>
										<img class='w-full' src='".$rows['channel_avatar']."' width='80' height='80'>
									    <div class='px-2 py-2'>
										<div class='whitespace-no-wrap font-bold text-md'>".$rows['channel_name']."</div>
											<p class='text-gray-700 text-base'>".$rows['review_count']." reviews</p>
									    </div>
									    <div class='px-2 pt-4 pb-2'>
											<span>".$rows['average_rating']." stars</span>
									    </div>
									</div>
								</a>";
						}
					?>			
				</div>
				<!-- highest rated -->
				<h1 class="border-t-4 border-gray-300 text-2xl font-semibold py-4">Highest rated</h1>
				<div class="flex pb-4">
					<?php
						foreach (range(0, 5) as $i) {
							$rows = $highestrated->fetch_assoc();
							echo "<a href='youtube/channel.php?c=".$rows['channel_id']."'>
									<div class='flex-none w-40 rounded overflow-hidden shadow-lg bg-white m-1'>
										<img class='w-full' src='".$rows['channel_avatar']."' width='80' height='80'>
									    <div class='px-2 py-2'>
										<div class='whitespace-no-wrap font-bold text-md'>".$rows['channel_name']."</div>
											<p class='text-gray-700 text-base'>".$rows['review_count']." reviews</p>
									    </div>
									    <div class='px-2 pt-4 pb-2'>
											<span>".$rows['average_rating']." stars</span>
									    </div>
									</div>
								</a>";
						}
					?>
				</div>
				<!-- lowest rated -->
				<h1 class="border-t-4 border-gray-300 text-2xl font-semibold py-4">Lowest rated</h1>
				<div class="flex pb-4">
					<?php
						foreach (range(0, 5) as $i) {
							$rows = $lowestrated->fetch_assoc();
							echo "<a href='youtube/channel.php?c=".$rows['channel_id']."'>
									<div class='flex-none w-40 rounded overflow-hidden shadow-lg bg-white m-1'>
										<img class='w-full' src='".$rows['channel_avatar']."' width='80' height='80'>
									    <div class='px-2 py-2'>
										<div class='whitespace-no-wrap font-bold text-md'>".$rows['channel_name']."</div>
											<p class='text-gray-700 text-base'>".$rows['review_count']." reviews</p>
									    </div>
									    <div class='px-2 pt-4 pb-2'>
											<span>".$rows['average_rating']." stars</span>
									    </div>
									</div>
								</a>";
						}
					?>
				</div>	

			</div>	
			<div class="max-w-xs w-full bg-white rounded shadow hidden xl:block px-4 pt-4 ml-4 mb-4">

				<div class="text-xl border-b pb-4">Latest reviews</div>
				<?php
				$recent = mysqli_query($conn, "SELECT * FROM reviews ORDER BY date DESC LIMIT 20");
				while ($rrow = mysqli_fetch_assoc($recent)) {
					$rcid = $rrow['channel_id'];
					$raid = $rrow['account_id'];
					$recentchannel = mysqli_query($conn, "SELECT * FROM channels WHERE channel_id='$rcid'");
					$crow = mysqli_fetch_assoc($recentchannel);
					$recentaccount = mysqli_query($conn, "SELECT * FROM accounts WHERE account_id='$raid'");
					$arow = $recentaccount->fetch_assoc();
					echo "<div class='w-full border-b py-2'>
							<a href='youtube/channel.php?c=".$crow['channel_id']."'>
								<div>".$arow['account_uid']." reviewed ".$crow['channel_name']."</div>
								<p class='text-gray-700 text-base'>".$rrow['message']."</p>
							</a>
						</div>";
				}
				?>
			</div>		
		</div>
	</main>
