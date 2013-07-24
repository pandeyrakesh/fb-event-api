<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/config.php');
require '../src/facebook.php';
$facebook = new Facebook(array(
  'appId'  => $appId,
  'secret' => $appSecret,
));
$user = $facebook->getUser();
$eventId =$_GET['fbEventId'];
if ($user)
{
	try
	{
		$fbAllEvent = $facebook->api($eventId.'/attending');
		$maybe = $facebook->api($eventId.'/maybe');
		$noreply = $facebook->api($eventId.'/noreply');
		
		$sql = "SELECT name, venue, location, start_time,end_time,description ,pic_small,pic_square,pic_big FROM event WHERE eid='$eventId'";
		$eventDetail = $facebook->api(array(
		'method' => 'facebook.fql_query',
		'query'	 => $sql
		));
	$postTitle = $eventDetail[0]['name'];
	$postContent = $eventDetail[0]['description'];
	$address = $eventDetail[0]['location'];
	$venue_id=$eventDetail[0]['venue']['id'];
	$phpdateStart = $eventDetail[0]['start_time'];
	$phpdateEnd  = $eventDetail[0]['end_time'];
	
	$sql = 	"SELECT location FROM page WHERE page_id = '$venue_id'";
		$venueDetail = $facebook->api(array(
		'method' => 'facebook.fql_query',
		'query'	 => $sql
		));
	
	$city = $venueDetail[0]['location']['city'];
	$street = $venueDetail[0]['location']['street'];
	$province = $venueDetail[0]['location']['state'];
	$country = $venueDetail[0]['location']['country'];
	$latitude = $venueDetail[0]['location']['latitude'];
	$longitude = $venueDetail[0]['location']['longitude'];
	$postal_code = $venueDetail[0]['location']['zip'];


	} catch (FacebookApiException $e)
	{
		error_log($e);
		$user = null;
	}
}
$facebook->getAppId();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
  						<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  						<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js'></script> 
  						<link rel="stylesheet" type="text/css" href="<?php $hostUrl?>/facebook/css/bootstrap.css" />
						<script type="text/javascript" src="<?php $hostUrl?>/facebook/js/jquery.js"></script>
						<script type="text/javascript" src="<?php $hostUrl?>/facebook/js/bootstrap.min.js"></script>
						<script type="text/javascript" src="<?php $hostUrl?>/facebook/js/bootstrap.js"></script>
						<script type="text/javascript" src="<?php $hostUrl?>/facebook/js/bootstrap-tab.js"></script>
						<title><?php echo $postTitle; ?></title>
						<style type="text/css">
						
							  .left-column, .right-column{
  								float:left;
								}
								.left-column{
  									width:30%; 
									}
								.right-column{
 									 width:60%; 
								}
		            
						    #tabs ul li{
						        font-size: 80%;
						    }
						    #tabs pre{
						        font-size: 138%;
						    }
						    #tabs{
						        padding: 8px;  
		    					font-family: lucida grande,tahoma,verdana,arial,sans-serif;
		    					float: left;  
		   						margin-right: 4px;  
		    					text-decoration: none; 
						        font-size: 80%;
						    }
						    #tabs div a{
						        color: #3b5998;  
		    				       outline-style: none;  
		    				       text-decoration: none;  
		    				      font-size: 11px;  
		    				      font-weight: bold;
		    				      color: #3b5998; 
						    }
						  #tabs div a:hover  
						{  
		    					text-decoration: underline;  
						}
						    .ui-tabs .ui-tabs-nav {
						        padding: 0 !important;
						    }
				        </style>
				        <script>
						  $(function () {
							  $('#myTab a').click(function (e) {
								  e.preventDefault();
								  $(this).tab('show');
								})
						  })
						  </script>
		    </head>
		   <body >
		   <div id="fb-root"></div>
		    <script>
		      window.fbAsyncInit = function() {
		        FB.init({
		          appId: '<?php echo  $appId;?>',
		          cookie: true,
		          xfbml: true,
		          oauth: true
		        });
		        FB.Event.subscribe('auth.login', function(response) {
		          window.location.reload();
		        });
		        FB.Event.subscribe('auth.logout', function(response) {
		          window.location.reload();
		        });
		        (function() {
			          var e = document.createElement('script');
			          e.type = 'text/javascript';
			          e.src = document.location.protocol +
			              '//connect.facebook.net/en_US/all.js';
			          e.async = true;
			          document.getElementById('fb-root').appendChild(e);
			      }());
		      };
		    </script>
		    <div id="fb-root"></div>
			<script>(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=<?php echo  $appId;?>";
				fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));
			</script>
			<?php if($user==0){?>
			<fb:login-button login_text='Log In' perms="user_events,rsvp_event,publish_stream,friends_events,user_groups,manage_pages"></fb:login-button>
			<?php }?>
    		<!--<div id="facebook-title">facebook</div>
    		-->
				<div class="tab-content" id="tabs"  style="text-align:left;width: 60%;">
				    <div style="text-align:left;" class='left-column'>
				        <div style="height: 480px; text-align:left;">
							<table>
								<?php 
								if(count($fbAllEvent['data'])>0)
								{
								?>
								<tr style="border-right: 1px solid #ccc;border-top: 1px solid #ccc">
									<td valign='top'>
										<h4>Accepted</h4>
										<table style="height: 100px;width80px;woverflow: auto;">	
											<tr><td></td><td></td></tr>
							        		<?php
											$allUser = $fbAllEvent['data'];
											$i=0;
							          		while($i<count($allUser) )
											{
												$msg['from']['id'];
												$k= $i;
												echo "<tr><td><a target=\"_blank\" href='http://www.facebook.com/profile.php?id=".$allUser[$k]['id']."'>"."<img  src='https://graph.facebook.com/".$allUser[$k][id]."/picture' width='30px' height='30px' class='CommentImg' style='float: left;border:0;' alt='' />".$allUser[$k]['name']."</a></td></tr>";
												//$k = $k+1;
												$i=$i+1;
											}
							       		 	?>
										</table>
									</td>
									<td style="border-right: 1px solid #ccc;border-top: 1px solid #ccc">&nbsp;&nbsp;</td>
								</tr>
								<?php 
								}
								if(count($maybe['data'])>0)
								{
								?>
								<tr style="border-right: 1px solid #ccc;border-top: 1px solid #ccc">
									<td valign='top'>
										<h4>Maybe</h4>
										<table style="height: 100px;width80px;">	
											<tr><td></td></tr>
							        		<?php
											$allMaybeUser = $maybe['data'];
											$j=0;
							          		while($j<count($allMaybeUser) )
											{
												$k= $j;
												echo "<tr><td><a target=\"_blank\" href='http://www.facebook.com/profile.php?id=".$allMaybeUser[$k]['id']."'>"."<img  src='https://graph.facebook.com/".$allMaybeUser[$k][id]."/picture' width='30px' height='30px' class='CommentImg' style='float: left;border:0;' alt='' />".$allMaybeUser[$k]['name']."</a></td></tr>";
												//$k = $k+1;
												$j=$j+1;
											}
							       		 	?>
										</table>
									</td>
									<td >&nbsp;&nbsp;</td>
								</tr>
								<?php 
								}
								if(count($noreply['data'])>0)
								{
								?>
								<tr style="border-right: 1px solid #ccc;border-top: 1px solid #ccc">
									<td valign='top'>
										<h4>Invited</h4>
										<table style="height: 100px;width80px;woverflow: auto;">	
											<tr><td></td></tr>
							        		<?php
											$allMaybeUser = $noreply['data'];
											$j=0;
							          		while($j<count($allMaybeUser) )
											{
												$k= $j;
												echo "<tr><td><a target=\"_blank\" href='http://www.facebook.com/profile.php?id=".$allMaybeUser[$k]['id']."'>"."<img  src='https://graph.facebook.com/".$allMaybeUser[$k][id]."/picture' width='30px' height='30px' class='CommentImg' style='float: left;border:0;' alt='' />".$allMaybeUser[$k]['name']."</a></td></tr>";
												//$k = $k+1;
												$j=$j+1;
											}
							       		 	?>
										</table>
									</td>
									<td >&nbsp;&nbsp;</td>
								</tr>
								<?php 
								}
								?>
							</table>
				        </div>
				    </div>
				    
				    <div class='right-column'>
				    	<h3><?php echo $postTitle; ?></h3>
				    	<div style="font-size: 13px;">
	    					<div style="border-top: 1px solid #ccc;margin-top: 5px;">
								<img class="img" src="https://fbstatic-a.akamaihd.net/rsrc.php/v2/yJ/r/UmLbGfwEuH6.png" title="When" width="16" height="16"/> 
								&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $phpdateStart." until ". $phpdateEnd."<br /><br/>"?>
							</div>
							<div style="border-top: 1px solid #ccc;margin-top: 5px;">
								<img class="img" src="https://fbstatic-a.akamaihd.net/rsrc.php/v2/yT/r/K6_TY47YS3x.png" title="Description" width="16" height="16" />
								 &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $postContent."<br /><br/>"?>
							</div>
							<div style="border-top: 1px solid #ccc;">
								<?php
								$address=str_replace(' ','+',$address);
								$street=str_replace(' ','+',$street);
								$city=str_replace(' ','+',$city);
								$country=str_replace(' ','+',$country);
								 $addLoc= $address.",".$street.",".$city.",".$country;
								?>
								  
							</div>
				    	</div>
				      <div style="height: 480px;text-align:left;">
       					<?php 
       					include 'index.php';
       					?>
				       </div>
				   </div>
		    </div>
		    </body>
	</html>