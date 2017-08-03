<?php
class LmeAreasWidget extends WP_Widget {
	function LmeAreasWidget() {
		parent::__construct( 
      "LmeAreas", 
      "Local Market Explorer Areas", 
      array(
			 "classname" => "lme-areas",
			 "description" => "Lists of Local Market Explorer areas with descriptions"
		  )
    );
	}
	function widget($args, $instance) {
		global $wpdb;
		
		$title = apply_filters("widget_title", $instance["title"]);
		echo $args["before_widget"];
		if ($title)
			echo $args["before_title"] . $title . $args["after_title"];
		
		$areas = $wpdb->get_results("SELECT * FROM " . LME_AREAS_TABLE . " ORDER BY city, neighborhood, zip");
		$blogUrl = get_bloginfo("url");

		echo "<ul class=\"lme-areas\">\n";
		foreach ($areas as $area) {
			if (!empty($area->zip)) {
				$locationUrl = $area->zip;
				$areaName = $area->zip;
			} else {
				$cityUrl = urlencode(strtolower(str_replace(array("-", " "), array("_", "-"), $area->city)));
				$areaName = $area->city;
				if (!empty($area->neighborhood)) {
					$neighborhoodUrl = urlencode(strtolower(str_replace(array("-", " "), array("_", "-"), $area->neighborhood))) . "/";
					$areaName = $area->neighborhood . ", " . $areaName;
				}
				$areaName .= ", {$area->state}";	
				$locationUrl = "{$neighborhoodUrl}{$cityUrl}/" . strtolower($area->state);
				
			}
			$url = "{$blogUrl}/local/{$locationUrl}/";
			echo "<li><a href=\"{$url}\">{$areaName}</a></li>\n";
		}
		echo "</ul>";
		echo $args["after_widget"];
	}
	function update($new_instance, $old_instance) {
		return $new_instance;
	}
	function form($instance) {
		$instance = wp_parse_args($instance, array(
			"title" => "Area Info"
		));

		$title = htmlspecialchars($instance["title"]);
		$titleFieldId = $this->get_field_id("title");
		$titleFieldName = $this->get_field_name("title");

		echo <<<HTML
			<p>
				<label for="{$titleFieldId}">Widget title</label>
				<input id="{$titleFieldId}" name="{$titleFieldName}" value="{$title}" class="widefat" type="text" />
			</p>
HTML;
	}
}
?>