<?php if($user)
{
	?>
<link href="<?php echo $hostUrl;?>/facebook/css/facebox.css" media="screen" rel="stylesheet" type="text/css" />
<link href="<?php echo $hostUrl;?>/facebook/css/screen.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo $hostUrl;?>/facebook/js/jquery.livequery.js"></script>
<script src="<?php echo $hostUrl;?>/facebook/js/jquery.elastic.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo $hostUrl;?>/facebook/js/jquery.watermarkinput.js" type="text/javascript"></script>
<script type="text/javascript">

		   $(document).ready(function()
			{
			   $('#shareButton').click(function()
				{
					var a = $("#watermark").val();
					var fbEventId = $("#fbEventId").val();
					if(a != "Write Somthing...")
					{
						$.post("<?php $hostUrl?>/facebook/comment/posts.php?message="+a+"&eventId="+fbEventId, {
			
						}, function(response){
							
							$("#posting").html($(response).fadeIn('slow'));
							$("#watermark").val("Write Something...");
							$(".commentMark").Watermark("Write a comment...");
						});
					}
				});	
		
		//$('textarea').elastic();
		jQuery(function($)
		{
		   $("#watermark").Watermark("Write Something...");
		   $(".commentMark").Watermark("Write a comment...");

		});

		jQuery(function($)
		{
		   $(".commentMark").Watermark("watermark","#EEEEEE");
		});	
	});	


	function sharePost()
	{
		var textPost = document.getElementById('watermark').value;
		if(textPost != "")
		{
			document.forms["postsForm"].submit();
		}
	}
	
	function enter(evt,fbEventId)
	{
		var charCode = (evt.which) ? evt.which : window.event.keyCode; 
		if (charCode == 13) 
		{ 
			var a = $("#commentMark-"+fbEventId).val();
					document.getElementById("commentMark-"+fbEventId).disabled = true;
			if(a != "Write a comment...")
			{
				$.post("<?php $hostUrl?>/facebook/comment/posts.php?message="+a+"&postId="+fbEventId+"&eventId=<?php echo $eventId;?>", {
			
				}, function(response){
					$("#posting").html($(response).fadeIn('slow'));
					$(".commentMark").Watermark("Write a comment...");
				});
			}
		} 
	}

	function likePost(opt,postId)
	{
		$.post("<?php $hostUrl?>/facebook/comment/likes.php?postId="+postId+"&opt="+opt+"&eventId=<?php echo $eventId;?>", {
			
		}, function(response){
			$("#posting").html($(response).fadeIn('slow'));
			$(".commentMark").Watermark("Write a comment...");
		});
	}
		
</script>
<div>

	<form action="" method="post" name="postsForm">
		<input type='hidden' value='<?php echo $eventId;?>' name='fbEventId' id='fbEventId'/>
		<div class="UIComposer_Box">
			<span class="w">
				<textarea class="input" id="watermark" name="watermark" style="height: 30px;width: 480px;" cols="80" ></textarea>
			</span>
			<br clear="all" />
			<div align="left" style="height: 30px; padding: 10px 5px;">
				<span style="float: left; padding-right: 450px;">&nbsp; </span>
					<input type="button" name='shareButton' id="shareButton" value='Share' class='fbboldfont' style="background-color:#3b5998;color:white;padding: 3px 10px 16px 10px;float: left;height:25px;width: 55px;"/>								
			</div>
			</div>
	</form>
	<br clear="all" />
	<div id="posting" align="center">
		<?php include 'posts.php';?>
	</div>
</div>
<?php } ?>