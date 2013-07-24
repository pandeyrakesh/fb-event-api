<?php
if(isset($_REQUEST['message']))
{
	include_once($_SERVER['DOCUMENT_ROOT'].'/config.php');
	require '../src/facebook.php';
	$facebook = new Facebook(array(
  	'appId'  => $appId,
  	'secret' => $appSecret,
	));
	$user = $facebook->getUser();
}
if ($user)
{
	try
	{
		if(isset($_REQUEST['message']))
		{
			$message = $_REQUEST['message'];
			$attachment = array(
    		'message' => $message,
			);
			if($_REQUEST['postId'])
			{
				$eventId = $_REQUEST['eventId'];
				$result = $facebook->api($_REQUEST['postId'].'/comments/', 'post', $attachment);
			}
			else
			{
				$eventId = $_REQUEST['eventId'];
				$result = $facebook->api($eventId.'/feed/', 'post', $attachment);
			}
		}
		$ev = $facebook->api($eventId.'/feed');
	} catch (FacebookApiException $e)
	{
		error_log($e);
		$user = null;
	}
	if(count($ev['data'])>0)
	{
		foreach ($ev['data'] as $val)
		{
			$uLike = false;
			if($val['likes']['count']>0)
			{
				foreach ($val['likes']['data'] as $vLike)
				{
					if(in_array($user,$vLike))
					{
						$uLike = true;
						break;
					}
					else
					{
						$uLike = false;
					}

				}
			}
?>
			<div class="friends_area" id="record-" style="line-height:normal;border-bottom: #cccccc solid 1px;">
				<a href='http://www.facebook.com/profile.php?id=<?php echo $val['from']['id'];?> '>
				<img src="https://graph.facebook.com/<?php echo $val['from']['id'];?>/picture" style="float: left;border:0;" alt="" /></a>
				<label style="float: left" class="name">
					<b  style="font-size: 13px;" >
						<a  href='http://www.facebook.com/profile.php?id=<?php echo $val['from']['id'];?> '><?php echo $val['from']['name'];?></a>
					</b>
					<br /><em> <?php echo $val['message'];?></em><br/>
					<?php 
					if($val['type']=='video')
					{
					?>
						<video width="378px" height="278px" controls="controls" style="margin-top: 10px;margin-bottom: 10px;" >
						  <source src="<?php echo $val['source'];?>" type="video/ogg" />
						</video>
					<?php 
					}else if($val['type']=='photo'){
					?>
					<img src="<?php echo $val['picture'];?>" style="border:0px solid rgba(0, 0, 0, .08);width: 378px;height: 278px;margin-top: 10px;margin-bottom: 10px;" />
					<br clear="all" />
					<?php }else if($val['type']=='link'){?>
					<div style="position: relative;width: 398px; height: 90px;background-color: #F7F7F7; border:1px solid rgba(0, 0, 0, .08);margin-top: 10px;margin-bottom: 10px;">
		
					<div style="height: 90px;width: 90px;border-right: 1px solid rgba(0, 0, 0, .08);  display: inline-block;display: inline-block;">
						<a  href="http://google.com/"   target="_blank">
							<img class="" src="<?php echo $val['picture'];?>" alt="" style="border:0px solid rgba(0, 0, 0, .08);height: 90px;width: 90px;">
						</a>
					</div>
					<div style="height: 90px;width: 278px;display: inline-block;vertical-align: top;padding: 5px 0 0 5px; ">
						<a class="" href="<?php echo $val['link'];?>"  target="_blank" style="text-decoration: none;">
								<div><strong style="font-size: 11px;color: #3B5998;"><?php echo $val['name'];?></strong> </div>
								<span style="font-size: 11px;color: gray;font-weight: lighter;"><?php echo $val['caption'];?></span>
								<div style="font-size: 11px;color: gray;font-weight: lighter;">
								<?php 
									$disc = str_split($val['description'],175); 
									echo $disc[0] ;
								?>
								</div>
							
						</a>
					</div>
			</div>
					<?php }?>
					<img src="<?php echo $val['icon'];?>" style="border: 0px;" />
					<span style="color:#999999;font-size: 11px;" >
						<?php if(!$uLike){?>
						<a href="javascript: void(0)" id="post_id" class="showCommentBox" onclick="likePost('like','<?php echo $val['id']; ?>');" style="font-weight:normal;">Like</a>
						<?php }else{?>
						<a href="javascript: void(0)" id="post_id" class="showCommentBox" onclick="likePost('unlike','<?php echo $val['id']; ?>');" style="font-weight:normal;">Unlike</a>
						<?php }?>
						&nbsp;.&nbsp;
						<a href="javascript: void(0)" id="post_id_Comment" class="showCommentBox" style="font-weight:normal;">Comment</a>
						&nbsp;.&nbsp;
						<?php echo returnDate($val['created_time']);?>
					</span>
				</label>
				<?php if($val['from']['id']==$user){?>
					<a  href="javascript: void(0)" class="commentRemoverButton uiCloseButton" href="#" title="remove" onclick="likePost('deletePost','<?php echo $val['id']; ?>');"></a>
				<?php }?>
				<br clear="all" />
				<?php 
				if($val['likes']['count']>0)
				{
				?>
				<div class="commentBox" align="left" id="commentBox-Like" style="height: 15px;width: 400px;">
					<div id="record-Like">
						<span style="display: inline;" class="uiUfiLikeIcon uiUfiLikeIconDisabled">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</span>
						<?php
						if($uLike)
						{
							if($val['likes']['count']>1)
								echo "You and " . ($val['likes']['count']-1) . " people like this.";
							else
								echo "You like this.";
						} else 
						{
						 echo $val['likes']['count']." people like this.";
						}
						?>
					</div>
					<br	clear="all" />
				</div>
				<?php
				}
				 $nestedEventMsg = $facebook->api($val['id'].'/comments');
				if(count($nestedEventMsg['data'])>0)
				{
					foreach($nestedEventMsg['data'] as $msg)
					{
						?>
						
						<div id="CommentPosted" style="line-height:normal;">
							
							<div class="commentPanel" id="record-" align="left" style="height: auto;width:400px;line-height:none;">
							
								<a href='http://www.facebook.com/profile.php?id=<?php echo $msg['from']['id'];?> '>
									<img  src="https://graph.facebook.com/<?php echo $msg['from']['id'];?>/picture" width="32px" height='32px' class="CommentImg" style="float: left;border:0;" alt="" />
								</a>
								<p style="line-height:normal;padding: 0 0 0 40px;">
									<b>
										<a href="http://www.facebook.com/profile.php?id=<?php echo $msg['from']['id'];?>"><?php echo $msg['from']['name'];?><a/>
									</b>
									<span ><?php  echo $msg['message'];?></span>
									<?php if($msg['from']['id']==$user){?>
										<a  ref="javascript: void(0)" class="commentRemoverButton uiCloseButton" href="#"  style="float: right; title="remove"  onclick="likePost('deletePost','<?php echo $msg['id']; ?>');"></a>
									<?php }?>
									<br />
									<span style="color:#999999;"><?php echo returnDate($msg['created_time']); ?>
										&nbsp;.&nbsp;
									<?php if($msg['user_likes']!=1){?>
										<a href="javascript: void(0)" id="post_id_Inner" class="showCommentBox" onclick="likePost('like','<?php echo $val['id']."_".$msg['id']; ?>');" style="font-weight:normal;">Like</a>
									<?php }else{?>
										<a href="javascript: void(0)" id="post_id_Inner" class="showCommentBox" onclick="likePost('unlike','<?php echo $val['id']."_".$msg['id']; ?>');" style="font-weight:normal;">Unlike</a>
									<?php }?>
									<?php 
										if($msg['like_count']>0)
										{
										?>
										&nbsp;.&nbsp;
										<a href="javascript: void(0)"  style="display: inline;font-weight:normal;text-decoration:none;" class="uiUfiLikeIcon uiUfiLikeIconDisabled"   href="#">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $msg['like_count']?></a>
										<?php }?>
									</span>
									
								</p>
							</div>

							
						</div>
				<?php
					} 
				}
				?>
				<div class="commentBox" align="right" id="commentBox-" style="height: 35px;width: 400px;">
					<img src="https://graph.facebook.com/<?php echo $user;?>/picture" width="32px"  height='32px' class="CommentImg" style="float: left;border:0;"	alt="" />
					<label id="record-">
						<textarea class="commentMark" id="commentMark-<?php echo $val['id'];?>" name="commentMark" onKeyPress="enter(event,'<?php echo $val['id']; ?>');" style="height: 25px" cols="80"></textarea>
					</label>
					<br	clear="all" />
				</div>
			</div>
		<?php
		}
		if(true)
		{
		?>
		<div id="bottomMoreButton">
			<a id="more_<?php echo @$next_records?>" class="more_records" href="javascript: void(0)">Older Posts</a>
		</div>
		<?php
		}
	}
}


