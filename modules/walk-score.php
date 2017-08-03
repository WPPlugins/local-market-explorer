<?php

class LmeModuleWalkScore {
	static function getModuleHtml($opt_neighborhood, $opt_city, $opt_state, $opt_zip) {
		$options = get_option(LME_OPTION_NAME);
		$apiKey = $options["api-keys"]["walk-score"];
	
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
		
    $apiKey = preg_replace('/[^a-z0-9_\-]/', '', esc_js(urlencode($apiKey)));
    $location = preg_replace('/[^a-zA-Z0-9_\-,\']/', '', esc_js(urldecode(urlencode($locationParams))));
    $location = str_replace("'", "\\'", $location);
    $lme_plugin_url = LME_PLUGIN_URL;
		
		return <<<HTML
			<h2 class="lme-module-heading">Walk Score</h2>
			<div class="lme-module lme-walk-score">
      
        <div id="ws-walkscore-tile">
		      <div id="ws-footer">
            <form id="ws-form">
              <a id="ws-a" href="http://www.walkscore.com/" target="_blank">Find out your home's Walk Score: </a>
				      <input type="text" id="ws-street" />
				      <input type="image" id="ws-go" src="{$lme_plugin_url}images/go-button.gif" height="15" width="22" border="0" alt="get my Walk Score" />
			     </form>
		      </div>
        </div>
        <script>
		      var ws_wsid = '{$apiKey}';
		      var ws_address = '{$location}';
		      var ws_width = "570";
		      var ws_height = "286";
		      var ws_layout = "horizontal";
        </script>
        <script src="{$lme_plugin_url}assets/show-walkscore-tile.php"></script>
      </div>
HTML;
	}
}

?>