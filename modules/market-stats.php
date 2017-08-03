<?php

class LmeModuleMarketStats {
	static function getApiUrls($opt_neighborhood, $opt_city, $opt_state, $opt_zip) {
		$options = get_option(LME_OPTION_NAME);
		$apiKey = $options["api-keys"]["zillow"];
		
		if (!$apiKey)
			return array();
		
		$urlBase = "http://www.zillow.com/webservice/";
		
		if (!empty($opt_zip)) {
			$locationParams = "zip={$opt_zip}";
		} else {
			$encodedCity = urlencode($opt_city);
			$locationParams = "city={$encodedCity}&state={$opt_state}"; 
			if (strlen($opt_neighborhood) > 0) {
				$encodedNeighborhood = urlencode($opt_neighborhood);
				$locationParams .= "&neighborhood={$encodedNeighborhood}";
			}
		}
		
		return array(
			"demographics"	=> "{$urlBase}GetDemographics.htm?zws-id={$apiKey}&{$locationParams}",
			"region-chart"	=> "{$urlBase}GetRegionChart.htm?zws-id={$apiKey}&{$locationParams}&unit-type=percent&width=235&height=250"
		);
	}
	static function getModuleHtml($apiResponses) {
		if (!$apiResponses["demographics"])
			return "";
		
		$demographics = @simplexml_load_string($apiResponses["demographics"])->response;
		$regionChart = @simplexml_load_string($apiResponses["region-chart"])->response;
		
		// it's unfortunate, but if Zillow fails to load, we don't want to hold up everything else
		if (empty($demographics) || empty($regionChart))
			return "";
		
		$zillowRegion = $demographics->region;
		$options = get_option(LME_OPTION_NAME);
		$zillowUrlSuffix = "#{scid=gen-api-wplugin";
		if (!empty($options["zillow-username"]))
			$zillowUrlSuffix .= "&scrnnm=" . $options["zillow-username"];
		$zillowUrlSuffix .= "}";
		
		if (isset($zillowRegion->neighborhood)) {
			$localNodeName = "neighborhood";
			$location = "{$zillowRegion->neighborhood}, {$zillowRegion->city}";
		} else if (isset($zillowRegion->zip)) {
			$localNodeName = "zip";
			$location = "{$zillowRegion->zip}";
		} else {
			$localNodeName = "city";
			$location = "{$zillowRegion->city}";
		}
		$zillowLocationUrl = $demographics[0]->links->main;
		
		$stateUrl = strtolower($zillowRegion->state);
		$mortgageUrlHtml = "Check out <a href=\"{$zillowRegion->zmmrateurl}\">{$zillowRegion->state} mortgage rates on Zillow</a>";
		
		$affordabilityData = $demographics[0]->xpath("pages/page[name='Affordability']/tables/table[name='Affordability Data']/data");
		$zhvi = $affordabilityData[0]->xpath("attribute[name='Zillow Home Value Index']/values/{$localNodeName}/value");
    if (isset($zhvi[0])) {
		  $zhvi = number_format((double)$zhvi[0]);
    }
    else {
      $zhvi = "0";
    }
		
		if ($zhvi != "0") {
			$zhviHtml = "<h3 class=\"lme-zhvi\"><a href=\"{$demographics->links->main}{$zillowUrlSuffix}\">Zillow Home Value Index: \${$zhvi}</a></h3>";
    }
    else {
      $zhviHtml = "";
    }
    
		
		$zhviDistributionChart = $demographics[0]->xpath("charts/chart[name='Zillow Home Value Index Distribution']/url");
		$homeSizeChart = $demographics[0]->xpath("charts/chart[name='Home Size in Square Feet']/url");
		$ownersRentersChart = $demographics[0]->xpath("charts/chart[name='Owners vs. Renters']/url");
		$yearBuiltChart = $demographics[0]->xpath("charts/chart[name='Year Built']/url");
	
    $noshowarr = array('PERCENT LISTING PRICE REDUCTION', 'MEDIAN LIST PRICE', 'HOMES FOR SALE','HOMES RECENTLY SOLD','MEDIAN VALUE PER SQ FT','HOMES FOR SALE BY OWNER','NEW CONSTRUCTION','FORECLOSURES');
  	
		$content = <<<HTML
			<h2 class="lme-module-heading">Real Estate Market Stats</h2>
			<div class="lme-module">
				{$zhviHtml}
				<div class="lme-market-charts-container">
					<!--<img src="{$regionChart->url}{$zillowUrlSuffix}" class="lme-zhvi-chart" />
					<div class="lme-market-charts">
            <div>
							<h4>Zillow Home Value Index</h4>
							<img src="{$zhviDistributionChart[0]}" />
						</div>
						<div>
							<h4>Owners vs. Renters</h4>
							<img src="{$ownersRentersChart[0]}" />
						</div>
					</div>
          //-->
					<div class="lme-market-chart-supplemental" style="clear: both;">
						<h4>Home Size in Square Feet</h4>
						<img src="{$homeSizeChart[0]}" />
					</div>
					<div class="lme-market-chart-supplemental">
						<h4>Year Built</h4>
						<img src="{$yearBuiltChart[0]}" />
					</div>
				</div>
				
				<table>
					<tr>
						<td></td>
						<th>Local</th>
						<th>National</th>
					</tr>
HTML;
		foreach ($affordabilityData[0]->attribute as $attribute) {
			if (empty($attribute->values->{$localNodeName}))
				continue;
			
			$name = htmlentities($attribute->name);
      if (in_array(trim(strtoupper($name)), $noshowarr)) {
        continue;
      }
			$value = (array)$attribute->values->{$localNodeName}->value;
			$nationalValue = (array)$attribute->values->nation->value;
			
			if ( (isset($value["@attributes"]["type"])) && ($value["@attributes"]["type"] == "USD") ) {
				$value = "$" . number_format(intval($value["0"]));
				$nationalValue = "$" . number_format(intval($nationalValue["0"]));
			} else if ( (isset($value["@attributes"]["type"])) && ($value["@attributes"]["type"] == "percent") ) {
				$value = ($value["0"] * 100) . "%";
				$nationalValue = ($nationalValue["0"] * 100) . "%";
			} else {
        if (isset($value["0"])) {
				  $value = number_format(intval($value["0"]));
        }
        else {
          $value = number_format(intval(0));
        }
        if (isset($nationalValue["0"])) {
				  $nationalValue = number_format(intval($nationalValue["0"]));
        }
        else {
          $nationalValue = number_format(intval(0));
        }
			}
?>
<?php			
			$content .= <<<HTML
					<tr>
						<th>{$name}</th>
						<td>{$value}</td>
						<td>{$nationalValue}</td>
					</tr>
HTML;
		}
		
    $zillow_logo_provided_a = LME_PLUGIN_URL .  "images/logos/Zillowlogo_150x40.gif";
		$content .= <<<HTML
				</table>
				<div class="lme-market-location-url">
					<a href="{$zillowLocationUrl}{$zillowUrlSuffix}" target="_blank">See {$location} home values at Zillow.com</a><br />
					{$mortgageUrlHtml}
				</div>
				<a href="http://www.zillow.com/{$zillowUrlSuffix}" target="_blank">
					<img class="lme-market-logo" src="{$zillow_logo_provided_a}" /></a>
				<div style="clear: both;"></div> <!-- IE 6 fix -->
			</div>
HTML;
		return $content;
	}
}

?>