<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/config.php');
require '../src/facebook.php';
$facebook = new Facebook(array(
  'appId'  => $appId,
  'secret' => $appSecret,
));
$user = $facebook->getUser();
$postId = $_REQUEST['postId'];
$eventId = $_REQUEST['eventId'];
if ($user)
{
	try
	{
		if($_REQUEST['postId'])
		{
			if($_REQUEST['opt']=='like')
			{
				$facebook->api($postId.'/likes/', 'post');
			}
			else if($_REQUEST['opt']=='unlike')
			{
				$facebook->api($postId.'/likes/', 'delete');
			}
			else if($_REQUEST['opt']=='deletePost')
			{
				$facebook->api($postId.'/', 'delete');
			}
		}
	} catch (FacebookApiException $e)
	{
		error_log($e);
		$user = null;
	}
	include 'posts.php';
}
?>