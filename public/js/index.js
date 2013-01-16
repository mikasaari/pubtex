var first_id;
var last_id;
var show_count = 5;
var scrollTimer, lastScrollFireTime = 0;

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

function wheel(e)
{
	e.preventDefault();
}

var keys = [37, 38, 39, 40];

function keydown(e)
{
	for(var i = keys.length; i--;)
	{
		if(e.keyCode === keys[i])
		{
			e.preventDefault();
			return;
		}

		if(e.keyCode === 27)
		{
			closeDetails();
			return;
		}
	}
}

function closeDetails()
{41
	$('#details').hide();
	$('.dim').hide();

   if (window.removeEventListener) 
		{
       window.removeEventListener('DOMMouseScroll', wheel, false);
   }
   window.onmousewheel = document.onmousewheel = document.onkeydown = null;

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

			// Clear Ad-Controls
			$(".ad-controls").empty();

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
		document.onkeydown = keydown;

		// Hide details if clicked outside of the details div
		$('.dim').click(function()
		{
			closeDetails();
		});

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

			// $("#freesearch").append('<span class="mediacontainer"><span class="media"><span class="mediathumb"><span class="thumbover"><img src="images/pxmDownload.png" /><img src="images/pxmAddPacket.png" /><img src="images/pxmRateUp.png" /><img src="images/pxmRateDown.png" /></span><a href="#" onclick="details(\''+obj[x].media_hash+'\'); return false;"><img src="'+imgfile+'"/></a></span></span></span>');

			thumbstr = '<span class="mediacontainer">\n' +
'	<span class="media">\n' +
'		<span class="mediathumb">\n' +
'	  	<div class="thumbdownload"><a href="#" onclick="download(\''+obj[x].file_hash+'\');return false;"><img src="images/pxmDownload.png" /></a></div>\n' +
'			<div class="thumbaddpacket"><a href="#" onclick="addpacket(\''+obj[x].file_hash+'\');return false;"><img src="images/pxmAddPacket.png" /></a></div>\n' +
'			<div class="thumbrateup"><a href="#" onclick="rateup(\''+obj[x].file_hash+'\');return false;"><img src="images/pxmRateUp.png" /></a></div>\n' +
'			<div class="thumbratedown"><a href="#" onclick="ratedown(\''+obj[x].file_hash+'\');return false;"><img src="images/pxmRateDown.png" /></a></div>\n' +
'			<a href="#" onclick="details(\''+obj[x].media_hash+'\'); return false;"><img src="'+imgfile+'"/></a>\n' +
'		</span>\n' +
'	</span>\n'+
'</span>\n';

			$("#freesearch").append(thumbstr);
		}

		// Update first_id and last_id
		first_id = obj[0].id;
		last_id = obj[obj.length-1].id;

		// -------------------------------------------------------
		// Media actions
		// -------------------------------------------------------
		$(".mediacontainer").mouseover(function()
		{
			$(this).find("div.thumbdownload").show();
			$(this).find("div.thumbaddpacket").show();
			$(this).find("div.thumbrateup").show();
			$(this).find("div.thumbratedown").show();
		});

		$(".mediacontainer").mouseout(function()
		{
			$(this).find("div.thumbdownload").hide();
			$(this).find("div.thumbaddpacket").hide();
			$(this).find("div.thumbrateup").hide();
			$(this).find("div.thumbratedown").hide();
		});

	});
}

function download(dwfile)
{
	window.open('file-download?df='+dwfile);
}

