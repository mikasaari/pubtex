function advanced()
{
	$('.more').hide();
	$('#common').show();
	$('#order').show();
	$('.border').css("border", "2px solid #c3ff00");
}

function changed(tag)
{
	if(tag.checked)
		document.searchform.search.value = tag.name + " " + document.searchform.search.value;
}

$(document).ready(function() 
{
	// Gallery image information
	var last_id;
	var banner_height = 100;
	var menu_height = 100;
	var footer_height = 250;
	var image_count = Math.round(($(window).height() - banner_height - menu_height - footer_height) / 150) * 5;
	var isLoading = false;

  // Scrolling
	$(window).scroll(function()
	{ 		
		if(($(window).scrollTop() > $(document).height()-$(window).height()-20 || $(window).height() >= $(document).height()) && !isLoading)
		{
			isLoading = true;
			$.get('/ajax-media?n=20&s='+last_id, function(data)
			{				
				// Parse the data coming from ajax call
				var obj = jQuery.parseJSON(data);

				if(obj.length > 0)
				{
					// Add each image one by one to the gallery
					for(x in obj)
					{
						var first = obj[x].file_hash.substring(0,2);
						var second = obj[x].file_hash.substring(2,4);
						var imgfile = '/data/images/'+first+'/'+second+'/'+obj[x].file_hash+'_thumb.png';

						$("#freesearch").append('<span class="mediacontainer"><span class="media"><span class="mediathumb"><a href="/media-details?mn='+obj[x].media_hash+'"><img src="'+imgfile+'"/></a></span></span></span>');
					}

					// Update last_id
					last_id = obj[obj.length-1].id;
				}
				isLoading = false;
			});
		}
	});

	// Tabs
	$('#tabs').tabs();
	
	// Searching
	var srchstr = "";

	$("#searchinput").append('<form class="searchform" name="searchform"><span class="label">Search: </span><input class="searchfield" id="target" name="search" type="text" value="" /><input class="searchbutton" type="button" value="Go" /></form>');

	$.get('/ajax-media?n='+image_count, function(data)
	{
		// Parse the data coming from ajax call
		var obj = jQuery.parseJSON(data);

		// Remove the gallery 
		$("#freesearch").remove();

		// Create new clean gallery
		$("#gallery").append('<div id="freesearch"><div style=\"clear: both;\"></div></div>');

		// Add each image one by one to the gallery
		for(x in obj)
		{
			var first = obj[x].file_hash.substring(0,2);
			var second = obj[x].file_hash.substring(2,4);
			var imgfile = '/data/images/'+first+'/'+second+'/'+obj[x].file_hash+'_thumb.png';

			$("#freesearch").append('<span class="mediacontainer"><span class="media"><span class="mediathumb"><a href="/media-details?mn='+obj[x].media_hash+'"><img src="'+imgfile+'"/></a></span></span></span>');
		}

		// Update last_id
		last_id = obj[obj.length-1].id;
	});

	$('#target').keyup(
	function(event) 
	{
		if (event.which == 13) 
		{
			event.preventDefault();
 		}  

		if(event.which == 32)
		{
			var tmp = document.searchform.search.value;
			var ta = new Array();
			var ns = "/ajax-media?";

			ta = tmp.split(' ');
			for(x in ta)
			{
				ns = ns + ta[x];
				if(x < ta.length-1)
					ns = ns + "&";
			}

			searchGallery(ns);
		}

		else if(event.which == 8)
		{
			var tmp = document.searchform.search.value;
			var ta = new Array();
			var ns = "/ajax-media?";

			ta = tmp.split(' ');
			last = ta.length - 1;
			if(last < 0)
				last = 0;

			for(x=0;x<last;x=x+1)
			{
				ns = ns + ta[x];
				if(x < ta.length-1)
					ns = ns + "&";
			}
			
			if(last > 0)	
			{
				searchGallery(ns);
			}
			else
			{
				ns = ns + 'n='+image_count;
				searchGallery(ns);
			}
		}

		else if(document.searchform.search.value != srchstr)
		{
			var tmp = document.searchform.search.value;
			var ta = new Array();
			var ns = "/ajax-media?";

			ta = tmp.split(' ');
			last = ta.length - 1;
			if(last < 0)
				last = 0;

			for(x=0;x<last;x=x+1)
			{
				ns = ns + ta[x] + "&";
			}
			
			ns = ns + 'ha='+ta[last];
			
			searchGallery(ns);

			srchstr = document.searchform.search.value;
		}		
	});

	$('#target').keydown(
	function(event) 
	{

	});	
});

