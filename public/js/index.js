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
	else
	{
		
	}
}

function closeDetails()
{
	$('#details').hide();
	$('.dim').hide();

   if (window.removeEventListener) 
		{
       window.removeEventListener('DOMMouseScroll', wheel, false);
   }
   window.onmousewheel = document.onmousewheel = null;  

	return false;
}

function details(hash)
{
	if(hash != "")
	{		
		$.get("/ajax-media?d="+hash, function(data)
		{
			// Parse the data coming from ajax call
			var obj = jQuery.parseJSON(data);
			var files = obj['files'];

			// Clear the thumbnail list
			$(".ad-thumb-list").empty();

			// Add new thumbnails to the list
			for(var x in files)
			{
				var first = x.substring(0,2);
				var second = x.substring(2,4);
				var imgfile = '/data/images/'+first+'/'+second+'/'+x+'_resize.png';
				var tmbfile = '/data/images/'+first+'/'+second+'/'+x+'_thumb.png';		
				$(".ad-thumb-list").append('<li><a href="'+imgfile+'"><img src="'+tmbfile+'"/></a></li>');
			}

			function doTricks()
			{
				// Clear the token input
				$("#pubtex-tags").tokenInput("clear");

				// Add tags to the created tokenInput
				var tags = obj['tags'];
	
				for(var x in tags)
				{
					$("#pubtex-tags").tokenInput("add", {id: x, name: tags[x]});
				}

				var sfile = $('.ad-image').find('img').attr('src');
				var tfile =  sfile.split('/');
				var file = tfile[tfile.length-1].split("_")[0];
				var nfiles = files[file];
				for(var x in nfiles)
				{
					$("#pubtex-tags").tokenInput("add", {id: x, name: nfiles[x]});
				}
				
				desc = $('<p class="img-desc">Name: '+file+'<br>Creator:'+obj['user']+'<br>Create Date:'+obj['created']+'<br>Description:'+obj['description']+'<br>');
				$('.img-desc').remove();
				$('.ad-image-wrapper').append(desc);				 
			}
			
			// Gallery code
			var galleries = $('.ad-gallery').adGallery({
				animate_first_image: true,
				callbacks: 
				{
					afterImageVisible: doTricks,
					init: function() 
					{
						this.preloadAll();
					}
				},
				slideshow: 
				{
					enable: false
				}
			});

			$('#switch-effect').change(function() 
			{
				galleries[0].settings.effect = $(this).val();
				return false;
			});

			$('#toggle-slideshow').click(function() 
			{
				galleries[0].slideshow.toggle();
				return false;
			});

			$('#toggle-description').click(function() 
			{
				if(!galleries[0].settings.description_wrapper) 
				{
					galleries[0].settings.description_wrapper = $('#descriptions');
				} 
				else 
				{
					galleries[0].settings.description_wrapper = false;
				}
				return false;
			}); 

			$('.ad-image-wrapper').hover(function()
			{
				$('.img-desc').show();
			}, function()
			{
				$('.img-desc').hide();
			});			
		});

		// Try blocking the scrolling
	  if (window.addEventListener) 
		{
	     window.addEventListener('DOMMouseScroll', wheel, false);
	  }
	  window.onmousewheel = document.onmousewheel = wheel;

		// Show details
		$('.dim').show();
		$('#details').show();
	}

	return false;
}

function searchGallery(ns) {
	$.get(ns, function(data)
	{
		var obj = jQuery.parseJSON(data);
		$("#freesearch").remove();

		$("#gallery").append('<div id="freesearch"><div style=\"clear: both;\"></div><br></div>');

		for(x in obj)
		{
			var first = obj[x].file_hash.substring(0,2);
			var second = obj[x].file_hash.substring(2,4);
			var imgfile = '/data/images/'+first+'/'+second+'/'+obj[x].file_hash+'_thumb.png';

			$("#freesearch").append('<span class="mediacontainer"><span class="media"><span class="mediathumb"><a href="#" onclick="details(\''+obj[x].media_hash+'\'); return false;"><img src="'+imgfile+'"/></a></span></span></span>');
		}
	});
}

function wheel(e)
{
	e.preventDefault();
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

						$("#freesearch").append('<span class="mediacontainer"><span class="media"><span class="mediathumb"><a href="#" onclick="details(\''+obj[x].media_hash+'\'); return false;"><img src="'+imgfile+'"/></a></span></span></span>');
					}

					// Update last_id
					last_id = obj[obj.length-1].id;
				}
				isLoading = false;
			});
		}
	});

	// Create Token Input for media details div (hidden)
	$("#pubtex-tags").tokenInput("http://pubtexi.local/ajax-tags", 
	{
		theme: "facebook",
		searchDelay: 100,
		hintText: false,
		preventDuplicates: true,
		deleteText: '',
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

			$("#freesearch").append('<span class="mediacontainer"><span class="media"><span class="mediathumb"><a href="#" onclick="details(\''+obj[x].media_hash+'\');return false;"><img src="'+imgfile+'"/></a></span></span></span>');
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

