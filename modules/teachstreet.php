<?php

class LmeModuleTeachStreet {
	static function getApiUrls($opt_neighborhood, $opt_city, $opt_state, $opt_zip) {
		$url = "http://www.teachstreet.com/lme/classes.json?where=";
		
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
		
		$url .= $locationParams;
		return array(
			"teachstreet" => "{$url}"
		);
	}
	static function getModuleHtml($classes) {
		$apiResponse = @json_decode($classes["teachstreet"]);
		
		if (empty($apiResponse))
			return;
		
		$classes = $apiResponse->items;
		
		if (empty($classes))
			return;
		
		$logoSrc = LME_PLUGIN_URL . "images/logos/teachstreet.png";
		
		$content = <<<HTML
			<h2 class="lme-module-heading">Local Classes (via TeachStreet)</h2>
			<div class="lme-module lme-teachstreet">
				<div class="lme-classes">
HTML;
		foreach ($classes as $class) {
			if (!empty($class->image))
				$reviewImgHtml = "<a href=\"{$class->url}\"><img src=\"{$class->image}\" class=\"lme-photo\" /></a>";
			
			$content .= <<<HTML
					<div class="lme-class">
						$reviewImgHtml
						<div class="lme-data">
							<a href="{$review->url}">{$review->title}</a>
							<div>Taught by <a href="{$class->teacher->url}">{$class->teacher->name}</a></div>
							<div>See more <a href="{$class->category->url}">{$class->category->name} classes in {$apiResponse->region}</a></div>
						</div>
					</div>
HTML;
		}
		$content .= <<<HTML
				</div>
				<div class="lme-branding">
					<a href="{$apiResponse->region_browse_url}" target="_blank">
						Find more classes and teachers in {$apiResponse->region}</a>
				</div>
				<a href="http://www.teachstreet.com" target="_blank"><img class="lme-logo" src="{$logoSrc}" /></a>
				<div style="clear: both;"></div> <!-- IE 6 fix -->
			</div>
HTML;
		return $content;
	}
}

?>