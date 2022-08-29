<?php
	require "../header.php";
	
	$channelid = $_GET['c'];
	if (strpos($channelid, '"') !== false || strpos($channelid, "'") !== false || strpos($channelid, '`') !== false || $channelid == "") {
		header("Location: /rate/ouplonk.php?error=201");
	}
	
	date_default_timezone_set('Europe/London');
	include '../server/dbh.php';
	include '../server/reviews.inc.php';
?>	
	<script>
	$(document).ready( function() {
		var ytName = "";
		var ytImg = "";
		var ytId = "<?php echo $channelid; ?>";
		var key = "";
		var url = "https://www.googleapis.com/youtube/v3/channels?part=snippet&id="+ytId+"&key="+key
		
		// load channel
		$.getJSON(url, function(data) {
			if (data.pageInfo.totalResults == 0) {
				console.log("asdfsadgsad");
				window.location.href = "/rate/ouplonk.php?error=202";
			} else {
				ytName = data.items[0].snippet.title;
				ytImg = data.items[0].snippet.thumbnails.medium.url;
				$('.ytName').html(ytName);
				$('.ytImage').html('<img src="'+ytImg+'" id="channelImage" class="rounded-full" width="80" height="80" >');
				$('title').html(ytName+"'s YouTube Ratings Summary | RateRanker");
			}
		});		
	
	    // load reviews
		var start = 0;
		var limit = 30;
		var reachedMax = false;
		
		getData();
		
		$(window).scroll(function () {
			if ($(window).scrollTop() == $(document).height() - $(window).height()) {
				getData();
			}
		});
		
		function getData() {
			if (reachedMax) {
				console.log("No more results.");
				return;
			}
			else {
				var rUsername;
				var rRating;
				var rDate;
				var rReview;
				var rrapi = "https://rateranker.co/api/youtube/reviews.php?channel="+ytId+"&offset="+start+"&limit="+limit;
				
				var template = document.getElementsByTagName("template")[0];
				html = template.content.querySelector("div");
				
				$.getJSON(rrapi, function(data) {
					if (start == 0 && data.success == "false") {
						$('.noreviews').toggleClass('active');
						//console.log("no reviews");
						reachedMax = true;
					} else if (data.success == "false") {
						reachedMax = true;
					} else {
						start += limit;
						// fix broken delete button
						$.each(data.items, function (i, item) {
							a = document.importNode(html, true);
							rUsername = item.postedBy;// formatted
							rUsername = (rUsername.length > 50) ? rUsername.substr(0, 49) + '...' : rUsername;
							rRating = item.rating;
							rDate = item.datePosted;
							rReview = item.reviewContent;
							a.querySelector(".user-name").textContent = rUsername;
							a.querySelector(".review-date").textContent = rDate;
							a.querySelector(".user-review").innerText = rReview;
							
							$(".reviewsection").append(a);
							if (a.querySelector(".user-review").offsetHeight > 100) {
								a.querySelector(".user-review").setAttribute('class', 'user-review hideContent');
								a.querySelector(".show-more").setAttribute('class', 'show-more text-gray-800');
								a.querySelector(".show-more").addEventListener('click', showHide);
							}
							$(".asdf").rateYo({
								rating: rRating,
								readOnly: true,
								starWidth: '20px'
							});
						});
					}
				});
			}
		}
	
		// show rating
		$("#currentRating").rateYo({
			rating: <?php averageRating($conn); ?>,
			readOnly: true,
			starWidth: "20px"
		});
		
		// change or set rating, show buttons
		var rating = "";
		$("#rateYo").rateYo().on("rateyo.change", function (e, data) {
			rating = data.rating;
			$(this).parent().find('input[name=rating]').val(rating);
		});
		$("#rateYo").rateYo().on("rateyo.set", function (e, data) {
			$('#submit-review').show();
			$('#cancel-review').show();
			$('#oucak').show();
		});
		
		// submit new review or edit
		$('#submit-review').click(function () {
			$.ajax({
				url: '/server/reviews.inc.php',
				type: 'POST',
				data: {
					channel_id: ytId,
					rating: rating,
					ytname: ytName,
					ytimage: ytImg,
					message: $('#review').val()
				},
				success: function(msg) {
					$('#success-alert').toggleClass('hidden');
					setTimeout( function() {
						$('#success-alert').addClass('hidden');
					}, 5000);
				}               
			});
			hideButtons();
		});
		
		// cancel review
		$('#cancel-review').click(function () {
			hideButtons();
		});
		
		// show submit button when
		$("#review").keyup(function () {
			$('#submit-review').show();
		});		
		
		// automatic textarea height adjustment
		$('.review-area').each(function () {
		  this.setAttribute('style', 'height:' + (this.scrollHeight+48) + 'px; overflow-y:hidden;');
		}).on('input', function () {
		  this.style.height = 'auto';
		  this.style.height = (this.scrollHeight) + 'px';
		});
		
		// show the show more button
		function showHide() {
			$(this).parent().toggleClass('showContent');
			var replaceText = $(this).parent().hasClass('showContent') ? 'Show less' : 'Show more';
			$(this).text(replaceText);
		}
		
		// options list
		$('.reviewsection').on('click', '.options-btn', function () {
			$(this).siblings().show();
			$('.options-list').removeClass('active');
			$(this).siblings().addClass('active');
		});
		$(document).click(function () {
			if(!$('.options-btn:hover').length > 0) {
			$('.options-list').hide();
			}
		});
		
		// reporting
		$('.reviewsection').on('click', '.report-btn', function () {
			$('#report-box').toggleClass('active');
			$('#channel-darken').toggleClass('active');
			$('body').toggleClass('no-scroll');
		});
		$('#report-submit').click(function () {
			$.ajax({
				url: '/rate/server/report.php',
				type: 'POST',
				data: {
					review_id: ytId,
					reportmessage: $('#report-message').val()
				},
				success: function(msg) {
					alert('Report sent');
				}               
			});
			$('#report-box').toggleClass('active');
			closePopup();
		});
		$('#report-box-close').click(function () {
			$('#report-box').toggleClass('active');
			closePopup();
		});
		
		// tag stuff
		const tagContainer = document.querySelector('.tag-container');
		const input = document.querySelector('.tag-container input');

		let tags = [];

		function createTag(label) {
		  const div = document.createElement('div');
		  div.setAttribute('class', 'tag');
		  const span = document.createElement('span');
		  span.innerHTML = label;
		  const closeIcon = document.createElement('i');
		  //closeIcon.innerHTML = 'delete';
		  //closeIcon.setAttribute('class', 'material-icons');
		  closeIcon.setAttribute('data-item', label);
		  const icon = document.createElement('img');
		  icon.setAttribute('class', 'h-3 w-3');
		  icon.setAttribute('style', 'pointer-events: none;');
		  icon.setAttribute('src', '/img/x.svg');
		  //icon.setAttribute('data-item', label);
		  div.appendChild(span);
		  div.appendChild(closeIcon);
		  closeIcon.appendChild(icon);
		  return div;
		}

		function clearTags() {
		  document.querySelectorAll('.tag').forEach(tag => {
			tag.parentElement.removeChild(tag);
		  });
		}

		function addTags() {
		  clearTags();
		  tags.slice().reverse().forEach(tag => {
			tagContainer.prepend(createTag(tag));
		  });
		}

		input.addEventListener('keyup', (e) => {
			if (e.key === 'Enter') {
			  e.target.value.split(',').forEach(tag => {
				tags.push(tag);  
			  });
			  
			  addTags();
			  input.value = '';
			}
		});
		document.addEventListener('click', (e) => {
		  //console.log(e.target.tagName);
		  if (e.target.tagName === 'I') {
			const tagLabel = e.target.getAttribute('data-item');
			const index = tags.indexOf(tagLabel);
			tags = [...tags.slice(0, index), ...tags.slice(index+1)];
			addTags();    
		  }
		})

		input.focus();
		
		// add tag
		$('#addtag-btn').click(function () {
			$('#addtag-box').toggleClass('active');
			$('#channel-darken').toggleClass('active');
			$('body').toggleClass('no-scroll');
		});
		$('#addtag-submit').click(function () {
			tags.forEach( function(item, index) {
				//console.log(item);
			
				$.ajax({
					url: '/server/addtag.php',
					type: 'POST',
					data: {
						//tag_name: $('#addtag-name').val(),
						tag_name: item,
						channel_id: ytId
					},
					success: function(msg) {
						$('#tag-success-alert').toggleClass('hidden');
						setTimeout( function() {
							$('#tag-success-alert').addClass('hidden');
						}, 5000);
					}               
				});
			});
			$('#addtag-box').toggleClass('active');
			closePopup();
		});
		$('#addtag-box-close').click(function () {
			$('#addtag-box').toggleClass('active');
			closePopup();
		});
		
		function hideButtons() {
			$("#rateYo").rateYo("rating", 0);
			$('#submit-review').hide();
			$('#oucak').hide();
			$('#cancel-review').hide();
		}
		function closePopup() {
			$('#channel-darken').toggleClass('active');
			$('body').toggleClass('no-scroll');
		}
	});	
	</script>
	<title>Loading...</title>
	<main>
		<div id="channel-darken" class="z-30 hidden"></div>
		<div id="report-box" class="popup-box flex mx-auto items-center justify-center rounded shadow-lg z-40 hidden">
			<div class="w-full rounded">
				<h3 class="inline-block text-lg p-4">Report review</h3>
				<label id="report-box-close" class="inline-block float-right p-4">Close</label>
			</div>
			<div class="px-4">
			    <p>To report an inappropriate review, send an email to support@rateranker.co containing the following information:</p>
    			<li>URL of channel that the review is on</li>
    			<li>Username of the reviewer</li>
    			<li>Reason for report</li>
    			<br>
    			<p>For bug reports, suggestions, and other inquiries, send an email to contact@rateranker.co</p>
			</div>
			<!--<div class="w-full flex px-3">
				<textarea id="report-message" class="bg-gray-100 rounded border border-gray-400 leading-normal resize-none w-full h-20 py-2 px-3 placeholder-gray-700 focus:outline-none focus:bg-white" placeholder="Reason for report..."></textarea><br>
			</div>
			<div class="flex justify-end p-3">
				<button id="report-submit" class="bg-blue-500 hover:bg-blue-700 border-blue-500 hover:border-blue-700 text-base border-4 text-white py-1 px-4 rounded">Send</button>
			</div>-->
		</div>
		<div id="addtag-box" class="popup-box flex mx-auto items-center justify-center rounded shadow-lg z-40 hidden">
			<div class="w-full items-center justify-between flex">
				<h3 class="inline-block text-lg p-4">Add category</h3>
				<label id="addtag-box-close" class="inline-block float-right p-4">
					<img src="/img/x.svg" class="h-4 w-4">
				</label>
			</div>
			<div class="w-full flex px-3">
				<div class="container">
					<div class="tag-container">
						<input placeholder="gaming, music, etc."/>
					</div>
				</div>
				<!--<textarea id="addtag-name" class="bg-gray-100 rounded-full border border-gray-400 leading-normal resize-none w-full h-10 py-2 px-3 placeholder-gray-700 focus:outline-none focus:bg-white" placeholder="Name of new category..."></textarea><br>-->
			</div>
			<div class="text-xs text-gray-600 px-3">Press enter after each category</div>
			<div class="flex justify-end p-3">
				<button id="addtag-submit" class="bg-blue-500 hover:bg-blue-700 border-blue-500 hover:border-blue-700 text-base border-4 text-white py-1 px-4 rounded">Send</button>
			</div>
		</div>
		<div class="justify-center flex p-4">
		    <div id="success-alert" class="z-30 fixed flex flex-col sm:flex-row sm:items-center bg-white shadow-lg rounded-md hidden py-5 pl-6 pr-8 sm:pr-6">
				<div class="flex flex-row items-center border-b sm:border-b-0 w-full sm:w-auto pb-4 sm:pb-0">
					<div class="text-green-500">
						<svg class="w-6 sm:w-5 h-6 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
					</div>
					<div class="text-sm font-medium ml-3">Success</div>
				</div>
				<div class="text-sm tracking-wide text-gray-500 mt-4 sm:mt-0 sm:ml-4">Your rating was successful!</div>
				<div class="absolute sm:relative sm:top-auto sm:right-auto ml-auto right-4 top-4 text-gray-400 hover:text-gray-800 cursor-pointer">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
				</div>
			</div>
			<div id="signin-success-alert" class="z-30 fixed flex flex-col sm:flex-row sm:items-center bg-white shadow-lg rounded-md hidden py-5 pl-6 pr-8 sm:pr-6">
				<div class="flex flex-row items-center border-b sm:border-b-0 w-full sm:w-auto pb-4 sm:pb-0">
					<div class="text-green-500">
						<svg class="w-6 sm:w-5 h-6 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
					</div>
					<div class="text-sm font-medium ml-3">Success</div>
				</div>
				<div class="text-sm tracking-wide text-gray-500 mt-4 sm:mt-0 sm:ml-4">Successfully signed in. You can now rate any channel!</div>
				<div class="absolute sm:relative sm:top-auto sm:right-auto ml-auto right-4 top-4 text-gray-400 hover:text-gray-800 cursor-pointer">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
				</div>
			</div>
			<div id="tag-success-alert" class="z-30 fixed flex flex-col sm:flex-row sm:items-center bg-white shadow-lg rounded-md hidden py-5 pl-6 pr-8 sm:pr-6">
				<div class="flex flex-row items-center border-b sm:border-b-0 w-full sm:w-auto pb-4 sm:pb-0">
					<div class="text-green-500">
						<svg class="w-6 sm:w-5 h-6 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
					</div>
					<div class="text-sm font-medium ml-3">Success</div>
				</div>
				<div class="text-sm tracking-wide text-gray-500 mt-4 sm:mt-0 sm:ml-4">Your category has been received and will be reviewed soon.</div>
				<div class="absolute sm:relative sm:top-auto sm:right-auto ml-auto right-4 top-4 text-gray-400 hover:text-gray-800 cursor-pointer">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
				</div>
			</div>
			<div class="max-w-5xl overflow-hidden">
				<div class="rounded-xl bg-white border mb-4">
					<img class="w-auto rounded-t-xl" src="/img/rateranker-banner.png" style="max-height:200px;" alt="Banner">
					<div class="px-4">
						<div class="flex items-center">
						   <?php
						   if ($channelid == 'UCuAXFkgsw1L7xaCfnd5JJOw' || $channelid == 'UC38IQsAvIsxxjztdMZQtwHA' || $channelid == 'UCv5mo0iXze8aKFjvdp51Fjg') {
						       echo "<a href='https://www.youtube.com/watch?v=dQw4w9WgXcQ'>
    						               <div class='ytImage flex-none pl-4'>
                							<svg height='80' width='80'>
                								<circle cx='40' cy='40' r='40' fill='gray'>
                							</svg>
                						  </div>
            						  </a>";
						   }
						   else {
						       echo "<div class='ytImage flex-none pl-4'>
            							<svg height='80' width='80'>
            								<circle cx='40' cy='40' r='40' fill='gray'>
            							</svg>
            						  </div>";
						   }
						  ?>
						  <div class="flex-grow px-6 py-4">
							<p class="text-sm text-gray-600 flex items-center">
								<!--<svg class="fill-current text-gray-500 w-3 h-3 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
								  <path d="M4 8V6a6 6 0 1 1 12 0v2h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-8c0-1.1.9-2 2-2h1zm5 6.73V17h2v-2.27a2 2 0 1 0-2 0zM7 6v2h6V6a3 3 0 0 0-6 0z" />
								</svg>
								<?php echo phpversion(); echo gettype(4.5);?>-->
								<a href="https://www.youtube.com/channel/<?php echo $channelid; ?>" target="_newtab">YouTube</a>
							</p>
							<h1 class="ytName font-bold text-2xl -my-1">RateRanker</h1>
							<div class="flex items-center">
								<div id="currentRating" class="mr-4 p-0"></div>
							</div>
						  </div>
						  <div class="px-6 pt-4 pb-2">
							<span class="flex inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">
								<b class="text-lg text-gray-900 leading-none"><?php echo averageRating($conn); ?></b>
								<div class="text-gray-600">/5</div>
							</span>
						  </div>
						</div>
						<div class='px-4 pb-3'>
							<!-- This is the tags container -->       
							<div class='my-2 flex flex-wrap m-1'>
								<h3 class="font-bold text-xs my-2 mr-4" >Categories</h3> 
								<?php getTags($conn); ?>
								<span id="addtag-btn" class="m-1 bg-gray-200 hover:bg-gray-300 rounded-full px-2 font-bold text-sm leading-loose cursor-pointer">+</span>
							</div>
						</div>
						<center class="text-sm border-t py-2">
							<b class="text-gray-900 leading-none"><?php echo averageRating($conn); ?> out of 5 stars</b>
							<p class="text-gray-600"><?php echo countRatings($conn); ?> reviews</p>
						</center>				
					</div>
				</div>

				<div class='reviewsection bg-white rounded-xl border mt-4'>
					<?php
					if (isset($_SESSION['userID'])) {						
						$check = "SELECT * FROM reviews WHERE channel_id=? AND account_id=?";
						$stmt = mysqli_stmt_init($conn);
						if(!mysqli_stmt_prepare($stmt, $check)) {
							echo "error";
						} else {
							mysqli_stmt_bind_param($stmt, "si", $channelid, $_SESSION['userID']);
							mysqli_stmt_execute($stmt);
							$results = $stmt->get_result();
						}
						if(mysqli_num_rows($results) > 0) {
							// edit existing review
							echo "<div class='flex border-b border-gray-400 p-4 '>
										<div class='w-full'>
											<b class='text-xl'>Change rating and review</b>
											<div id='rateYo' data-rateyo-rating='0' data-rateyo-full-star='true' class='pl-0 py-2'></div>
											<div id='oucak' class='hidden flex items-center border-b border-blue-500 pt-2'>
												<input type='hidden' name='channel_id' value='".$channelid."'>
												<input type='hidden' name='rating'>
												<textarea id='review' class='review-area appearance-none bg-transparent border-none w-full text-gray-700 leading-tight resize-none focus:outline-none p-1' placeholder='Add or edit review...' name='message'></textarea><br>								
											</div>
											<div class='flex justify-between items-center pt-2'>
												<a href='https://twitter.com/share?ref_src=twsrc%5Etfw' id='twitter-share-button' class='twitter-share-button' data-text='I rated a channel using @rateranker at' data-show-count='false'></a><script async src='https://platform.twitter.com/widgets.js' charset='utf-8'></script>										
												<div>
													<button id='cancel-review' class='hidden flex-shrink-0 border-transparent border-4 text-teal-500 hover:text-teal-800 text-base py-1 px-4 rounded' type='button'>CANCEL</button>
													<button id='submit-review' class='hidden flex-shrink-0 bg-blue-500 hover:bg-blue-700 border-blue-500 hover:border-blue-700 text-base border-4 text-white py-1 px-4 rounded'>EDIT</button>
												</div>
											</div>
										</div>
									</div>";
						}
						else {	
							// create a new review
							echo "<div class='flex border-b border-gray-400 p-4 '>
										<div class='w-full'>
											<b class='text-xl'>Choose a rating</b>
											<div id='rateYo' data-rateyo-rating='0' data-rateyo-full-star='true' class='pl-0 py-2'></div>
											<div id='oucak' class='hidden flex items-center border-b border-blue-500 pt-2'>
												<input type='hidden' name='channel_id' value='".$channelid."'>
												<input type='hidden' name='rating'>							
												<textarea id='review' class='review-area appearance-none bg-transparent border-none w-full text-gray-700 leading-tight resize-none focus:outline-none p-1' placeholder='Add a review...' name='message'></textarea><br>								
											</div>
											<div class='flex justify-between items-center pt-2'>
												<a href='https://twitter.com/share?ref_src=twsrc%5Etfw' id='twitter-share-button' class='twitter-share-button' data-text='I rated a channel using @rateranker at' data-show-count='false'></a><script async src='https://platform.twitter.com/widgets.js' charset='utf-8'></script>										
												<div>
													<button id='cancel-review' class='hidden flex-shrink-0 border-transparent border-4 text-teal-500 hover:text-teal-800 text-base py-1 px-4 rounded' type='button'>CANCEL</button>
													<button id='submit-review' class='hidden flex-shrink-0 bg-blue-500 hover:bg-blue-700 border-blue-500 hover:border-blue-700 text-base border-4 text-white py-1 px-4 rounded'>REVIEW</button>
												</div>
											</div>
										</div>
									</div>";
						}		
					} else {	
						echo "<div class='border-b border-gray-400 p-4'>
								<center class='mb-2'>Sign in to rate</center>
								<div class='justify-center flex'>
									<button class='flex-shrink-0 bg-blue-500 hover:bg-blue-700 border-blue-500 hover:border-blue-700 text-base border-4 text-white rounded py-1 px-4 mr-4'>
										<a href='../register.php'>Log in or register</a>
									</button>
									<div class='g-signin2' data-onsuccess='onSignIn' data-width='160' data-height='50' data-theme='light'></div>  						
								</div>
							</div>";
					}
					?>
					<div class='noreviews p-4 rounded-b hidden'>No reviews yet. Be the first to review!</div>
					<template>
					<div class='flex justify-center rounded'>
						<div class='max-w-5xl w-full flex justify-start relative m-4'>				
							<img class='h-10 w-10 rounded-full mr-2' src='/img/user-icon.svg'>
							<div>
								<div class='flex'>
									<b class='user-name'>username</b>
									<div class='review-date text-gray-600 mx-2'>1 minute ago</div>
								</div>
								<div class='asdf p-0'></div>
								<p class='user-review'>message</p>
								<button class='show-more text-gray-800 hidden'>Show more</button>
								<div class='absolute top-0 right-0'>
									<button class='options-btn'>
										<img src='/img/options.svg' height='20px' width='20px'>
									</button>
									<ul class='options-list hidden absolute rounded shadow-lg top-0 right-0'>
										<li>
											<button class='report-btn flex w-full border-b text-gray-700 text-left bg-white hover:bg-gray-300 px-4 py-4' style='width:150px'>
												<img class='h-6 w-6 rounded-full mr-2' src='/img/flag.svg'>
												Report
											</button>
										</li>
									</ul>
								</div>
							</div>					
						</div>
					</div>
					</template>
				</div>
			</div>
			<div class="max-w-xs w-full hidden xl:block ml-4">

				<?php
				$recommended = mysqli_query($conn, "SELECT * FROM `channels` WHERE NOT ouplonkd=1 AND review_count BETWEEN 10 AND 2000 ORDER BY RAND() LIMIT 30");
				while ($rrow = mysqli_fetch_assoc($recommended)) {
					echo "<div class='w-full mt-2'>
							<a href='channel.php?c=".$rrow['channel_id']."'>
								<div class='bg-white flex h-full items-center rounded border p-4'>
									<img class='flex-none rounded-full w-16 h-16 mr-4' src=".$rrow['channel_avatar'].">
									<div class='mb-4'>
										<div class='whitespace-no-wrap text-gray-900 font-bold text-xl mb-2'>".$rrow['channel_name']."</div>
										<p class='text-gray-700 text-base'>".$rrow['average_rating']." stars, ".$rrow['review_count']." reviews</p>
									</div>
								</div>
							</a>
						</div>";
				}
				?>
			</div>
		</div>
	</main>