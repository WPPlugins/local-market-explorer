
var lmeModule = (function() {
	var nodeEditing;
	var returnObj;
	var $ = jQuery;
	
	returnObj = {
		init: function() {
			var startNode = tinyMCEPopup.editor.selection.getStart();
			var nodeTextContent = startNode.textContent || startNode.innerText; 
			var neighborhood, city, state, zip;
			
			if (/^\[lme-module /.test(nodeTextContent) && startNode.tagName == 'P') {
				nodeEditing = startNode;
				tinyMCEPopup.editor.execCommand('mceSelectNode', false, nodeEditing);
				
				neighborhood = /^[^\]]+ neighborhood=['"]([^"']+)/.exec(nodeTextContent);
				city = /^[^\]]+ city=['"]([^"']+)/.exec(nodeTextContent);
				state = /^[^\]]+ state=['"]([^"']+)/.exec(nodeTextContent);
				zip = /^[^\]]+ zip=['"]([^"']+)/.exec(nodeTextContent);
				
				$('#module').val(/^[^\]]+ module=['"]([^"']+)/.exec(nodeTextContent)[1]);
				if (neighborhood)
					$('#neighborhood').val(neighborhood[1]);
				if (city)
					$('#city').val(city[1]);
				if (state)
					$('#state').val(state[1]);
				if (zip)
					$('#zip').val(zip[1]);
			}
			$('#city, #state').blur(returnObj.loadNeighborhoods).blur();
		},
		loadNeighborhoods: function () {
			var city = $('#city').val();
			var state = $('#state').val();
			
			if (city && state)
				lmeadmin.loadNeighborhoods($('#neighborhood'), city, state);
		},
		insert: function() {
			var shortcode = '<p>[lme-module module="' + $('#module').val() + '"';
			var neighborhood = $('#neighborhood').val();
			var city = $('#city').val();
			var state = $('#state').val();
			var zip = $('#zip').val();
			
			if (neighborhood)
				shortcode += ' neighborhood="' + neighborhood + '"';
			if (city)
				shortcode += ' city="' + city + '"';
			if (state)
				shortcode += ' state="' + state + '"';
			if (zip)
				shortcode += ' zip="' + zip + '"';
			shortcode += ']</p>';
			
			tinyMCEPopup.editor.execCommand(nodeEditing ? 'mceReplaceContent' : 'mceInsertContent', false, shortcode);
			tinyMCEPopup.close();
		}
	};
	
	return returnObj;
})();

tinyMCEPopup.onInit.add(lmeModule.init, lmeModule);
