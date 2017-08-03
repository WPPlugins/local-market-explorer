<?php
class LmeShortcodes {
	static function Module($atts, $content = null, $code = "") {
		$neighborhood = empty($atts["neighborhood"]) ? "" : $atts["neighborhood"];
		$city = empty($atts["city"]) ? "" : $atts["city"];
		$state = empty($atts["state"]) ? "" : $atts["state"];
		$zip = empty($atts["zip"]) ? "" : $atts["zip"];
		$modules = array();
		
		if ($atts["module"] == "market-stats") {
			$modules[] = LmeModuleMarketStats::getApiUrls($neighborhood, $city, $state, $zip);
		} else if ($atts["module"] == "market-activity") {
			$modules[] = LmeModuleMarketActivity::getApiUrls($neighborhood, $city, $state, $zip);
		} else if ($atts["module"] == "yelp") {
			$modules[] = LmeModuleYelp::getApiUrls($neighborhood, $city, $state, $zip);
		} else if ($atts["module"] == "neighborhoods") {
			$modules[] = LmeModuleNeighborhoods::getApiUrls($neighborhood, $city, $state, $zip);
		}
		
		$moduleContent = LmeApiRequester::gatherContent($modules);
	
		if ($atts["module"] == "market-stats") {
			return LmeModuleMarketStats::getModuleHtml($moduleContent[0]);
		} else if ($atts["module"] == "market-activity") {
			return LmeModuleMarketActivity::getModuleHtml($moduleContent[0]);
		} else if ($atts["module"] == "yelp") {
			return LmeModuleYelp::getModuleHtml($moduleContent[0]);
		} else if ($atts["module"] == "walk-score") {
			return LmeModuleWalkScore::getModuleHtml($neighborhood, $city, $state, $zip);
		} else if ($atts["module"] == "streetadvisor") {
			return LmeModuleStreetAdvisor::getModuleHtml($neighborhood, $city, $state, $zip);
		} else if ($atts["module"] == "about") {
			return LmeModuleAboutArea::getModuleHtml($neighborhood, $city, $state, $zip);
		} else if ($atts["module"] == "neighborhoods") {
			return LmeModuleNeighborhoods::getModuleHtml($moduleContent[0], $neighborhood, $city, $state, $zip);
		} 
	}
}

add_shortcode("lme-module", array("LmeShortcodes", "Module"));
?>