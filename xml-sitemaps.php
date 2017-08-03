<?php

add_action("sm_buildmap", array("LmeXmlSitemaps", "buildSitemap"));

class LmeXmlSitemaps {
	static function buildSitemap() {
		global $wpdb;
		
		$options = get_option(LME_OPTION_NAME);
		$blogUrl = get_bloginfo("url");

		$generatorObject = &GoogleSitemapGenerator::GetInstance();

		if ($generatorObject != null) {
			$areas = $wpdb->get_results("SELECT neighborhood, city, state, zip FROM " . LME_AREAS_TABLE . " ORDER BY state, city, neighborhood, zip");

			foreach ($areas as $area) {
				$url = $blogUrl . LmeModulesPageRewrite::getCanonicalLink($area->zip, $area->city, $area->neighborhood, $area->state);
				$generatorObject->AddUrl($url, time(), "daily", ".5");
			}
		}
	}
}

?>