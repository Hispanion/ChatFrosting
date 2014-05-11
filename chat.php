<?php
/**~. ChatFrosting 0.1 (c) 2014 Garrett R. Morris .~**->>             
 *Licensed Under the MIT License : http://www.opensource.org/licenses/mit-license.php
 * Removing this copyright notice is a violation of the license.
 ***************************************/

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
	die;
}else{
	$loggedInUser = null;//if a user is logged in, the session will overwrite this in the included config file on the next line.
	require_once __DIR__ . '/../models/config.php';
	require_once __DIR__ . '/class.guest.php';
	
	//we need to check if a user is logged in, and set a guest session if not.

	if($loggedInUser == null && !isset($_SESSION["userCakeGuest"])) {
		$guestUser                    = new guestUser();
		$guestUser->last_ajax_request = time();
		$guestUser->minute_throttle   = 1;
		$_SESSION["userCakeGuest"]    = $guestUser;
	}

	
	//now we set up our prerequisites to rate limiting our ajax.
	if(isUserLoggedIn()) {
		$last_ajax_request = $loggedInUser->last_ajax_request;
		$last_ajax_diff    = time() - $last_ajax_request;
		$minute_throttle   = $loggedInUser->minute_throttle;
	}else{
		$last_ajax_request = $guestUser->last_ajax_request;
		$last_ajax_diff    = time() - $last_ajax_request;
		$minute_throttle   = $guestUser->minute_throttle;
	}
	#Ajax Rate Limiting
	$minute = 60;
	$minute_limit = 100;
	
	if (is_null($minute_limit)) {
		$new_minute_throttle = 0;
	}else{
		$new_minute_throttle   = $minute_throttle - $last_ajax_diff;
		$new_minute_throttle   = $new_minute_throttle < 0 ? 0 : $new_minute_throttle;
		$new_minute_throttle  +=	$minute / $minute_limit;
		$minute_hits_remaining = floor(($minute - $new_minute_throttle ) * $minute_limit / $minute);
		$minute_hits_remaining = $minute_hits_remaining >= 0 ? $minute_hits_remaining : 0;
		if(isUserLoggedIn()) {
			$loggedInUser->last_ajax_request = time();
			$loggedInUser->minute_throttle   = $new_minute_throttle;
		}else{
			$guestUser->last_ajax_request   = time();
			$guestUser->minute_throttle     = $new_minute_throttle;
		}
	}

	if ($new_minute_throttle > $minute) {
		$wait   = ceil($new_minute_throttle - $minute);
		$result = json_encode(array('code' => '4041', 'message' => 'Ajax Limit Exceeded: wait '.$wait.' seconds.'));
		die($result);//ajax rate limiting exceeded.
	}else{
		//we carry out the request
		$do = $_GET["do"];
		switch($do) {
			case 'last100':
				$result = json_encode(getLast100());
				die($result);
			break;
			case 'post':
				if(isUserLoggedIn()) {
					postMsg();
					$result = json_encode(getNewMsg($loggedInUser->last_ajax_request - 2));//we fetch new messages since the users last ajax request.
					die($result);
				}else{
					die;
				}
			break;
			case 'getnew':
				$time = $_GET["time"];
				$result = json_encode(getNewMsg($time));//we fetch new messages since the users last ajax request.
				die($result);
			break;
			default: die;
		}
	}
}