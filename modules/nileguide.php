<?php

class LmeModuleNileGuide {
	static function getApiUrls($opt_neighborhood, $opt_city, $opt_state, $opt_zip) {
		$apiKey = "ec9b61be-8365-457c-a070-a7ed200049b5"; // embedded per NG's request
		$apiUrl = "http://www.nileguide.com/service/cat?key=" . $apiKey;
		$placeApi = "/service/place?count=10&searchTerms=category:seedo&searchTerms=";
		$tripApi = "/service/trip?count=10&searchTerms=";
		
		if (!empty($opt_zip)) {
			$placeLocationParams = "postalCode:{$opt_zip}";
			$tripLocationParams = "postalCode:{$opt_zip}";
		} else {
			// this is pretty sucky. it's just just temporary though until NileGuide implements a system to look up
			// places / trips by city / state. this is in their todo queue.
			
			$geocodeUrl = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=";
			if (!empty($opt_neighborhood))
				$geocodeUrl .= urlencode($opt_neighborhood . ",");
			$geocodeUrl .= urlencode($opt_city . "," . $opt_state);
			$geocodeContent = json_decode(LmeApiRequester::getContent($geocodeUrl));
			$latLng = $geocodeContent->results[0]->geometry->location;
			
			// end temp code
			
			$placeLocationParams = "destinationLatLong:{$latLng->lat},{$latLng->lng}&searchTerms=latLong:{$latLng->lat},{$latLng->lng}&searchTerms=maxDistance:15";
			$tripLocationParams = "destinationLatLong:{$latLng->lat},{$latLng->lng}";
		}
		
		$url = "{$apiUrl}&service=" . urlencode($placeApi . $placeLocationParams) . "&service=" . urlencode($tripApi . $tripLocationParams);
		return array( "nileguide" => $url );
	}
	static function getModuleHtml($modules) {
		global $wp_scripts;
		
		$apiResponse = @json_decode($modules["nileguide"])->feed->entry;
		
		if (empty($apiResponse) || count($apiResponse) == 0)
			return;
		
		$jsonResults = array();
		$logoSrc = LME_PLUGIN_URL . "images/logos/nileguide.png";
		$resultsId = rand();
	
		foreach ($apiResponse as $entry) {
			if ($entry->category->term != "/place/seedo")
				continue;
			
			if (is_array($entry->link)) {
				foreach ($entry->link as $possibleLink) {
					if ($possibleLink->type == "text/html")
						$link = $possibleLink->href;
					else if ($possibleLink->type == "application/pdf")
						$pdfLink = $possibleLink->href;
				}
			} else {
				$link = $entry->link->href;
			}
			
			$summary = (array)$entry->summary;
			$summary = $summary["$"]; 
			
			$image = (array)$entry->image;
			$image = $image["$"];
			
			$latLng = explode(" ", $entry->point);
			
			$jsonResults[] = (object)array(
				"name"			=> $entry->title,
				"url"			=> $link,
				"summary"		=> $summary,
				"image"			=> $image,
				"latitude"		=> $latLng[0],
				"longitude"		=> $latLng[1]
			);
			
			if (!empty($image))
				$reviewImgHtml = "<a href=\"{$link}\"><img src=\"{$image}\" class=\"lme-photo\" /></a>";
			else
				$reviewImgHtml = "";
			
			$description = (array)$entry->content;
			$description = strip_tags($description["$"]);
			if (strlen($description) > 400)
				$description = substr($description, 0, 400) . "...";
			$description .= " (<a href=\"{$link}\">read more</a>";
			if (!empty($pdfLink))
				$description .= " - <a href=\"{$pdfLink}\">PDF guide</a>)";
			else
				$description .= ")";
			
			$seeAndDoContent .= <<<HTML
						<div class="lme-entry">
							$reviewImgHtml
							<div class="lme-data">
								<a href="{$link}">{$entry->title}</a>
								<div>{$description}</div>
							</div>
						</div>
HTML;
		}
		
		wp_enqueue_script("gmaps3", "http://maps.google.com/maps/api/js?sensor=false", null, null, true);
		wp_enqueue_script("local-market-explorer");
		
		if (version_compare($wp_version, '3.3', '<')) {
			$wp_scripts->in_footer[] = "gmaps3";
			$wp_scripts->in_footer[] = "local-market-explorer";
		}
		
		$content = <<<HTML
			<h2 class="lme-module-heading">Local Content from NileGuide</h2>
			<div class="lme-module lme-nileguide">
HTML;
		
		if (!empty($seeAndDoContent)) {
			$content .= <<<HTML
					<h3>Things to See and Do</h3>
					<div class="lme-seedo">
HTML;
			$content .= $seeAndDoContent;
			$pluginUrl = LME_PLUGIN_URL;
			$jsonResultsSerialized = json_encode($jsonResults);
			$content .= <<<HTML
					</div>
					<script>
						var lme = lme || {};
						lme.pluginUrl = '{$pluginUrl}';
						lme.nileGuideData = lme.nileGuideData || {};
						lme.nileGuideData['{$resultsId}'] = {$jsonResultsSerialized};
					</script>
					
					<hr />
					<h3>Map of Things to See and Do</h3>
					<div class="lme-seedo-map" data-resultsid="{$resultsId}"></div>
					
					<hr />
HTML;
		}
		$content .= <<<HTML
				<h3>Suggested Trip Itineraries</h3>
				<div class="lme-trips">
HTML;
		
		foreach ($apiResponse as $entry) {
			if ($entry->category->term != "/trip")
				continue;
			
			if (is_array($entry->link)) {
				foreach ($entry->link as $possibleLink) {
					if ($possibleLink->type == "text/html")
						$link = $possibleLink->href;
					else if ($possibleLink->type == "application/pdf")
						$pdfLink = $possibleLink->href;
				}
			} else {
				$link = $entry->link->href;
			}
			
			$image = (array)$entry->image;
			$image = $image["$"];
			if (!empty($image))
				$reviewImgHtml = "<a href=\"{$link}\"><img src=\"{$image}\" class=\"lme-photo\" /></a>";
			else
				$reviewImgHtml = "";
			
			$description = (array)$entry->content;
			$description = strip_tags($description["$"]);
			if (strlen($description) > 400)
				$description = substr($description, 0, 400) . "...";
			$description .= " (<a href=\"{$link}\">read more</a>";
			if (!empty($pdfLink))
				$description .= " - <a href=\"{$pdfLink}\">PDF guide</a>)";
			else
				$description .= ")";
			
			$content .= <<<HTML
						<div class="lme-entry">
							$reviewImgHtml
							<div class="lme-data">
								<a href="{$link}">{$entry->title}</a>
								<div>{$description}</div>
							</div>
						</div>
HTML;
		}
		$content .= <<<HTML
				</div>
				<a href="http://www.nileguide.com" target="_blank"><img class="lme-logo" src="{$logoSrc}" /></a>
				<div style="clear: both;"></div> <!-- IE 6 fix -->
			</div>
HTML;
		return $content;
	}
}

?>