<?php

class LmeModuleDsIdxPress {
	static function getModuleHtml($opt_neighborhood, $opt_city, $opt_state, $opt_zip) {
		$hasdsidxpress = false;
	
		foreach (get_option("active_plugins") as $plugin) {
			$pluginData = get_plugin_data(WP_CONTENT_DIR . "/plugins/$plugin");
			if ($pluginData["Name"] == "dsIDXpress") {
				$hasdsidxpress = true;
				break;
			}
		}
		
		if (!$hasdsidxpress)
			return;
		
		if (isset($opt_zip)) {
			$shortcode = "[idx-listings zip=\"{$opt_zip}\"]";
		} else {
			$shortcode = "[idx-listings city=\"{$opt_city}\"]";
		}
		
		$content = do_shortcode($shortcode);
		return <<<HTML
			<h2 class="lme-module-heading">Newest Real Estate Listings</h2>
			<div class="lme-module lme-about-area">
				{$content}
				<div style="clear: both;"></div> <!-- IE 6 fix -->
			</div>
HTML;
	}
}

?>