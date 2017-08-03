var lmeadmin = lmeadmin || {};

lmeadmin.newAreaId = 0;
lmeadmin.processAreaDescriptionNode = function() {
	var neighborhoodNode = jQuery(this).find('.lme-areas-neighborhood');
	var clickTarget = jQuery(document.createElement('div'));
	
	clickTarget
		.click(lmeadmin.loadNeighborhoodsOnClick)
		.width(neighborhoodNode.outerWidth())
		.height(neighborhoodNode.outerHeight())
		.css({ 'position': 'absolute', 'left': neighborhoodNode.get(0).offsetLeft + 'px', 'top': neighborhoodNode.get(0).offsetTop + 'px' });
	neighborhoodNode.get(0).parentNode.appendChild(clickTarget.get(0));
	
	jQuery(this).find('.lme-areas-remove').click(lmeadmin.removeAreaDescriptionNode);
	jQuery(this).find('.lme-areas-city, .lme-areas-state').blur(lmeadmin.loadNeighborhoodsOnBlur);
}
lmeadmin.addAreaDescriptionNode = function() {
	var nodeToCopy = document.getElementById('lme-areas-new');
	var newNode = nodeToCopy.cloneNode(true);
	
	newNode.id = '';
	newNode.innerHTML = newNode.innerHTML.replace(/lme\-areas\[new\]/g, 'lme-areas[new' + lmeadmin.newAreaId++ + ']');
	lmeadmin.processAreaDescriptionNode.call(jQuery(newNode));
	
	nodeToCopy.parentNode.insertBefore(newNode, nodeToCopy);
}
lmeadmin.removeAreaDescriptionNode = function(event) {
	jQuery(event.target).closest('li').detach();
}
lmeadmin.loadNeighborhoodsOnBlur = function(event) {
	var parentListEl = jQuery(event.target).closest('li');
	var city = parentListEl.find('.lme-areas-city').val();
	var state = parentListEl.find('.lme-areas-state').val();
	var neighborhoodDropDown = parentListEl.find('.lme-areas-neighborhood');
	
	if (!city || !state)
		return;
	lmeadmin.loadNeighborhoods(neighborhoodDropDown, city, state);
}
lmeadmin.loadNeighborhoodsOnClick = function(event) {
	var parentListEl = jQuery(event.target).closest('li');
	var neighborhoodDropDown = jQuery(event.target).siblings('.lme-areas-neighborhood');
	var city = parentListEl.find('.lme-areas-city').val();
	var state = parentListEl.find('.lme-areas-state').val();

	lmeadmin.loadNeighborhoods(neighborhoodDropDown, city, state);
	event.target.parentNode.removeChild(event.target);
}
lmeadmin.loadNeighborhoods = function(neighborhoodDropDown, city, state) {
	if (neighborhoodDropDown.attr('data-city') == city && neighborhoodDropDown.attr('data-state') == state)
		return;
	
	neighborhoodDropDown
		.attr('data-city', city)
		.attr('data-state', state)
		.attr('disabled', 'disabled');
	
	var apiParams = {};
	var api = 'GetRegionChildren';
	var callback = function(response) { lmeadmin.loadNeighborhoodsCallback(neighborhoodDropDown, response); };
	
	apiParams['zws-id'] = jQuery('#local-market-explorer\\[api-keys\\]\\[zillow\\]').val();
	apiParams['city'] = city;
	apiParams['state'] = state;
	apiParams['childtype'] = 'neighborhood';
	
	neighborhoodDropDown.html('<option value="">Loading, please wait...</option>');
	jQuery.get(ajaxurl, {
		'action': 'lme-proxyZillowApiRequest',
		'api': 'GetRegionChildren',
		'apiParams': apiParams
	}, callback);
}
lmeadmin.loadNeighborhoodsCallback = function(neighborhoodDropDown, response) {
	var apiData = eval('(' + response + ')'); // we trust zillow data

	if (apiData.message.code == '2') {
		neighborhoodDropDown
			.html('<option value="">(Invalid Zillow API key)</option>')
			.attr('data-city', '')
			.attr('data-state', '');
		return;
	}
	if (!apiData.response || !apiData.response.list || !apiData.response.list.region) {
		neighborhoodDropDown.html('<option value="">(no neighborhoods found)</option>');
		return;
	}
	
	var neighborhoods = apiData.response.list.region;
	var neighborhoodSelections = [];
	
	neighborhoods.sort(function(n1, n2) {
		return n1.name > n2.name ? -1 : 1;
	});
	neighborhoodSelections.push('<option value=""> - None - </option>');
	for (var i = neighborhoods.length - 1; i--;)
		neighborhoodSelections.push('<option value="' + neighborhoods[i].name + '">' + neighborhoods[i].name + '</option>');
	
	neighborhoodDropDown
		.html(neighborhoodSelections.join(''))
		.removeAttr('disabled');
}
lmeadmin.preSaveOptions = function(event) {
	var orderedModules = [];
	var formSerializedModules = document.createElement('input');
	
	jQuery('#lme-modules-to-display li input:checked').each(function() {
		orderedModules.push(this.name.match(/local\-market\-explorer\[global-modules\]\[(.+)\]/)[1]);
	});
	
	formSerializedModules.type = 'hidden';
	formSerializedModules.name = 'local-market-explorer[global-module-orders]';
	formSerializedModules.value = orderedModules.join(',');
	jQuery(event.target).closest('form').append(formSerializedModules);
}
lmeadmin.changeLink = function(event) {
	var parentListEl = jQuery(event.target).closest('li');
	var city = parentListEl.find('.lme-areas-city').val();
	var state = parentListEl.find('.lme-areas-state').val();
	var neighborhood = parentListEl.find('.lme-areas-neighborhood').val();
	var zip = parentListEl.find('.lme-areas-zip').val();
	var url = lmeadmin.blogUrl + '/local/';
	
	if (zip) {
		url += zip + '/';
	} else if (neighborhood && city && state) {
		url += neighborhood.toLowerCase().replace(/ /g, '-') + '/' + city.toLowerCase().replace(/ /g, '-') + '/' + state.toLowerCase() + '/';
	} else if (city && state) {
		url += city.toLowerCase().replace(/ /g, '-') + '/' + state.toLowerCase() + '/';
	}
	
	parentListEl.find('.lme-link').html('<a href="' + url + '" target="_blank">' + url + '</a>');
}

// gotta do this because events don't fire when elements are disabled
jQuery('#lme-areas-descriptions li.lme-area[id!="lme-areas-new"]').each(lmeadmin.processAreaDescriptionNode);
jQuery('#lme-areas-add').click(lmeadmin.addAreaDescriptionNode);
jQuery('#lme-save-options').click(lmeadmin.preSaveOptions);
if (jQuery('#lme-modules-to-display').length) // yeah yeah, bad practice...
	jQuery('#lme-modules-to-display').sortable({ axis: 'y' });
jQuery('#lme-areas-descriptions .lme-location-input').change(lmeadmin.changeLink).change();

if (YUI) {
	YUI().use('tabview', function(Y) {
		var tabview = new Y.TabView({ srcNode: '#lme-options' });
		tabview.render();
	});
}