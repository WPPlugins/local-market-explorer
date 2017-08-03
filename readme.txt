=== Local Market Explorer ===
Contributors: amattie, jmabe, dustin999
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=10178626
Feedback page link: http://localmarketexplorer.uservoice.com/
Tags: zillow, walk score, streetadvisor, real estate, local, city data, neighborhood data
Requires at least: 3.0
Tested up to: 4.3.1
Stable tag: 4.5.2

This plugin allows WordPress to load data from a number of neighborhood-related APIs to be presented on a single page or within
your own pages / posts.

== Description ==

This plugin allows WordPress to load data from a number of neighborhood-related APIs to be presented on a single page or within
your own pages / posts. 

Info is recovered by calling the following APIs: 

* Real Estate Market Stats (via [Zillow](http://www.zillow.com/webservice/))
* StreetAdvisor (via [StreetAdvisor](http://www.streetadvisor.com))
* Walk Score (via [Walk Score](http://www.walkscore.com))
* Google Maps (http://maps.google.com./maps/api) 

To use the modules on the pre-generated Local Market Explorer "virtual pages," you don't need to do anything other than link to
or visit the specially-crafted URLs that Local Market Explorer intercepts. The formats of these URLs look like this:

* &lt;http://www.yoursite.com/local/_city_/_state_/&gt;
* &lt;http://www.yoursite.com/local/_neighborhood_/_city_/_state_/&gt;
* &lt;http://www.yoursite.com/local/_zip_/&gt;

For example, to load the Local Market Explorer for Seattle, WA, you'd simply need to point your browser to
&lt;http://www.yoursite.com/local/seattle/wa/&gt;. If you have spaces in your city name, you can use hyphens for the spaces in
the URL, like so: &lt;http://www.yoursite.com/local/laguna-beach/ca/&gt;. You DO NOT need to pre-initialize these
URLs to work; simply having Local Market Explorer installed is enough to get any of these URLs to load the appropriate data.

To use the modules with your own pages and posts, you only need to insert the Local Market Explorer shortcode text via the
page / post editor. There's a small map icon in the toolbar editor that can help you do this.

More information and help can be found in the "Help" tab in the admin once the plugin is installed.

This plugin is open-source donationware. We’re willing to accept and integrate well-written patches into the code, but the
continued development of the plugin (new features, bug fixes, etc) by the plugin author is funded by either donations or
companies willing to pay a fee to have their data integrated. You can thank Zillow, and [StreetAdvisor](http://www.streetadvisor.com/) for funding the
vast majority of the development thus far. This plugin is currently funded by [StreetAdvisor](http://www.streetadvisor.com/), and being developed by [Caffeine Interactive](http://caffeineinteractive.com/).

If you'd like to contribute a feature suggestion or need to document a bug, please use the
[User Voice forum](http://localmarketexplorer.uservoice.com/) set up specifically for that purpose. With User Voice, each user
gets a fixed number of votes that they can cast for any particular bug or feature. The higher the number of votes for an item,
the higher the priority will be for that item as development commences on the plugin itself.

== Installation ==

1. Go to your WordPress admin area, then go to the "Plugins" area, then go to "Add New".
2. Search for "local market explorer" (sans quotes) in the plugin search box.
3. Click "Install" on the right, then click "Install" at the top-right in the window that comes up.
4. Go to the "Settings" -> "Local Market Explorer" area.
5. Visit each of the API key links and get your API keys. After you put in each API key, the data will load for the corresponding modules.

== Changelog ==

= 4.5.2 =
* Removal of Education.com module. Support was discontinued.

= 4.5.1 =
* Updates for about text 

= 4.5 =
* Fixes for tiny Editor in posts and Pages 

= 4.4 =
* Update for StreetAdvisor module 

= 4.3 =
* Added StreetAdvisor module

= 4.2 =
* Optimization of the plugin

= 4.1.1 =
* Fix some additional CSS issues.

= 4.1.1 =
* Fix some additional CSS issues.

= 4.1 =
* Reported issue was fixed. Also, some CSS issues in the admin were fixed.

= 4.0 =
* Major update to remove bugs and patch security issues

= 3.2.6 =
* Various bug and security fixes

= 3.2.5 =
* Fixes for NileGuide module

= 3.2.4 =
* Fixed a few XSS security issues with colleges, neighborhoods, and Walk Score modules
* Added links to API key locations in admin

= 3.2.3 =
* Make all branding links open in new windows
* Fix schools typo

= 3.2.2 =
* Fixed NileGuide data for upcoming API change
* Ensured compatibility with WordPress 3.3

= 3.2.1 =
* Fixed styles for shortcodes

= 3.2 =
* Added Realtors module using data from [Homethinking](http://www.homethinking.com)
* Small styling fixes
* Fixes for Zillow market activity and market stats modules

= 3.1.2 =
* Removing some debugging code that was causing problems during install
* Cleaned up option upgrading code for v1.x and v2.x to ensure that everything transfers correctly during the upgrade
* Fixed bug with college module not being initialized on install and empty / blank module being initialized instead

= 3.1 =
* Fixed possible issues with some of the modules not working when something other than a zip code was used
* Fixed issue where shortcode insertion tool was occasionally not working
* Added colleges module using data from [MatchCollege](http://www.matchcollege.com)

= 3.0.1 =
* Fixed documentation error with modules
* Fixed some pedantic PHP warnings when debug mode was turned on
* Fixed neighborhoods, NileGuide, Walk Score, Teach Street, and about area shortcodes
* Added neighborhoods and NileGuide modules to the shortcode insertion tool
* Added workarounds for http://core.trac.wordpress.org/ticket/9346

= 3.0 =
* Performance has been significantly increased by making all of the external data requests in parallel instead of in series.
* The modules can now be used on individual pages and posts instead of only on the Local Market Explorer virtual pages.
* The HTML markup and CSS styling has been significantly pruned so that skinning is easier and so that the default styles work better with many more themes.
* The admin UI has been significantly enhanced to make it easier to use.
* A neighborhood module has been added on city pages that links to all the neighborhoods within that city.
* Support for canonical link tags has been added so that search engines will better index the true URL and won't see duplicate content within your domain.
* This version works way better with some of the more obscure WordPress installs as well as with WordPress 3.x.
* Support for XML sitemaps has been added via the Google XML Sitemaps plugin.
* Lots of bugs have been fixed relating to data not displaying when it should and blank data displaying when it shouldn't.
* More charts and data have been added to a few of the modules.
* Support for dsIDXpress has been added to the local pages.

= 2.1 =
* Added option to link to an IDX page per area
* Fixed issues with template selection so that the plugin will more reliably select the page template (not the post template)
* Fixed bug where empty areas were getting saved

= 2.0.2 =
* Fixed bug where beds / baths were transposed in recent sales activity module

= 2.0.1 =
* Fixed issue with the widget not showing up in admin / not showing areas
* Temporarily removed some of the market stats for zip codes (pending a bug fix by Zillow)

= 2.0 =
* Cached some API requests (where allowed) to make module load faster
* Added support for displaying data for neighborhoods and zips
* Added ability to reorder modules
* Added ability to put HTML (YouTube videos, etc) into the area descriptions
* Usability improvements in the admin
* Added new market data stats (provided by Zillow)
* Added ability to turn off Flickr, Schools, and Market stats panel

= 1.1 =
* Added ability to pull in TeachStreet data

= 1.0.4 =
* Fixed typo since 1.0.2 that caused Thesis theme not to work properly

= 1.0.3 =
* Fixed issue with Zillow API where an undesired city could be loaded if the city name had a space in it
* Changed "What's a Zestimate" to "What's a Zindex" in the disclaimer

= 1.0.2 =
* Fixed another bug with two links in Education.com module (specifically, cities with spaces and anything in California, Colorado, or Arizona)
* Added handling for Thesis theme and any other theme that has a file called "custom_template.php" instead of "page.php" -- new fallback is to "post.php" if neither of those exist
* Added handling for what seems to be a Walk Score tile duplication issue where the tile is getting placed on the page twice (due to their script?)

= 1.0.1 =
* Updated installation instructions
* Fixed bug with links in Education.com module

== Frequently Asked Questions ==

= Can I customize the styling and display format? =

Yes. All of the styles are controlled via an external CSS stylesheet named client.css (located in the 'css' folder). You can
easily override any of the styles in there. Be aware, however, that the default styles were created to be compliant with all of
the branding requirements of the different APIs. It's possible that overriding any of the styles could put you out of compliance
with the API provider(s).

= How do I draw attention to the pages for my target markets? =

There are a number of pre-built images you can use to use as calls to action on your sidebar or anywhere else. The images are
available in the following colors: black, blue, green, orange, and red. The images can be found in the following folder:
/wp-content/plugins/local-market-explorer/images/badges

= How do I add a sidebar module listing my target markets? =

From your WordPress admin interface, simply navigate to Appearance -> Widgets, then you can drag + drop the "Local Market
Explorer Areas" from the "Available Widgets" to a sidebar on the right (such as "Sidebar 1"). Once the widget is placed, you can
click the down-arrow on the newly placed widget to customize the title.

= The Market Activity module is not getting populated with recent sales data - why? =

The module is driven by a private API call that needs permissions to be granted to a specific Zillow API key.
To request access to this API, simply fill out the API upgrade form located
[here](http://www.zillow.com/webservice/APIUpgradeRequest.htm) and select "Local Market Explorer Wordpress Plugin" in the API
request type field. Once the request is processed, your market activity module should populate automatically with recent sales
data.

== Screenshots ==

1. Real Estate Market Stats module
2. Real Estate Market Activity module
4. Walk Score module