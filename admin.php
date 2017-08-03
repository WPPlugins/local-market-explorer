<?php
add_action("admin_init", array("LmeAdmin", "initialize"));
add_action("admin_menu", array("LmeAdmin", "addMenu"));
add_action("wp_ajax_lme-proxyZillowApiRequest", array("LmeAdmin", "proxyZillowApiRequest"));

class LmeAdmin {
	static function addMenu() {
		$optionsPage = add_options_page("Local Market Explorer Options", "Local Market Explorer", "manage_options", "lme",
			array("LmeAdmin", "editOptions"));
		add_action("admin_print_scripts-{$optionsPage}", array("LmeAdmin", "loadHeader"));
		
		add_filter("mce_external_plugins", array("LmeAdmin", "addTinyMcePlugin"));
		add_filter("mce_buttons", array("LmeAdmin", "registerTinyMceButton"));
	}
	static function addTinyMcePlugin($plugins) {
		$plugins["lmemodule"] = LME_PLUGIN_URL . "tinymce/lmemodule/editor_plugin.js";
		return $plugins;
	}
	static function registerTinyMceButton($buttons) {
		array_push($buttons, "separator", "lmemodule");
		return $buttons;
	}
	static function initialize() {
		register_setting(LME_OPTION_NAME, LME_OPTION_NAME, array("LmeAdmin", "sanitizeOptions"));
	}
  static function load_custom_wp_admin_style() {
  }
	static function loadHeader() {
		$pluginUrl = LME_PLUGIN_URL;
		wp_enqueue_script("yui-3", "{$pluginUrl}js/yui-min.js", null, "3.1.1", true);
		wp_enqueue_script("lme-admin", "{$pluginUrl}js/admin.js", array("jquery", "jquery-ui-sortable"), LME_PLUGIN_VERSION, true);
    wp_register_style('jquery-ui', LME_PLUGIN_URL . 'css/jquery-ui-1.7.2.custom.css', false, '1.7.2');
    wp_enqueue_style('jquery-ui');
    wp_register_style('admin-css', LME_PLUGIN_URL . 'css/admin.css', false, '1.0');
    wp_enqueue_style('admin-css');
	}

