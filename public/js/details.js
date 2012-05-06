$(document).ready(function() 
{	
	// When page ready request media details
	var hash = getParameterByName('mn');	

	if(hash != "")
	{
		$.get("/ajax-media?d="+hash, function(data)
		{
			// Parse the data coming from ajax call
			var obj = jQuery.parseJSON(data);
			var files = obj['files'];

			for(var x in files)
			{
				var first = x.substring(0,2);
				var second = x.substring(2,4);
				var imgfile = '/data/images/'+first+'/'+second+'/'+x+'_resize.png';
				var tmbfile = '/data/images/'+first+'/'+second+'/'+x+'_thumb.png';		
				$(".ad-thumb-list").append('<li><a href="'+imgfile+'"><img src="'+tmbfile+'"/></a></li>');
			}

			$("#pubtex-tags").tokenInput("http://pubtexi.local/ajax-tags", 
			{
				theme: "facebook",
				searchDelay: 100,
				hintText: false,
				preventDuplicates: true,
				deleteText: '',
			});
			
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
	}
});
