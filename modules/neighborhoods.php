<?php

class LmeModuleNeighborhoods {
	static function getApiUrls($opt_neighborhood, $opt_city, $opt_state, $opt_zip) {
		if (!empty($opt_neighborhood) || !empty($opt_zip))
			return array();
		
		$options = get_option(LME_OPTION_NAME);
		$apiKey = $options["api-keys"]["zillow"];
		$city = urlencode($opt_city);
		
		$url = "http://www.zillow.com/webservice/GetRegionChildren.htm?&zws-id={$apiKey}&city={$city}&state={$opt_state}&childtype=neighborhood";
		
		return array("neighborhoods" => $url);
	}
	static function getModuleHtml($apiResponses, $opt_neighborhood, $opt_city, $opt_state, $opt_zip) {
		if (!empty($opt_neighborhood) || !empty($opt_zip))
			return;
		
		$neighborhoods = @simplexml_load_string($apiResponses["neighborhoods"]);
		if (empty($neighborhoods))
			return;
		
		$neighborhoods = $neighborhoods->response->list->region;
		if (!count($neighborhoods))
			return;
		
		$sortedNeighborhoods = array();
		foreach ($neighborhoods as $neighborhood)
			array_push($sortedNeighborhoods, (string)$neighborhood->name);
		sort($sortedNeighborhoods);
		
		$city = ucwords($opt_city);
		$state = strtoupper($opt_state);
		
		$encCity = htmlentities($city);
		$encState = htmlentities($state);
		
		$content = <<<HTML
			<h2 class="lme-module-heading">Neighborhoods in {$encCity}, {$encState}</h2>
			<div class="lme-module lme-neighborhoods">
HTML;
		
		foreach ($sortedNeighborhoods as $neighborhood) {
			$encLmeUrl = htmlentities(LmeModulesPageRewrite::getCanonicalLink(null, $city, $neighborhood, $state));
			$encNeighborhood = htmlentities($neighborhood);
      $siteURL = get_site_url();
			
			$content .= <<<HTML
				<div class="lme-neighborhood">
					<a href="{$siteURL}{$encLmeUrl}">{$encNeighborhood}</a>
				</div>
HTML;
		}
		
		$content .= <<<HTML
				<div style="clear: both;"></div> <!-- IE 6 fix -->
			</div>
HTML;
		return $content;
	}
}

?>