function returnDate($timestamp)
{
	
	date_default_timezone_set('Atlantic/Reykjavik');
	$timestamp = strtotime($timestamp);
	$curr_date = date('Y-m-d h:i a', $timestamp);
	$datetime = new DateTime($curr_date);
	$datetime->format('Y-m-d h:i a') . "\n"."<br/>";
	$la_time = new DateTimeZone('Asia/Kolkata');
	$datetime->setTimezone($la_time);
	$datetime->format('l F d Y h:i A');//get date here 
	$t = explode(" ",$datetime->format('l F d Y h:i a'));// explode date here
	$day = $t[0];
	$month = $t[1];
	$date = $t[2];
	$year = $t[3];
	$cu_time =$t[4];
	$AorP =$t[5];
	
	$fullTime = $day."   "." at ".$cu_time.$AorP;
	$fulldateTime = $month."   ".$date." at ".$cu_time.$AorP;
	$fullyearTime = $month."   ".$date." at ".$year;

	$timestamp      = (int) $timestamp;
	$current_time   = time();
	$diff           = $current_time - $timestamp;
	//intervals in seconds
	$intervals      = array (
        'year' => 31556926, 'month' => 2629744, 'week' => 604800,'beforeyesterday'=>345600,'day' => 86400, 'hour' => 3600, 'minute'=> 60
	);
	//now we just find the difference
	if ($diff == 0)
	{
		return 'just now';
	}
	if ($diff < 60)
	{
		return $diff == 1 ? $diff . ' second ago' : $diff . ' seconds ago';
	}
	if ($diff >= 60 && $diff < $intervals['hour'])
	{
		$diff = floor($diff/$intervals['minute']);
		return $diff == 1 ? $diff . ' minute ago' : $diff . ' minutes ago';
	}
	if ($diff >= $intervals['hour'] && $diff < $intervals['day'])
	{
		$diff = floor($diff/$intervals['hour']);
		return $diff == 1 ? $diff . ' hour ago' : $diff . ' hours ago';
	}
	if ($diff >= $intervals['day'] && $diff < $intervals['beforeyesterday'])
	{
		$diff = floor($diff/$intervals['day']);
		return $diff == 1 ? ' yesterday at  '.$cu_time.$AorP : $fullTime ;
	}
	
	if ($diff >= $intervals['beforeyesterday'] && $diff < $intervals['month'])
	{
		$diff = floor($diff/$intervals['week']);
		return $diff == 1 ? $fulldateTime : $fulldateTime;
	}
	if ($diff >= $intervals['month'] && $diff < $intervals['year'])
	{
		$diff = floor($diff/$intervals['month']);
		return $diff == 1 ? $diff . ' month ago' : $diff . ' months ago';
	}
	if ($diff >= $intervals['year'])
	{
		$diff = floor($diff/$intervals['year']);
		return $diff == 1 ? $fullyearTime: $fullyearTime;
	}
}
?>