/** Tile Utility Functions **/

    
      var WS_TILE_MIN_WIDTH = 225;
      var WS_TILE_WIZARD_MIN_WIDTH = 250;
      var WS_TILE_LAYOUT_SWITCH_THRESHOLD = 475;

      var WS_TILE_H_DEFAULT_WIDTH = 700;
      var WS_TILE_H_DEFAULT_HEIGHT = 440;
      var WS_TILE_H_MIN_HEIGHT = 200;
      var WS_TILE_H_MAX_HEIGHT = 3000;
      var WS_TILE_V_MIN_HEIGHT = 300;
      var WS_TILE_V_MAX_HEIGHT = 3000;

      var WS_TILE_V_DEFAULT_LIST_LIMIT = 5;
      var WS_TILE_V_MIN_LIST_LIMIT = 5;
      var WS_TILE_MAX_LIST_LENGTH = 10;
      var WS_TILE_H_LINE_HEIGHT = 38;
      var WS_TILE_V_LINE_HEIGHT = 34;

      var WS_TILE_FOOTER_LINE_HEIGHT = 18;
      var WS_TILE_FOOTER_2_LINE_THRESHOLD = 350;

      var WS_TILE_FOOTER_HIDDEN = 100;
      var WS_TILE_FOOTER_ADDRESS = 101;
      
    function ws_tileOptions(defaultHeight, minHeight, maxHeight, numFooterLines) {
      this.defaultHeight = defaultHeight;
      this.minHeight = minHeight;
      this.maxHeight = maxHeight;
      this.footerLines = numFooterLines;
    }
    function ws_footerInfo(footerX, footerY, footerX2, inputX, inputY, inputW, numFooterLines) {
      this.footerX = footerX;
      this.footerY = footerY;
      this.footerX2 = footerX2;
      this.inputX = inputX;
      this.inputY = inputY;
      this.inputW = inputW;
      this.footerLines = numFooterLines;
    }
    function ws_rangeObj(minVal, maxVal) {
      this.minVal = minVal;
      this.maxVal = maxVal;
    }

    function ws_getTileOptions(
      width, layout, footerType, showCommute
    ){

      if (layout==null || layout=="auto")
        layout = ws_getDefaultLayout(width);

      //default to horizontal
      var defaultHeight = WS_TILE_H_DEFAULT_HEIGHT;
      var minHeight = WS_TILE_H_MIN_HEIGHT;
      var maxHeight = WS_TILE_H_MAX_HEIGHT;

      if ( layout == "vertical")
      {
        defaultHeight = WS_TILE_V_MIN_HEIGHT;
        minHeight = WS_TILE_V_MIN_HEIGHT;
        maxHeight = WS_TILE_V_MAX_HEIGHT;
      }
      //how much vertical space will the footer require?
      var numFooterLines = 1;
      if ( footerType == WS_TILE_FOOTER_HIDDEN )
        numFooterLines = 0;
      else if (width < WS_TILE_FOOTER_2_LINE_THRESHOLD)
        numFooterLines = 2;

      //if footer not on one line, need to correct height for that:
      if (layout == "horizontal" && numFooterLines == 1) {
        minHeight += 10;
      }

      return new ws_tileOptions(defaultHeight, minHeight, maxHeight, numFooterLines);
    }

    function ws_getFooterInfo(width, layout, height, footerType, actualTextWidth){

      //default to horizontal
      var footerX = 8;
      var footerX2 = width-4;

      if ( layout == "vertical")
      {
        footerX = 3;
        footerX2 = width-3;
      }

      //default to single line footer
      var footerY = height - WS_TILE_FOOTER_LINE_HEIGHT;
      var inputX = (actualTextWidth) ? actualTextWidth + 6 : 170;
      var inputY = 0;
      var numFooterLines = 1;

      if (width < WS_TILE_FOOTER_2_LINE_THRESHOLD)
      {
        footerY -= WS_TILE_FOOTER_LINE_HEIGHT;
        inputX = 0;
        inputY += WS_TILE_FOOTER_LINE_HEIGHT;
        numFooterLines = 2;
      }

      var inputW = footerX2 - footerX - 32 - inputX; //avail width - button - text-space

      return new ws_footerInfo(footerX, footerY, footerX2, inputX, inputY, inputW, numFooterLines);
    }

    function ws_getDefaultTileHeight(
      width, layout, footerType, showCommute
    ){
      var options = ws_getTileOptions(
        width, layout, footerType, showCommute
      );
      return options.defaultHeight;
    }

    function ws_getDefaultLayout(width, height){
      return ( Number(width) < WS_TILE_LAYOUT_SWITCH_THRESHOLD || (Number(width)<=500 && Number(width)<Number(height)) ) ? "vertical" : "horizontal";
    }