// -------------------------------------------------------
// Document is ready, initialize everything
// -------------------------------------------------------
$(document).ready(function() 
{
	// Gallery image information
	var banner_height = 100;
	var menu_height = 100;
	var footer_height = 250;
	var image_count = Math.round(($(window).height() - banner_height - menu_height - footer_height) / 150) * 5;		
	
	// -------------------------------------------------------
	// When page loaded, fetch first images
	// -------------------------------------------------------
	searchGallery('/ajax-media?n='+image_count);

	// -------------------------------------------------------
	// Search text input actions
	// -------------------------------------------------------
	var srchstr = "";

	$("#searchinput").append('<form class="searchform" name="searchform"><span class="label">Search: </span><input class="searchfield" id="target" name="search" type="text" value="" /><input class="searchbutton" type="button" value="Go" /></form>');

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
			var ns = "/ajax-media?c="+image_count+"&";

			ta = tmp.split(' ');
			last = ta.length - 1;

			// Add full tags to the filter
			if(last > 0)
			{
				ns = ns + "ta=";
				for(x in ta)
				{
					ns = ns + ta[x];
					if(x < ta.length-2)
						ns = ns + ",";
				}
			}
	
			searchGallery(ns);
		}

		else if(event.which == 8)
		{
			var tmp = document.searchform.search.value;
			var ta = new Array();
			var ns = "/ajax-media?c="+image_count+"&ta=";

			ta = tmp.split(' ');
			last = ta.length - 1;
			if(last < 0)
				last = 0;

			for(x=0;x<last;x=x+1)
			{
				ns = ns + ta[x];
				if(x < ta.length-2)
					ns = ns + ",";
			}
			
			// Tags / No-Tags
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
			var ns = "/ajax-media?c="+image_count+"&";

			// Get tags from the input field
			ta = tmp.split(' ');
			last = ta.length - 1;
			if(last < 0)
				last = 0;

			// Add full tags to the filter
			if(last > 0)
			{
				ns = ns + "ta=";
				for(x=0;x<last;x=x+1)
				{
					ns = ns + ta[x];
					if(x < ta.length-2)
					{
						ns = ns + ",";
					}
				}
			}
			
			// Add partially written tag to the filter
			if(last > 0)
				ns = ns + '&pa='+ta[last];
			else
				ns = ns + 'pa='+ta[last];
			
			// Show images on the screen
			searchGallery(ns);

			// Update saved search string for next round
			srchstr = document.searchform.search.value;
		}		
	});

	$('#target').keydown(
	function(event) 
	{

	});

	// -------------------------------------------------------
	// Scrolling behaviour
	// -------------------------------------------------------

	$(window).scroll(function()
	{
    var minScrollTime = 100;
    var now = new Date().getTime();

		function processScroll()
		{		
			if(($(window).scrollTop() > $(document).height()-$(window).height()-20 || $(window).height() >= $(document).height()))
			{	
				var tmp = document.searchform.search.value;
				var searching = "";

				// Generate search filter if there is tags in text input field
				if(tmp.length > 0)
				{
					var ta = new Array();
					searching = '/ajax-media?c='+show_count+'&s='+last_id+'&ta=';

					ta = tmp.split(' ');
					last = ta.length - 1;
					if(last < 0)
						last = 0;

					for(x=0;x<last;x=x+1)
					{
						searching = searching + ta[x];
						if(x < ta.length-2)
							searching = searching + ",";
					}
				}
				else
				{
					searching = '/ajax-media?n='+show_count+'&s='+last_id;
				}
	
				// If there is search filter generated, then request data from http server
				if(searching != "")
				{
					$.get(searching, function(data)
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
					});
				}		
			}
		}

    if (!scrollTimer) 
		{
			if (now - lastScrollFireTime > (3 * minScrollTime)) 
			{
				processScroll();   // fire immediately on first scroll
				lastScrollFireTime = now;
			}

			scrollTimer = setTimeout(function() 
			{
				scrollTimer = null;
				lastScrollFireTime = new Date().getTime();
				processScroll();
			}, minScrollTime);
		}	
	});

	// -------------------------------------------------------
	// Token Input for Detail popup
	// -------------------------------------------------------
	$("#pubtex-tags").tokenInput("http://pubtexi.local/ajax-tags", 
	{
		theme: "facebook",
		searchDelay: 100,
		hintText: false,
		preventDuplicates: true,
		deleteText: '',
	});

	// -------------------------------------------------------
	// Login, Logout area
	// -------------------------------------------------------
	$("#signin").mouseover(function()
	{
	
		$("#popup").show();
		return false;
	}).mouseout(function()
	{
		$("#popup").hide();
		return true;
	});

	$("body #main").click(function()
	{
		$("#popup").hide();
		return false;
	});

	// -------------------------------------------------------
	// Initialize tabs
	// -------------------------------------------------------
	$('#tabs').tabs();	

});

