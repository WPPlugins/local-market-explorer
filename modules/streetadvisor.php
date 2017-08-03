<?php

class LmeModuleStreetAdvisor {
	
  static function getModuleHtml($opt_neighborhood, $opt_city, $opt_state, $opt_zip) {

    $level=0;    
		if (!empty($opt_zip)) {
			$locationParams = "{$opt_zip}";
      $level = 4;
		} 
    else {
			$encodedCity = urlencode($opt_city);
			$locationParams = "{$encodedCity},{$opt_state}";
      if ($level==0) {
		    $level=4;
      }
    	if (strlen($opt_neighborhood) > 0) {
				$encodedNeighborhood = urlencode($opt_neighborhood);
				$locationParams = "{$encodedNeighborhood},{$locationParams}";
        $level=5;
			}
		}
    
    $geocodeUrl = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=";
		$geocodeUrl .= $locationParams;                                                          
		$geocodeContent = json_decode(LmeApiRequester::getContent($geocodeUrl));
		$latLng = $geocodeContent->results[0]->geometry->location;
    $long_name = $geocodeContent->results[0]->address_components[0]->long_name;
    $lat = $latLng->lat;
    $lng = $latLng->lng;
    $originalLevel =  $level;
    
    //Location data
    $iterate = True;
    $tries = 0;
    $notFound = False;
    while ($iterate) {
      $url = 'https://web-api-legacy.streetadvisor.com/locations/overview/'.$lat.'/'.$lng.'/'.$level;
      $ch = curl_init($url);
      $options = array(
        CURLOPT_RETURNTRANSFER => true,         // return web page
        CURLOPT_HEADER         => false,        // don't return headers
        CURLOPT_FOLLOWLOCATION => false,         // follow redirects
        CURLOPT_AUTOREFERER    => true,         // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 60,          // timeout on connect
        CURLOPT_TIMEOUT        => 60,          // timeout on response
        CURLOPT_HTTPHEADER     => array(
          "Authorization: b08f6473-8dee-41c3-9a3e-b32f335e9d2d",
          "Content-Type: application/json"
        )
      );
      curl_setopt_array($ch,$options);
      $data = curl_exec($ch); 
      curl_close($ch);
      $locationData = json_decode($data, true);
      if (!isset($locationData['Errors'])) {
        $iterate = False;
      }
      else {
        $iterate = True;
        $level = $level - 1;
      }
      $tries = $tries + 1;
      if ($tries == 9) {
        $iterate = False;
        $notFound = True;
      }
    }
    if ($notFound) {
      return '';
    }
    
    //Reviews data
    $notReviews = False;
    $url = 'https://web-api-legacy.streetadvisor.com/reviews/'.$lat.'/'.$lng.'/'.$level.'?take=5';
    $ch = curl_init($url);
    $options = array(
        CURLOPT_RETURNTRANSFER => true,         // return web page
        CURLOPT_HEADER         => false,        // don't return headers
        CURLOPT_FOLLOWLOCATION => false,         // follow redirects
        CURLOPT_AUTOREFERER    => true,         // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 60,          // timeout on connect
        CURLOPT_TIMEOUT        => 60,          // timeout on response
        CURLOPT_HTTPHEADER     => array(
          "Authorization: b08f6473-8dee-41c3-9a3e-b32f335e9d2d",
          "Content-Type: application/json"
        )
    );
    curl_setopt_array($ch,$options);
    $data = curl_exec($ch); 
    curl_close($ch);
    $reviewsData = json_decode($data, true);
    if (isset($reviewsData['Errors'])) {
      $notReviews = True;
    }
    if (isset($reviewsData['TotalItems'])) {
      if ($reviewsData['TotalItems'] == 0) {
        $notReviews = True;
      }
    }
    else {
      $reviewsData['TotalItems'] = 0;
      $notReviews = True;
    }
    
    $reviewsHTML = '';
    if ($notReviews) {
      $reviewsHTML .= '<a target="_blank" href="'.$locationData['Location']['Url'].'/write-a-review">No reviews yet. Be the first to write a review.</a>';
    }
    else {
      $minorValue = $reviewsData['TotalItems']<5 ? $reviewsData['TotalItems'] : 5; 
      for ($i=0; $i<$minorValue; $i++ ) {          
        $avatarURL = str_replace('image/upload/', 'image/upload/h_48,w_48,c_fill,g_face/', $reviewsData['Items'][$i]['User']['AvatarUrl']); 
        $reviewsHTML .= '<div>
                            <div class="reviewTitle">
                              <strong>"'.substr($reviewsData['Items'][$i]['Title'],0,54).'..."</strong>
                                <div>'.substr($reviewsData['Items'][$i]['Content'],0,243).'... <br/><a target="_blank" href="'.$locationData['Location']['Url'].'">Read more...</a>
                                </div>
                            </div>
                            <div class="reviewAvatar">
                              <strong>by '.$reviewsData['Items'][$i]['User']['DisplayName'].'</strong>
                              <img width="34" height="34" src="'.$avatarURL.'" align="right">
                            </div>
                          </div>'; 
        $reviewsHTML .= '<br clear="all"/><br clear="all"/>';                                              
      }
    }
    $restReviews = $reviewsData['TotalItems'] - 5;
    if ($restReviews < 0) {
      $restReviews = 0;
    }
    
    $bnHTML = '';
    if ($originalLevel==4) {
      $bnURL = str_replace('streetadvisor.com/','streetadvisor.com/search/neighborhoods-in-',$locationData['Location']['Url']);
      $bnHTML = '<a target="_blank" href="'.$bnURL.'">Learn More about Best Neighborhoods in '.$locationData['Location']['Name'].'</a>';
    }
    
    $lme_plugin_url = LME_PLUGIN_URL;

    return <<<HTML
 			<h2 class="lme-module-heading">What the Locals Think</h2>
			<div class="lme-module lme-streetadvisor">
        <div class="circle">
          <span class="value">{$locationData['Location']['Score']}</span>
        </div>
        <div class="nameheader">
          <a href="{$locationData['Location']['Url']}" target="_blank">{$locationData['Location']['Name']}</a>
          <i class="rankingdescription">Score: {$locationData['Location']['Score']} out of 10<br/>{$locationData['Location']['RankingDescription']}</i>
        </div>
        <br clear="all"/>
        <div class="sa-body">
          <span class="textred">Reviews</span>
          <div>
            {$reviewsHTML}
            <div class="sa_see_reviews">
                <a target="_blank" href="{$locationData['Location']['Url']}">See all {$locationData['Location']['Name']} reviews</a>
            </div>
            <div class="sa_button_reviews">
               <input type="button" id="leave_review" value="Leave a Review" class="button2">  
               <input type="hidden" id="leave_review_url" value="{$locationData['Location']['Url']}/write-a-review">
            </div>
          </div>
          <br clear="all"/><br/>
          <div align="center">
            {$bnHTML}
          </div>                       
          <br clear="all"/>
          <div>
            <span>Have a burning question? Why not ask the locals?</span>
            <input type="text" name="sa_title" id="sa_title" class="ask_question" placeholder="Your question here...">
            <input type="button" value="Ask It" id="ask_button" class="button">
            <input type="hidden" id="ask_url" value="{$locationData['Location']['Url']}/questions/ask">
          </div>
          <br clear="all"/>
          <div>
            <a href="http://www.streetadvisor.com/" target="_blank"><img align="right" src="{$lme_plugin_url}images/logos/streetadvisor.png" width="109" height="22" border="0" alt="StreetAdvisor" /></a>
          </div>
        </div>
      </div>
      
HTML;
	}

}
                             
?>