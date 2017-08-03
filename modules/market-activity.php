<?php

class LmeModuleMarketActivity {
	static function getApiUrls($opt_neighborhood, $opt_city, $opt_state, $opt_zip) {
		$options = get_option(LME_OPTION_NAME);
		$apiKey = $options["api-keys"]["zillow"];
		
		if (!$apiKey)
			return array();
		
		$url = "http://www.zillow.com/webservice/FMRWidget.htm?status=recentlySold&zws-id={$apiKey}&region=";
		
		if (!empty($opt_zip)) {
			$locationParams = "{$opt_zip}";
		} else {
			$encodedCity = urlencode($opt_city);
			$locationParams = "{$encodedCity},{$opt_state}"; 
			if (strlen($opt_neighborhood) > 0) {
				$encodedNeighborhood = urlencode($opt_neighborhood);
				$locationParams = "{$encodedNeighborhood},{$locationParams}";
			}
		}
		
		return array(
			"recent-sales"	=> "{$url}{$locationParams}"
		);
	}
	static function getModuleHtml($apiResponses) {
		if (!$apiResponses["recent-sales"])
			return "";
		
		$activity = @simplexml_load_string($apiResponses["recent-sales"])->response;
		$arrayActivity = (array)$activity;
		if (empty($arrayActivity))
			return;
		unset($arrayActivity);
		
		$options = get_option(LME_OPTION_NAME);
		$zillowRegion = $activity->region;
		$zillowUrlSuffix = "#{scid=gen-api-wplugin";
		if (!empty($options["zillow-username"]))
			$zillowUrlSuffix .= "&scrnnm=" . $options["zillow-username"];
		$zillowUrlSuffix .= "}";
		
		if (isset($zillowRegion->neighborhood)) {
			$location = "{$zillowRegion->neighborhood}, {$zillowRegion->city}";
		} else if (isset($zillowRegion->zip)) {
			$location = "{$zillowRegion->zip}";
		} else {
			$location = "{$zillowRegion->city}";
		}
		
		$stateUrl = strtolower($zillowRegion->state);
		$cityUrl = str_replace(" ", "-", strtolower($zillowRegion->city));
		if ($stateUrl && $cityUrl) {
			$mortgageUrl = "http://www.zillow.com/mortgage-rates/{$stateUrl}/{$cityUrl}/{$zillowUrlSuffix}";
			$mortgageUrlHtml = "View <a href=\"{$mortgageUrl}\">{$zillowRegion->city}, {$zillowRegion->state} mortgage rates on Zillow</a>";
		}
		
		$content = <<<HTML
			<h2 class="lme-module-heading">Real Estate Market Activity</h2>
			<div class="lme-module lme-market-activity">
				<h3>Recently sold {$location} homes</h3>
HTML;
		
		$resultsShown = 0;
		foreach ($activity->results->result as $soldProperty) {
			$resultsShown++;
			if ($resultsShown > 10)
				continue;
				
			$soldPrice = number_format((double)$soldProperty->lastSoldPrice);
			$finishedSqFt = number_format((string)$soldProperty->finishedSqFt);
			
			$content .= <<<HTML
				<div class="lme-recently-sold">
					<a href="{$soldProperty->detailPageLink}{$zillowUrlSuffix}"><img src="{$soldProperty->largeImageLink}" /></a>
					<div class="lme-data">
						<div>
							<a href="{$soldProperty->detailPageLink}{$zillowUrlSuffix}">{$soldProperty->address->street},
								{$soldProperty->address->city}, {$soldProperty->address->state}</a>
						</div>
						<div>Sold {$soldProperty->lastSoldDate} for \${$soldPrice}</div>
						<div>{$soldProperty->bedrooms} beds, {$soldProperty->bathrooms} baths, {$finishedSqFt} sq ft</div>
					</div>
				</div>
HTML;
		}
		
		$content .= <<<HTML
				<div style="clear: both;"></div> <!-- IE 6 fix -->
				<a href="{$activity->links->forSale}{$zillowUrlSuffix}" target="_blank">
					See {$location} real estate and homes for sale</a><br />
				{$mortgageUrlHtml}
				<div style="clear: both;"></div> <!-- IE 6 fix -->
			</div>
HTML;
		return $content;
	}
}

?>