	static function editOptions() {
		global $wpdb;
		
		$blogUrl = get_bloginfo("url");
		$options = get_option(LME_OPTION_NAME);
		$areas = $wpdb->get_results("SELECT * FROM " . LME_AREAS_TABLE . " ORDER BY state, city, neighborhood, zip");
		$checkedModules = array(); 
		
		$moduleInfo = array(
			"about" => array(
				"name" => "About area",
				"description" => "your own description"),
			"market-stats" => array(
				"name" => "Market statistics",
				"description" => "area statistics from <a href=\"http://www.zillow.com\">Zillow</a>"),
			"neighborhoods" => array(
				"name" => "Neighborhoods",
				"description" => "neighborhoods (only for cities)"),
			"market-activity" => array(
				"name" => "Market activity",
				"description" => "recent sales from <a href=\"http://www.zillow.com\">Zillow</a>"),
			"walk-score" => array(
				"name" => "Walk Score",
				"description" => "see <a href=\"http://www.walkscore.com\">Walk Score</a>"),
			"streetadvisor" => array(
				"name" => "StreetAdvisor",
				"description" => "see <a href=\"http://www.streetadvisor.com/\">StreetAdvisor</a>"),
			"dsidxpress" => array(
				"name" => "Newest real estate",
				"description" => "from <a href=\"http://www.dsidxpress.com\">dsIDXpress</a> by <a href=\"http://www.diversesolutions.com\">Diverse Solutions</a>"),
		);
	
		$listItemHtml = <<<HTML
			<li id="lme-areas-#{id}" class="lme-area">
				<div class="lme-areas-citystate-container">
					<div>
						<label>City, State:</label>
						<input type="text" class="lme-areas-city lme-location-input" name="lme-areas[#{id}][city]" value="#{city}" />,
						<input type="text" class="lme-areas-state lme-location-input" name="lme-areas[#{id}][state]" value="#{state}" />
					</div>
					
					<div style="clear: both; position: relative;">
						<label>
							Neighborhood <span class="lme-small">(optional)</span>:
						</label>
						<select class="lme-areas-neighborhood lme-location-input" name="lme-areas[#{id}][neighborhood]" disabled="disabled">
							#{neighborhoodOptions}
						</select>
					</div>
				</div>
				
				<div class="lme-areas-or">- or -</div>
				
				<div class="lme-areas-zip-container">
					<label>Zip:</label>
					<input type="text" class="lme-areas-zip lme-location-input" name="lme-areas[#{id}][zip]" value="#{zip}" />
				</div>
				
				<div class="lme-areas-description-container">
					<label>Description <span class="lme-small">(HTML allowed)</span>:</label><br />
					<textarea name="lme-areas[#{id}][description]">#{description}</textarea>
				</div>
				
				<div class="lme-link"></div>
				<div class="lme-areas-remove-container">
					<input type="button" class="lme-areas-remove button-secondary" value="Remove this area's description" />
				</div>
			</li>
HTML;
		$moduleOrderHtml = <<<HTML
			<li class="ui-state-default">
				<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
				<input type="checkbox" id="local-market-explorer[global-modules][#{internal-name}]"
					name="local-market-explorer[global-modules][#{internal-name}]" #{checked} />
				<label for="local-market-explorer[global-modules][#{internal-name}]">#{name}</label>
				<span class="lme-small">(#{short-description})</span>
			</li>
HTML;
?>
	<div class="wrap lme">
		<div class="icon32" id="icon-options-general"><br/></div>
		<h2>Local Market Explorer Options</h2>
		<form method="post" action="options.php">
<?php
			settings_fields(LME_OPTION_NAME);
?>
			<script>
			var lmeadmin = lmeadmin || {};
			lmeadmin.blogUrl = '<?php echo $blogUrl ?>';
			</script>
			<div class="yui3-skin-sam" style="margin-top: 30px;">
				<div id="lme-options">
					<ul>
						<li><a href="#general-options">General Options</a></li>
						<li><a href="#module-page-options">Module Page Options</a></li>
						<li><a href="#help">Help</a></li>
						<li><a href="#about">About</a></li>
					</ul>
					<div>
						<div>
							<h3>API Keys</h3>
							<p>
								In order for Local Market Explorer to load the data for the different panels, you'll need to collect a few API
								keys around the web and plug them in here.
							</p>
							<table class="form-table lme-api-keys">
								<tr>
									<th>
										<label for="local-market-explorer[api-keys][zillow]">
											Zillow API key:<br>
											<span style="font-size: 10px;">(<a href="https://www.zillow.com/webservice/Registration.htm" target="_blank">get one</a>)</span>
										</label>
									</th>
									<td>
										<input class="lme-api-key" type="text" id="local-market-explorer[api-keys][zillow]"
											name="local-market-explorer[api-keys][zillow]"
											value="<?php if (isset($options["api-keys"]["zillow"])) { echo $options["api-keys"]["zillow"]; } ?>" />
									</td>
								</tr>
								<tr>
									<th>
										<label for="local-market-explorer[api-keys][walk-score]">
											Walk Score ID:<br>
											<span style="font-size: 10px;">(<a href="http://www.walkscore.com/professional/sign-up" target="_blank">get one</a>)</span>
										</label>
									</th>
									<td>
										<input class="lme-api-key" type="text" id="local-market-explorer[api-keys][walk-score]"
											name="local-market-explorer[api-keys][walk-score]"
											value="<?php if (isset($options["api-keys"]["walk-score"])) { echo $options["api-keys"]["walk-score"]; } ?>" />
									</td>
								</tr>
							</table>
							
							<h3 style="margin-top: 40px;">Other Options</h3>
							<ul id="lme-other-options">
								<li>
									<input type="text" name="local-market-explorer[zillow-username]"
										id="local-market-explorer[zillow-username]" value="<?php if (isset($options["zillow-username"])) { echo $options["zillow-username"]; } ?>" />
									<label for="local-market-explorer[zillow-username]">
										Your username on Zillow.com (for your branding when clicking through on the links)</label>
								</li>
							</ul>
						</div>
						<div>

							<h3>Modules to Display and Module Order</h3>
							<p>
								<i>These settings only apply to the pre-built Local Market Explorer "virtual" pages.</i> Place a check in the
								checkbox for all of the modules you want to display and / or reorder the modules by simply dragging and
								dropping them to the desired order.
							</p>
							<ul id="lme-modules-to-display">
<?php
		$moduleOrderReplacements = array("#{internal-name}", "#{checked}", "#{name}", "#{short-description}");
		if (count($options["global-modules"])) {
			foreach ($options["global-modules"] as $module) {
				if (!isset($moduleInfo[$module]))
					continue;
				$moduleOrderValues = array(
					$module,
					"checked='checked'",
					$moduleInfo[$module]["name"],
					$moduleInfo[$module]["description"]
				);
				echo str_replace($moduleOrderReplacements, $moduleOrderValues, $moduleOrderHtml);
			}
		}
		foreach (array_keys($moduleInfo) as $module) {
			if (is_array($options["global-modules"]) && in_array($module, $options["global-modules"]))
				continue;
			$moduleOrderValues = array(
				$module,
				"",
				$moduleInfo[$module]["name"],
				$moduleInfo[$module]["description"]
			);
			echo str_replace($moduleOrderReplacements, $moduleOrderValues, $moduleOrderHtml);
		}
?>
							</ul>
							
							<h3 style="margin-top: 40px;">Area Descriptions</h3>
							<p>
								<i>These settings only apply to the pre-built Local Market Explorer "virtual" pages.</i> You can add descriptions
								for different areas that will show up when the virtual page for that area loads.
							</p>
							<ul id="lme-areas-descriptions">
<?php
		$listItemReplacements = array("#{id}", "#{city}", "#{state}", "#{neighborhoodOptions}", "#{zip}", "#{description}");
		foreach ($areas as $area) {
			if ($area->neighborhood)
				$neighborhood = "<option value='{$area->neighborhood}'>{$area->neighborhood}</option>";
			else
				$neighborhood = "<option value=''>(click to load neighborhoods)</option>"; 
			$listItemValues = array(
				$area->id,
				$area->city,
				$area->state,
				$neighborhood,
				$area->zip,
				htmlentities($area->description)
			);
			echo str_replace($listItemReplacements, $listItemValues, $listItemHtml);
		}
		echo str_replace($listItemReplacements, array("new", "", "", "<option value=''>(enter a city / state first)</option>", "", ""), $listItemHtml);
?>
								<li>
									<div style="text-align: right;">
										<input id="lme-areas-add" type="button" class="button-secondary" value="Add description for a new area" />
									</div>
								</li>
							</ul>
						</div>
						<div>
							<h3>Using Local Marker Explorer</h3>
							<p>
								Local Marker Explorer is essentially a collection of modules that can be displayed on
								its own pre-created pages or on any page or post you desire. The two ways you can use
								these modules are described below. 
							</p>
							<h4>Method 1</h4>
							<div style="margin-left: 30px;">
								<p>
									The Local Market Explorer plugin is triggered whenever a URL matches the form of
									www.yourblog.com/local/XXXX where XXXX is a city/state, neighborhood/city/state,
									or zip code. Following are examples of these three types of links that will work as
									soon as the API keys are filled in:
								</p>
								<ul style="margin-left: 20px;">
									<li><a href="<?php echo $blogUrl ?>/local/downtown/los-angeles/ca/"><?php echo $blogUrl ?>/local/downtown/los-angeles/ca/</a></li>
									<li><a href="<?php echo $blogUrl ?>/local/queen-anne/seattle/wa/"><?php echo $blogUrl ?>/local/queen-anne/seattle/wa/</a></li>
									<li><a href="<?php echo $blogUrl ?>/local/los-angeles/ca/"><?php echo $blogUrl ?>/local/los-angeles/ca/</a></li>
									<li><a href="<?php echo $blogUrl ?>/local/seattle/wa/"><?php echo $blogUrl ?>/local/seattle/wa/</a></li>
									<li><a href="<?php echo $blogUrl ?>/local/92651/"><?php echo $blogUrl ?>/local/92651/</a></li>
								</ul>
								<p>
									Any valid city / state, neighborhood / city / state, or zip code will work even
									without first adding a description in the "Area Descriptions" area in the "Module Page
									Options" tab. If using a city/state or neighborhood/city/state URL, you'll want to
									use all lowercase letters and replace any spaces with hyphens. For example, if you
									wanted a URL for Mission Viejo, CA, the URL would look like
									<?php echo $blogUrl ?>/local/downtown/mission-viejo/ca/.
								</p>
								<p>
									These pages don't need to be pre-loaded, updated in the database, or otherwise
									initialized in any way. Don't think of these URLs as typical pages or posts; instead,
									think of them as virtual pages that are created as soon as the URLs are	requested and
									are always available. 
								</p>
							</div>
							<h4>Method 2</h4>
							<div style="margin-left: 30px;">
								<p>
									Each individual module can also be inserted into any of your pages or posts via
									WordPress's shortcode system. Shortcodes are essentially text snippets that are
									replaced with the module data when the pages / posts are loaded for your visitors.
								<p>
									Shortcodes are always contained within brackets, and Local Market Explorer's
									shortcodes always start with <span style="font-face: Courier New;">lme-module</span>.
									The lme-module shortcode always contains a "module" attribute as well as attributes to
									specify the desired location to load the data for. The order of the attributes doesn't
									matter. The valid module attributes are as follows:
								</p>
								<ul style="margin-left: 20px;">
									<li>about</li>
									<li>market-stats</li>
									<li>market-activity</li>
									<li>walk-score</li>
                  <li>streetadvisor</li>
								</ul>
								<p>
									Note that local real estate via <a href="http://www.dsidxpress.com">dsIDXpress</a> is
									NOT included in that list as it has its own shortcodes you can use.
								<p>
									For the location attributes, you'll need to specify either "city" and "state",
									"neighborhood" "city" and "state", or "zip". You'll want to use the full names
									of these areas for the values (including spaces). Following are some shortcode
									examples:
								</p>
								<ul>
									<li>[lme-module module="market-stats" city="Laguna Beach" state="CA"]</li>
									<li>[lme-module module="market-activity" neighborhood="Downtown" city="Los Angeles" state="CA"]</li>
									<li>[lme-module module="walk-score" zip="92651"]</li>
								</ul>
							</div>
						</div>
						<div>
              <p>This plugin is open-source donationware. We’re willing to accept and integrate well-written patches into the code, but the continued development of the plugin 
              (new features, bug fixes, etc) by the plugin author is funded by either donations or companies willing to pay a fee to have their data integrated. You can thank 
              Zillow, and <a href="http://www.streetadvisor.com/" target="_blank">StreetAdvisor</a> for funding the vast majority of the 
              development thus far. This plugin is currently funded by <a href="http://www.streetadvisor.com/" target="_blank">StreetAdvisor</a>, and being developed by 
              <a href="http://caffeineinteractive.com/" target="_blank">Caffeine Interactive</a>.</p>

              <p>If you'd like to contribute a feature suggestion or need to document a bug, please use the <a href="http://localmarketexplorer.uservoice.com/" target="_blank">User Voice forum</a> 
              set up specifically for that purpose. With User Voice, each user gets a fixed number of votes that they can cast for any particular bug or feature. The higher the 
              number of votes for an item, the higher the priority will be for that item as development commences on the plugin itself.</p>
            
							<p>
								<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=dustin.devries%40caffeineconsulting%2ecom&lc=US&item_name=Local%20Market%20Explorer&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest">
									<img src="<?php echo LME_PLUGIN_URL; ?>images/logos/btn_donateCC_LG.gif" alt="PayPal - The safer, easier way to pay online!" />
								</a>
							</p>
						</div>
					</div>
				</div>
			</div>
			
			<div style="text-align: right; margin: 30px 0;">
				<input id="lme-save-options" type="submit" class="button-primary" name="Submit" value="Save Options" />
			</div>
		</form>
	</div>
<?php
	}
	static function sanitizeOptions($options) {
		$areas = $_POST["lme-areas"];
		
		if (sizeof($options["global-module-orders"]) > 0 && isset($options["global-module-orders"][0]))
			$options["global-modules"] = explode(",", $options["global-module-orders"]);
		unset($options["global-module-orders"]);
		
		LmeAdmin::addNewAreaDescriptions($areas);
		
		return $options;
	}
	static function addNewAreaDescriptions($newAreasArray) {
		global $wpdb;
		
		unset($newAreasArray["new"]);
		// clear everything out and re-add
		$wpdb->query("DELETE FROM " . LME_AREAS_TABLE);
		foreach ($newAreasArray as $area) {
			$area = stripslashes_deep($area);
			$wpdb->insert(
				LME_AREAS_TABLE,
				array(
					"city"			=> $area["city"],
					"neighborhood"	=> $area["neighborhood"],
					"zip"			=> $area["zip"],
					"state"			=> $area["state"],
					"description"	=> $area["description"]
				),
				array("%s", "%s", "%s", "%s", "%s")
			);
		}
	}
	static function proxyZillowApiRequest() {
    $theApikey = preg_replace("/[^a-z0-9_\-]+/i", "", esc_js($_GET["api"]));
    $apiBase = "http://www.zillow.com/webservice/" . urlencode($theApikey) . ".htm";
		$apiParams = $_GET["apiParams"];
                                             
    $finalApiUrl = $apiBase;
    $finalApiUrl = add_query_arg( $apiParams, $finalApiUrl );
		if (empty($apiParams["zws-id"])) {
			$options = get_option(LME_OPTION_NAME);
      $finalApiUrl = add_query_arg( array('zws-id' => urlencode($options["api-keys"]["zillow"])), $finalApiUrl );
		}
    $finalApiUrl = add_query_arg( array('output' => 'json'), $finalApiUrl );    

		$apiResponse = wp_remote_get($finalApiUrl);
		
		header("Cache-Control: max-age=86400"); // we'll consider responses to be valid for a day
		echo json_encode(simplexml_load_string($apiResponse["body"]));
		die();
	}
}
?>