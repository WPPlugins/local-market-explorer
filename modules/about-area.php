<?php

class LmeModuleAboutArea {
	static function getModuleHtml($opt_neighborhood, $opt_city, $opt_state, $opt_zip) {
		global $wpdb;
		$sqlPreWhereClause = "SELECT description FROM " . LME_AREAS_TABLE . " WHERE ";
		
		if (!empty($opt_zip)) {
			$query = $wpdb->prepare($sqlPreWhereClause . "zip = %s", $opt_zip);
		} else if (strlen($opt_neighborhood) > 0) {
			$query = $wpdb->prepare(
				$sqlPreWhereClause . "neighborhood = %s AND city = %s AND state = %s",
				$opt_neighborhood,
				$opt_city,
				$opt_state
			);
		} else {
			$query = $wpdb->prepare(
				$sqlPreWhereClause . "neighborhood = '' AND city = %s AND state = %s",
				$opt_city,
				$opt_state
			);
		}
		
		// since this is their own blog, we specifically allow all HTML in the description
		$description = $wpdb->get_col($query);
		
		if (count($description) == 0 || empty($description[0]))
			return;
		$description = $description[0];
		
		return <<<HTML
			<h2 class="lme-module-heading">About Area</h2>
			<div class="lme-module lme-about-area">
				{$description}
				<div style="clear: both;"></div> <!-- IE 6 fix -->
			</div>
HTML;
	}
}

?>