function ws_var_exists(paramName)
{
  return typeof( window[paramName] ) != "undefined" && window[paramName] != null;
}

function ws_default_if_not_set(param, defaultVal)
{
	return (typeof param != "undefined") ? param : defaultVal;
}

function ws_escape(value)
{
  if (value==='true' || value===true)
    return 't';
  if (value==='false' || value===false)
    return 'f';
  return encodeURIComponent(String(value).replace(/#/g, ""));
}

function ws_show_address(){
  var address = document.getElementById("ws-street").value;
  var url = "http://www.walkscore.com/score/" + ws_urlifyAddress(address) + "/?utm_source=" + encodeURI(ws_host_domain) + "&utm_campaign=tilefooter&utm_medium=address_search";
  window.open(url, "Walk_Score");
}
function ws_urlifyAddress(address) {
	if (typeof address == "undefined" || address=="")
		return "loc";

	address = ws_replaceAll( address, "-", ".dash." );
	address = ws_replaceAll( address, "#", ".num." );
	address = ws_replaceAll( address, "/", ".slash." );
	address = ws_replaceAll( address, "&", " and " );
	address = ws_replaceAll( address, ",", " " ); //change comma to space
	address = ws_replaceAll( address, "  ", "-" ); //doublespace
	address = ws_replaceAll( address, " ", "-" ); //singlespace
	return encodeURIComponent(address);
}
function ws_replaceAll (strOrig, strTarget, strSubString) {
	var intIndexOfMatch = strOrig.indexOf( strTarget );
	while (intIndexOfMatch != -1) {
		strOrig = strOrig.replace( strTarget, strSubString )
		intIndexOfMatch = strOrig.indexOf( strTarget );
	}
	return strOrig;
}


function ws_innerclean_elem(elem) {
	if (!elem || !elem.style)
  	return;
	elem.style.textAlign = "left";
	elem.style.textDecoration = "none";
	elem.style.padding = 0;
	elem.style.fontSizeAdjust = "none";
	elem.style.fontStretch = "normal";
	elem.style.fontStyle="normal";
	elem.style.fontVariant = "normal";
  elem.style.letterSpacing = "normal";
	elem.style.wordSpacing = "normal";
	elem.style.textTransform = "none";
	elem.style.verticalAlign = "baseline";
	elem.style.textIndent = 0;
	elem.style.textShadow = "none";
	elem.style.whiteSpace = "normal";
  elem.style.backgroundImage = "none";
  elem.style.backgroundColor = "transparent";
}
function ws_outerclean_elem(elem) {
	if (!elem || !elem.style)
  	return;
	elem.style.margin = 0;
	elem.style.outline = "none";
}

function ws_fullclean_elem(elem){
	if (!elem || !elem.style)
  	return;
	ws_outerclean_elem(elem);
	ws_innerclean_elem(elem);
	elem.style.border = "none";
}

function ws_show_tile(width, layout, height, hideFooter, params)
{
  //do some css and dynamic layout:
  var tile = document.getElementById(ws_div_id);
  if (tile) {
    tile.style.position = "relative";
    tile.style.textAlign = "left";
	}

  //default to 1-line footer -- set iframe height to accomodate
  var iframeHeight = ws_height - WS_TILE_FOOTER_LINE_HEIGHT;

  if (hideFooter)
  {
    iframeHeight += WS_TILE_FOOTER_LINE_HEIGHT;
    var footer = document.getElementById("ws-footer");
    if (footer)
      footer.style.display = "none";
  }
  else {
    var footText = document.getElementById("ws-foottext");
    var link = document.getElementById("ws-a");
    if (link) {
      ws_fullclean_elem(link);
      link.style.fontWeight = "bold";
      //footer text different in legacy installs, so need to adapt here based on actual width
      if (footText) {
        ws_fullclean_elem(footText);
        var actualTextWidth = footText.offsetWidth;
      }
      else {
        var actualTextWidth = link.offsetWidth;
      }
      var footerInfo = ws_getFooterInfo(width, layout, height, WS_TILE_FOOTER_ADDRESS, actualTextWidth);
    }
    else{ //fallback case -- don't choke if we don't find the element (but shouldn't ever happen)
	    var footerInfo = ws_getFooterInfo(width, layout, height, WS_TILE_FOOTER_ADDRESS);
    }


    function isHidden(el) {
			if (!window.getComputedStyle) //IE8, skip this check
				return false;
      var style = window.getComputedStyle(el);
      return (style.display=='none' || el.offsetHeight==0 || style.visibility=='hidden');
    }

    if (footerInfo.footerLines == 2)
      iframeHeight -= WS_TILE_FOOTER_LINE_HEIGHT;

    var footer = document.getElementById("ws-footer");
    if (footer) {
      ws_fullclean_elem(footer);
      footer.style.position = "absolute";
      footer.style.display = "block";
      footer.style.left = footerInfo.footerX+"px";
      footer.style.top = footerInfo.footerY+"px";
      footer.style.width = (footerInfo.footerX2 - footerInfo.footerX) +"px";
      footer.style.font = "11px/9px Verdana, Arial, Helvetica, sans-serif";
      var link = document.getElementById("ws-a");
      var footText = document.getElementById("ws-foottext");
			var mainDiv = document.getElementById("ws-walkscore-tile");
			var onlyFooterHidden = isHidden(link) && !isHidden(mainDiv);
      if (link && !onlyFooterHidden && link.href && link.href == "https://www.redfin.com/how-walk-score-works"){
        if (footText && link.innerHTML == "Your Home")
          params+="&e=2";
        else
          params+="&e=1.5";
      }
    }

    var form = document.getElementById("ws-form");
    if (form) {
      ws_fullclean_elem(form);
      form.onsubmit=function(){ ws_show_address(); return false; }
		}

    var input = document.getElementById("ws-street");
    if (input) {
      ws_innerclean_elem(input);
      ws_outerclean_elem(input);
      input.style.position = "absolute";
      input.style.left = footerInfo.inputX + "px";
      input.style.top = footerInfo.inputY + "px";
      input.style.width = footerInfo.inputW + "px";
      input.style.height = "13px";
      input.style.border = "1px solid #aaa";
      input.style.color = "#000";
      input.style.backgroundColor = "#fff";
      input.style.font = "11px/9px Verdana, Arial, Helvetica, sans-serif";
      input.value='Enter an address';
      input.onfocus=function() { if (this.value=='Enter an address') this.value=''; }
		}

    var button = document.getElementById("ws-go");
    if (button) {
    	ws_fullclean_elem(button);
      button.style.position = "absolute";
      button.style.right = "0px";
      button.style.top = footerInfo.inputY + "px";
		}
  }

  var tile = document.getElementById(ws_div_id);
	if (tile) {
    ws_innerclean_elem(tile);
    tile.style.height = height+"px";
    params += "&h=" + iframeHeight;
    params += "&fh=" + (height-iframeHeight);

    var iframe = document.createElement('iframe');
    ws_fullclean_elem(iframe);
    iframe.marginHeight = "0";
    iframe.marginWidth = "0";
    iframe.height=iframeHeight + "px";
    iframe.frameBorder="0";
    iframe.scrolling = "no";
    iframe.style.border = 0;
    iframe.allowTransparency = true;
    tile.appendChild(iframe);

    if ((new RegExp("^[0-9]+%$")).test(width)) {
      tile.style.width = width
      iframe.width = '100%';
      params += "&w=" + iframe.clientWidth;
    }
    else {
      tile.style.width = width+"px";
      iframe.width=width + "px";
      params += "&w=" + ws_escape(ws_width);
    }

          iframe.src = "http://www.walkscore.com/serve-walkscore-tile.php?" + params;
    
  }
}

//inline code to prepare to display the tile

var ws_div_id = ws_default_if_not_set( ws_div_id, 'ws-walkscore-tile' );

//build param string using any variables that were defined on hosting page
var ws_params = "";

//wsid
var ws_wsid = ws_default_if_not_set( ws_wsid, "none");
ws_params += "wsid=" + ws_escape(ws_wsid);

//address
if (ws_var_exists("ws_address"))
  ws_params += "&s=" + ws_urlifyAddress(ws_address);

//lat lng (can be in addition to address)
if (ws_var_exists("ws_lat") && ws_var_exists("ws_lon"))
  ws_params += "&lat=" + ws_escape(ws_lat) + "&lng=" + ws_escape(ws_lon);

// Polygon (if set)
if (ws_var_exists("ws_poly"))
  ws_params += "&polygon=" + ws_escape(ws_poly);

//width
var ws_width = String(ws_default_if_not_set( ws_width, "500")).replace(/px/g, "");

//layout
var ws_layout = ws_default_if_not_set( ws_layout, ws_getDefaultLayout(ws_width) );
ws_params += "&o=" + (ws_layout=='none'?'none': (ws_layout=='vertical'?'v':'h'));

//transit -- a pro param, but effects layout so must do early

if (ws_var_exists("ws_transit_score"))
ws_params += "&ts=" + ws_escape(ws_transit_score);

if (ws_var_exists("ws_public_transit"))
ws_params += "&pt=" + ws_escape(ws_public_transit);

var ws_commute = ws_default_if_not_set( ws_commute, false );
ws_params += "&c=" + ws_escape(ws_commute);

if (ws_var_exists("ws_show_reviews"))
ws_params += "&sr=" + ws_escape(ws_show_reviews);

if (ws_var_exists("ws_no_link_score_description"))
ws_params += "&nld=" + ws_escape(ws_no_link_score_description);

//height
var ws_height = ws_default_if_not_set( ws_height, ws_getDefaultTileHeight( Number(ws_width), ws_layout, WS_TILE_FOOTER_ADDRESS) );
ws_height = Number(ws_height);


//distance units
if (ws_var_exists("ws_distance_units"))
	ws_params += "&units=" + ws_escape(ws_distance_units);

if (ws_var_exists("ws_hide_scores_below"))
	ws_params += "&hide_scores_below=" + ws_escape(ws_hide_scores_below);

if (!ws_var_exists("ws_hide_footer"))
	ws_hide_footer = false;

if (ws_var_exists("ws_no_head"))
	ws_params += '&no_head=true';

//color parameters for iframe
if (ws_var_exists("ws_background_color")) ws_params += "&bg_col=" + ws_escape(ws_background_color);
if (ws_var_exists("ws_category_color")) ws_params += "&category_col=" + ws_escape(ws_category_color);
if (ws_var_exists("ws_result_color")) ws_params += "&result_col=" + ws_escape(ws_result_color);
if (ws_var_exists("ws_map_frame_color")) ws_params += "&map_frame_col=" + ws_escape(ws_map_frame_color);

//more pro params
if (ws_var_exists("ws_industry_type")) ws_params += "&industry=" + ws_escape(ws_industry_type);
if (ws_var_exists("ws_map_icon_type")) ws_params += "&icon=" + ws_escape(ws_map_icon_type);
if (ws_var_exists("ws_custom_pin")) ws_params += "&custom_pin=" + ws_escape(ws_custom_pin);
if (ws_var_exists("ws_default_view")) ws_params += "&default_view=" + ws_escape(ws_default_view);
if (ws_var_exists("ws_commute_address")) ws_params += "&commute_address=" + ws_escape(ws_commute_address);
if (ws_var_exists("ws_commute_display_address")) ws_params += "&commute_display_address=" + ws_escape(ws_commute_display_address);
if (ws_var_exists("ws_fixed_commute")) ws_params += "&fixed_commute=" + ws_escape(ws_fixed_commute);
if (ws_var_exists("ws_map_provider")) ws_params += "&map_provider=" + ws_escape(ws_map_provider);
if (ws_var_exists("ws_map_zoom")) ws_params += "&map_zoom=" + ws_escape(ws_map_zoom);
if (ws_var_exists("ws_map_modules")) ws_params += "&mm=" + ws_escape(ws_map_modules);
if (ws_var_exists("ws_base_map")) ws_params += "&base_map=" + ws_escape(ws_base_map);
if (ws_var_exists("ws_no_link_logo")) ws_params += "&unlink_logo=" + ws_escape(ws_no_link_logo);
if (ws_var_exists("ws_hide_bigger_map")) ws_params += "&hide_bigger_map=" + ws_escape(ws_hide_bigger_map);
if (ws_var_exists("ws_disable_street_view")) ws_params += "&disable_street_view=" + ws_escape(ws_disable_street_view);
if (ws_var_exists("ws_no_link_info_bubbles")) ws_params += "&nlb=" + ws_escape(ws_no_link_info_bubbles);

//source param for urls
var ws_host_domain = location.hostname;
try{
	ws_host_domain = ws_host_domain.substr( 1 + ws_host_domain.lastIndexOf(".", ws_host_domain.length-5) );
}catch(err){ }

var ws_link = document.getElementById("ws-a");
if (ws_link && ws_link.href.indexOf('how-walk-score-works')==-1){
	ws_link.href = ws_link.href + "/?utm_source=" + encodeURI(ws_host_domain) + "&utm_campaign=tilefooter&utm_medium=footer_link";
}


//write the iframe and display the tile:
ws_show_tile(ws_width, ws_layout, ws_height, ws_hide_footer, ws_params);



//footer color customization:

if (ws_link && ws_var_exists("ws_score_color")) {
  ws_link.style.color = "#" + ws_escape(ws_score_color);
}

if (ws_var_exists("ws_background_color")) {
  var ws_tile = document.getElementById(ws_div_id);
  if (ws_tile) ws_tile.style.backgroundColor = "#" + ws_escape(ws_background_color);
}

var ws_input = document.getElementById("ws-street");
if (ws_input) {
  if (ws_var_exists("ws_address_box_frame_color"))
    ws_input.style.borderColor = "#" + ws_escape(ws_address_box_frame_color);
  if (ws_var_exists("ws_address_box_bg_color"))
    ws_input.style.backgroundColor = "#" + ws_escape(ws_address_box_bg_color);
  if (!ws_var_exists("ws_address_box_text_color"))
  	ws_address_box_text_color = "757575";
  ws_input.style.color = "#" + ws_escape(ws_address_box_text_color);
}
