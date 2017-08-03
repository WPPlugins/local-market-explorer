<?php
add_filter("rewrite_rules_array", array("LmeModulesPageRewrite", "insertRules"));
add_filter("query_vars", array("LmeModulesPageRewrite", "saveQueryVars"));

class LmeModulesPageRewrite {
	static function insertRules($incomingRules) {
		$lmeRules = array(
			"^local/(\d{5})$"					=> 'index.php?lme-action=1&lme-zip=$matches[1]',
			"^local/([^/]+)/(\w{2})$"			=> 'index.php?lme-action=1&lme-city=$matches[1]&lme-state=$matches[2]',
			"^local/([^/]+)/([^/]+)/(\w{2})$"	=> 'index.php?lme-action=1&lme-neighborhood=$matches[1]&lme-city=$matches[2]&lme-state=$matches[3]'
		);
		return $lmeRules + $incomingRules;
	}
	static function saveQueryVars($queryVars) {
		$queryVars[] = "lme-action";
		$queryVars[] = "lme-zip";
		$queryVars[] = "lme-neighborhood";
		$queryVars[] = "lme-city";
		$queryVars[] = "lme-state";

		return $queryVars;
	}
	static function getCanonicalLink($zip, $city, $neighborhood, $state) {
		if (!empty($zip)) {
			$locationUrl = "/local/{$zip}";
		} else {
			$prettyUrlRegex = '/^[\w\d\s\-_]+$/';
			
			$citySlug = urlencode(strtolower(str_replace(array("-", " "), array("_", "-"), $city)));
			$allowPrettyCity = preg_match($prettyUrlRegex, $citySlug);
			
			if (empty($neighborhood)) {
				if ($allowPrettyCity)
					$locationUrl = "/local/{$citySlug}/" . strtolower($state) . "/";
				else
					$locationUrl = "/?lme-action=1&lme-city=" . urlencode($citySlug) . "&lme-state=" . strtolower($state);
			} else {
				$neighborhoodSlug = urlencode(strtolower(str_replace(array("-", " "), array("_", "-"), $neighborhood)));
				$allowPrettyNeighborhood = preg_match($prettyUrlRegex, $neighborhoodSlug);
				
				if ($allowPrettyCity && $allowPrettyNeighborhood)
					$locationUrl = "/local/{$neighborhoodSlug}/{$citySlug}/" . strtolower($state) . "/";
				else if ($allowPrettyCity)
					$locationUrl = "/local/{$citySlug}/" . strtolower($state) . "/?lme-neighborhood=" . urlencode($neighborhoodSlug);
				else
					$locationUrl = "/?lme-action=1&lme-city=" . urlencode($citySlug) . "&lme-neighborhood=" . urlencode($neighborhoodSlug) . "&lme-state=" . strtolower($state);
			}
			
		}
		return $locationUrl;
	}
}
?>