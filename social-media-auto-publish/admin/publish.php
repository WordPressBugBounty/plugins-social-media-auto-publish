<?php
if( !defined('ABSPATH') ){ exit();}
/*add_action('publish_post', 'xyz_link_publish');
add_action('publish_page', 'xyz_link_publish');

$xyz_smap_future_to_publish=get_option('xyz_smap_std_future_to_publish');

if($xyz_smap_future_to_publish==1)
	add_action('future_to_publish', 'xyz_link_smap_future_to_publish');

function xyz_link_smap_future_to_publish($post){
	$postid =$post->ID;
	xyz_link_publish($postid);
}*/
add_action(  'transition_post_status',  'xyz_link_smap_future_to_publish', 10, 3 );

function xyz_link_smap_future_to_publish($new_status, $old_status, $post){

	if (isset($_GET['_locale']) && empty($_POST))
		return ;

	if(!isset($GLOBALS['smap_dup_publish']))
		$GLOBALS['smap_dup_publish']=array();
	$postid =$post->ID;
	$post_published_date_time=$post_modified_date_time=time();
	if ($post) {
		$post_published_date_time = strtotime(get_the_date('Y-m-d H:i:s', $postid));
		$post_modified_date_time = strtotime(get_the_modified_date('Y-m-d H:i:s', $postid));
	}
	$get_post_meta=get_post_meta($postid,"xyz_smap",true);                           //	prevent duplicate publishing
	$post_permissin=get_option('xyz_smap_post_permission');
	$post_twitter_permission=get_option('xyz_smap_twpost_permission');
	$lnpost_permission=get_option('xyz_smap_lnpost_permission');
	$igpost_permission=get_option('xyz_smap_igpost_permission');
	$tgpost_permission=get_option('xyz_smap_tgpost_permission');
	$post_threads_permission=get_option('xyz_smap_thpost_permission');

	if(isset($_POST['xyz_smap_post_permission']))
	{
		$post_permissin=intval($_POST['xyz_smap_post_permission']);
		if ( (isset($_POST['xyz_smap_post_permission']) && isset($_POST['xyz_smap_po_method'])) )
		{
			$futToPubDataFbArray=array( 'post_fb_permission'	=>	$_POST['xyz_smap_post_permission'],
									  'xyz_fb_po_method'	=>	$_POST['xyz_smap_po_method'],
									  'xyz_fb_message'	=>	$_POST['xyz_smap_message']);
			update_post_meta($postid, "xyz_smap_fb_future_to_publish", $futToPubDataFbArray);
		}
	}
	if(isset($_POST['xyz_smap_twpost_permission']))
	{
		$post_twitter_permission=intval($_POST['xyz_smap_twpost_permission']);
		if ( (isset($_POST['xyz_smap_twpost_permission']) && isset($_POST['xyz_smap_twpost_image_permission'])) )
		{
			$futToPubDataTwArray=array('post_tw_permission'	=>	$_POST['xyz_smap_twpost_permission'],
					'xyz_tw_img_permissn'	=>	$_POST['xyz_smap_twpost_image_permission'],
					'xyz_tw_message'	=>	$_POST['xyz_smap_twmessage']);
			update_post_meta($postid, "xyz_smap_tw_future_to_publish", $futToPubDataTwArray);
		}
	}
	if(isset($_POST['xyz_smap_thpost_permission']))
	{
		$post_threads_permission=intval($_POST['xyz_smap_thpost_permission']);
		if ( (isset($_POST['xyz_smap_thpost_permission']) && isset($_POST['xyz_smap_thpost_method'])) )
		{
			$futToPubDataThArray=array('post_th_permission'	=>	$_POST['xyz_smap_thpost_permission'],
					'xyz_smap_thpost_method'	=>	$_POST['xyz_smap_thpost_method'],
					'xyz_th_message'	=>	$_POST['xyz_smap_thmessage']);
			update_post_meta($postid, "xyz_smap_th_future_to_publish", $futToPubDataThArray);
		}
	}
	if(isset($_POST['xyz_smap_tbpost_permission'])){
	    $post_tb_permission=intval($_POST['xyz_smap_tbpost_permission']);
	    if((isset($_POST['xyz_smap_tbpost_permission']) && isset($_POST['xyz_smap_tbpost_media_permission'])))
	    {
	        $futToPubDataTbArray=array( 'post_tb_permission'	=>	$_POST['xyz_smap_tbpost_permission'],
	            'xyz_smap_tbpost_media_permission'	=>	$_POST['xyz_smap_tbpost_media_permission'],
	            'xyz_smap_tbmessage'	=>	$_POST['xyz_smap_tbmessage']);
	        update_post_meta($postid, "xyz_smap_tb_future_to_publish", $futToPubDataTbArray);
	    }
	}
	if(isset($_POST['xyz_smap_lnpost_permission']))
	{
		$lnpost_permission=intval($_POST['xyz_smap_lnpost_permission']);
		if ( (isset($_POST['xyz_smap_lnpost_permission']) && isset($_POST['xyz_smap_ln_shareprivate'])) )
		{
			$futToPubDataLnArray=array(
					'post_ln_permission'	=>	$_POST['xyz_smap_lnpost_permission'],
					'xyz_smap_ln_shareprivate'	=>	$_POST['xyz_smap_ln_shareprivate'],
					'xyz_smap_lnpost_method'	=>	$_POST['xyz_smap_lnpost_method'],
					'xyz_smap_lnmessage'	=>	$_POST['xyz_smap_lnmessage']);
			update_post_meta($postid, "xyz_smap_ln_future_to_publish", $futToPubDataLnArray);
		}
	}
	if(isset($_POST['xyz_smap_igpost_permission']))
	{
	    $igpost_permission=intval($_POST['xyz_smap_igpost_permission']);
	    if ( (isset($_POST['xyz_smap_igpost_permission']) && isset($_POST['xyz_smap_igpost_image_permission'])) )
	    {
	        $futToPubDataIgArray=array('post_ig_permission'	=>	$_POST['xyz_smap_igpost_permission'],
	            'xyz_ig_img_permissn'	=>	$_POST['xyz_smap_igpost_image_permission'],
	            'xyz_ig_message'	=>	$_POST['xyz_smap_igmessage']);
	        update_post_meta($postid, "xyz_smap_ig_future_to_publish", $futToPubDataIgArray);
	    }
	}
	if(isset($_POST['xyz_smap_tgpost_permission']))
	{
	    $tgpost_permission=intval($_POST['xyz_smap_tgpost_permission']);
	    if ( (isset($_POST['xyz_smap_tgpost_permission']) && isset($_POST['xyz_smap_tgpost_method'])) )
	    {
	        $futToPubDataTgArray=array('post_tg_permission'	=>	$_POST['xyz_smap_tgpost_permission'],
	            'xyz_smap_tgpost_method'	=>	$_POST['xyz_smap_tgpost_method'],
	            'xyz_tg_message'	=>	$_POST['xyz_smap_tgmessage']);
	        update_post_meta($postid, "xyz_smap_tg_future_to_publish", $futToPubDataTgArray);
	    }
	}
	if(!(isset($_POST['xyz_smap_post_permission']) || isset($_POST['xyz_smap_twpost_permission']) || isset($_POST['xyz_smap_thpost_permission']) || isset($_POST['xyz_smap_lnpost_permission']) || isset($_POST['xyz_smap_igpost_permission']) || isset($_POST['xyz_smap_tbpost_permission']) || isset($_POST['xyz_smap_tgpost_permission'])))
	{
	    if($post_permissin == 1 || $post_twitter_permission == 1 || $post_threads_permission == 1|| $lnpost_permission == 1 || $igpost_permission ==1 || $$post_tb_permission==1 || $tgpost_permission ==1) {

			if($new_status == 'publish')
			{
				if ($get_post_meta == 1 ) {
					if(get_option('xyz_smap_default_selection_edit')==0)
					return;
				}
				else //prevent backend publish
				{
					//post meta not 1, edited post
					if (($post_modified_date_time != $post_published_date_time) && $old_status=='publish' ) 
					{//already plublished ,auto publish on edit is disabled
						if ((get_option('xyz_smap_default_selection_edit') == 0))
							return;
					}
					//post meta not 1, new post ,auto publish on create is disabled
					else{
					if ((get_option('xyz_smap_default_selection_create') == 0))
						return;
					}
				}
			}
			else return;
		}
	}
	if($post_permissin == 1 || $post_twitter_permission == 1 || $lnpost_permission == 1 || $igpost_permission==1 || $post_tb_permission==1 || $tgpost_permission ==1)
	{
		if($new_status == 'publish')
		{
			if(!in_array($postid,$GLOBALS['smap_dup_publish'])) {
				$GLOBALS['smap_dup_publish'][]=$postid;
				xyz_link_publish($postid);
			}
		}

	}
	else return;

}

function xyz_link_publish($post_ID) {
	$_POST_CPY=$_POST;
	$_POST=stripslashes_deep($_POST);
	$get_post_meta_future_data_fb=get_post_meta($post_ID,"xyz_smap_fb_future_to_publish",true);
	$get_post_meta=get_post_meta($post_ID,"xyz_smap",true); 
	$post_twitter_image_permission=$post_tumblr_media_permission=$posting_method=$ln_posting_method=$xyz_smap_ln_shareprivate=$tg_posting_method=$xyz_smap_thpost_method=0;
	$message=$messagetopost=$lmessagetopost=$igmessagetopost=$tbmessagetopost=$tgmessagetopost=$thmessagetopost='';
	$post_permissin=get_option('xyz_smap_post_permission');

	if(!empty($get_post_meta_future_data_fb) && ((get_option('xyz_smap_default_selection_edit')==2 && $get_post_meta==1) || (get_option('xyz_smap_default_selection_create')==2 && $get_post_meta!=1 )))///select values from post meta
	{
		$post_permissin=$get_post_meta_future_data_fb['post_fb_permission'];
		$posting_method=$get_post_meta_future_data_fb['xyz_fb_po_method'];
		$message=$get_post_meta_future_data_fb['xyz_fb_message'];
	}
	if(isset($_POST['xyz_smap_post_permission']))
		$post_permissin=intval($_POST['xyz_smap_post_permission']);

	$post_twitter_permission=get_option('xyz_smap_twpost_permission');
	$get_post_meta_future_data_tw=get_post_meta($post_ID,"xyz_smap_tw_future_to_publish",true);

	if(!empty($get_post_meta_future_data_tw) && ((get_option('xyz_smap_default_selection_edit')==2 && $get_post_meta==1) || (get_option('xyz_smap_default_selection_create')==2 && $get_post_meta!=1 )))///select values from post meta
	{
		$post_twitter_permission=$get_post_meta_future_data_tw['post_tw_permission'];
		$post_twitter_image_permission=$get_post_meta_future_data_tw['xyz_tw_img_permissn'];
		$messagetopost=$get_post_meta_future_data_tw['xyz_tw_message'];
	}
	if(isset($_POST['xyz_smap_twpost_permission']))
	$post_twitter_permission=intval($_POST['xyz_smap_twpost_permission']);

	$post_tb_permission=get_option('xyz_smap_tbpost_permission');
	$get_post_meta_future_data_tb=get_post_meta($post_ID,"xyz_smap_tb_future_to_publish",true);
	if(!empty($get_post_meta_future_data_tb) && ((get_option('xyz_smap_default_selection_edit')==2 && $get_post_meta==1) || (get_option('xyz_smap_default_selection_create')==2 && $get_post_meta!=1 )))///select values from post meta
	{
		$post_tb_permission=$get_post_meta_future_data_tb['post_tb_permission'];
		$post_tumblr_media_permission=$get_post_meta_future_data_tb['xyz_smap_tbpost_media_permission'];
		$tbmessagetopost=$get_post_meta_future_data_tb['xyz_smap_tbmessage'];
	}
	if(isset($_POST['xyz_smap_tbpost_permission']))
	$post_tb_permission=intval($_POST['xyz_smap_tbpost_permission']);

	$igpost_permission=get_option('xyz_smap_igpost_permission');
	$get_post_meta_future_data_ig=get_post_meta($post_ID,"xyz_smap_ig_future_to_publish",true);
	if(!empty($get_post_meta_future_data_ig) && ((get_option('xyz_smap_default_selection_edit')==2 && $get_post_meta==1) || (get_option('xyz_smap_default_selection_create')==2 && $get_post_meta!=1 )))///select values from post meta
	{
		$igpost_permission=$get_post_meta_future_data_ig['post_ig_permission'];
// 		$ig_posting_method=$get_post_meta_future_data_ig['xyz_smap_igpost_method'];
		$igmessagetopost=$get_post_meta_future_data_ig['xyz_smap_igmessage'];
	}
	if(isset($_POST['xyz_smap_igpost_permission']))
	$igpost_permission=intval($_POST['xyz_smap_igpost_permission']);

	$lnpost_permission=get_option('xyz_smap_lnpost_permission');
	$get_post_meta_future_data_ln=get_post_meta($post_ID,"xyz_smap_ln_future_to_publish",true);
	if(!empty($get_post_meta_future_data_ln) && ((get_option('xyz_smap_default_selection_edit')==2 && $get_post_meta==1) || (get_option('xyz_smap_default_selection_create')==2 && $get_post_meta!=1 )))///select values from post meta
	{
		$lnpost_permission=$get_post_meta_future_data_ln['post_ln_permission'];
		$xyz_smap_ln_shareprivate=$get_post_meta_future_data_ln['xyz_smap_ln_shareprivate'];
		$ln_posting_method=$get_post_meta_future_data_ln['xyz_smap_lnpost_method'];
		$lmessagetopost=$get_post_meta_future_data_ln['xyz_smap_lnmessage'];
	}
	if(isset($_POST['xyz_smap_lnpost_permission']))
	$lnpost_permission=intval($_POST['xyz_smap_lnpost_permission']);

	$post_threads_permission=get_option('xyz_smap_thpost_permission');
	$get_post_meta_future_data_th=get_post_meta($post_ID,"xyz_smap_th_future_to_publish",true);
	if(!empty($get_post_meta_future_data_th) && ((get_option('xyz_smap_default_selection_edit')==2 && $get_post_meta==1) || (get_option('xyz_smap_default_selection_create')==2 && $get_post_meta!=1 )))///select values from post meta
	{
		$post_threads_permission=$get_post_meta_future_data_th['post_th_permission'];
		$xyz_smap_thpost_method=$get_post_meta_future_data_th['xyz_smap_thpost_method'];
		$messagetopost=$get_post_meta_future_data_th['xyz_th_message'];
	}
	if(isset($_POST['xyz_smap_thpost_permission']))
	$post_threads_permission=intval($_POST['xyz_smap_thpost_permission']);
	$tgpost_permission=get_option('xyz_smap_tgpost_permission');
	
	$get_post_meta_future_data_tg=get_post_meta($post_ID,"xyz_smap_tg_future_to_publish",true);
	if(!empty($get_post_meta_future_data_tg) && ((get_option('xyz_smap_default_selection_edit')==2 && $get_post_meta==1) || (get_option('xyz_smap_default_selection_create')==2 && $get_post_meta!=1 )))///select values from post meta
	{
		$tgpost_permission=$get_post_meta_future_data_tg['post_tg_permission'];
		$tg_posting_method=$get_post_meta_future_data_tg['xyz_smap_tgpost_method'];
		$tgmessagetopost=$get_post_meta_future_data_tg['xyz_tg_message'];
	}
	if(isset($_POST['xyz_smap_tgpost_permission']))
	$tgpost_permission=intval($_POST['xyz_smap_tgpost_permission']);
	if (($post_permissin != 1)&&($post_twitter_permission != 1)&&($lnpost_permission != 1)&&($igpost_permission != 1)&&($post_tb_permission != 1)&&($tgpost_permission != 1)&&($post_threads_permission != 1)) {
		$_POST=$_POST_CPY;
		return ;
	}elseif(((isset($_POST['_inline_edit'])) || (isset($_REQUEST['bulk_edit'])) ) && (get_option('xyz_smap_default_selection_edit') == 0 && $get_post_meta==1) ) {

		$_POST=$_POST_CPY;
		return;
	}

	global $current_user;
	wp_get_current_user();



/////////////twitter//////////
$tclient_id=$tclient_secret=$taccess_token=$taccess_token_secret=$tappid=$tappsecret=$tauthToken='';
$tw_af=1;
$xyz_smap_tw_app_sel_mode=get_option('xyz_smap_tw_app_sel_mode');
if($xyz_smap_tw_app_sel_mode==0){
	$tappid=get_option('xyz_smap_twconsumer_id');
	$tappsecret=get_option('xyz_smap_twconsumer_secret');
	$taccess_token=get_option('xyz_smap_current_twappln_token');
	$taccess_token_secret=get_option('xyz_smap_twaccestok_secret');
}
elseif($xyz_smap_tw_app_sel_mode==2){
	$tauthToken = get_option('xyz_smap_tw_token');
	$tw_af = get_option('xyz_smap_tw_af');
}
	$twid=get_option('xyz_smap_tw_id');
	if ($messagetopost=='')
	$messagetopost=get_option('xyz_smap_twmessage');
	if(isset($_POST['xyz_smap_twmessage']))
		$messagetopost=$_POST['xyz_smap_twmessage'];
	$appid=get_option('xyz_smap_application_id');
	if ($post_twitter_image_permission==0)
	$post_twitter_image_permission=get_option('xyz_smap_twpost_image_permission');
	if(isset($_POST['xyz_smap_twpost_image_permission']))
		$post_twitter_image_permission=intval($_POST['xyz_smap_twpost_image_permission']);
		$xyz_smap_tw_app_sel_mode=get_option('xyz_smap_tw_app_sel_mode');
	$xyz_smap_secret_key_tw=get_option('xyz_smap_secret_key_tw');
		////////////////////////

	////////////tb////////////
	//$tbaf=get_option('xyz_smap_tb_af');
	$tmbappid=get_option('xyz_smap_tbconsumer_id');
	$tmbappsecret=get_option('xyz_smap_tbconsumer_secret');
	$tbid=get_option('xyz_smap_tb_id');
	$tbaccess_token=get_option('xyz_smap_current_tbappln_token');
	$tbaccess_token_secret=get_option('xyz_smap_tbaccestok_secret');
	if ($tbmessagetopost=='')
	   $tbmessagetopost=get_option('xyz_smap_tbmessage');
	if(isset($_POST['xyz_smap_tbmessage']))
	    $tbmessagetopost=sanitize_textarea_field($_POST['xyz_smap_tbmessage']);
	if ($post_tumblr_media_permission==0)
	    $post_tumblr_media_permission=get_option('xyz_smap_tbpost_media_permission');
	if(isset($_POST['xyz_smap_tbpost_media_permission']))
	    $post_tumblr_media_permission=intval($_POST['xyz_smap_tbpost_media_permission']);
	///////////////////////////
	////////////fb///////////
	$app_name=get_option('xyz_smap_application_name');
	$appsecret=get_option('xyz_smap_application_secret');
	$useracces_token=get_option('xyz_smap_fb_token');

	if ($message=='')
	$message=get_option('xyz_smap_message');
	if(isset($_POST['xyz_smap_message']))
		$message=$_POST['xyz_smap_message'];
	//$fbid=get_option('xyz_smap_fb_id');
	if ($posting_method==0)
	$posting_method=get_option('xyz_smap_po_method');
	if(isset($_POST['xyz_smap_po_method']))
		$posting_method=intval($_POST['xyz_smap_po_method']);
		//////////////////////////////

		/////////////////ig///////////
		$igapp_name=get_option('xyz_smap_igapplication_name');
		$igappsecret=get_option('xyz_smap_igapplication_secret');
		$iguseracces_token=get_option('xyz_smap_ig_token');
		$igappid=get_option('xyz_smap_igapplication_id');
		if ($igmessagetopost=='')
		    $igmessagetopost=get_option('xyz_smap_igmessage');
	    if(isset($_POST['xyz_smap_igmessage']))
	        $igmessagetopost=$_POST['xyz_smap_igmessage'];
		$xyz_smap_ig_app_sel_mode=get_option('xyz_smap_ig_app_sel_mode');
		$xyz_smap_secret_key_ig=get_option('xyz_smap_secret_key_ig');
		///////////////////////////////

	////////////linkedin////////////

	$lnappikey=get_option('xyz_smap_lnapikey');
	$lnapisecret=get_option('xyz_smap_lnapisecret');
	if ($lmessagetopost=='')
	$lmessagetopost=get_option('xyz_smap_lnmessage');
	if(isset($_POST['xyz_smap_lnmessage']))
		$lmessagetopost=$_POST['xyz_smap_lnmessage'];

	if ($ln_posting_method==0)
		$ln_posting_method=get_option('xyz_smap_lnpost_method');
	if(isset($_POST['xyz_smap_lnpost_method']))
		$ln_posting_method=$_POST['xyz_smap_lnpost_method'];
  if ($xyz_smap_ln_shareprivate==0)
  $xyz_smap_ln_shareprivate=get_option('xyz_smap_ln_shareprivate');
  if(isset($_POST['xyz_smap_ln_shareprivate']))
  $xyz_smap_ln_shareprivate=intval($_POST['xyz_smap_ln_shareprivate']);
//  if ($xyz_smap_lnpost_method==0)
//   $xyz_smap_ln_sharingmethod=get_option('xyz_smap_ln_sharingmethod');
//   if(isset($_POST['xyz_smap_ln_sharingmethod']))
//   $xyz_smap_ln_sharingmethod=intval($_POST['xyz_smap_ln_sharingmethod']);

    $lnaf=get_option('xyz_smap_lnaf');
/////////////////tg///////////
$xyz_smap_tgapplication_name=get_option('xyz_smap_tgapplication_name');
$xyz_smap_bot_token=get_option('xyz_smap_bot_token');
$xyz_smap_bot_username=get_option('xyz_smap_bot_username');
$tguseracces_token=get_option('xyz_smap_tg_token');
if ($tgmessagetopost=='')
	$tgmessagetopost=get_option('xyz_smap_tgmessage');
if ($tg_posting_method==0)
	$tg_posting_method=get_option('xyz_smap_tgpost_method');
if(isset($_POST['xyz_smap_tgpost_method']))
	$tg_posting_method=$_POST['xyz_smap_tgpost_method'];
if(isset($_POST['xyz_smap_tgmessage']))
	$tgmessagetopost=$_POST['xyz_smap_tgmessage'];

	/////////////threads//////////
	$thappid=get_option('xyz_smap_th_app_id');
	$thappsecret=get_option('xyz_smap_th_app_secret');
	$th_user_id=get_option('xyz_smap_th_user_id');
	$thuseracces_token=get_option('xyz_smap_th_access_token');
	$auth_data = json_decode($thuseracces_token, true);
		$thaccess_token=$auth_data['access_token'];  
	$user_profile_name=get_option('xyz_smap_th_username');

	if ($thmessagetopost=='')
	$thmessagetopost=get_option('xyz_smap_thmessage');
	if(isset($_POST['xyz_smap_thmessage']))
		$thmessagetopost=$_POST['xyz_smap_thmessage'];

	if ($xyz_smap_thpost_method==0)
	$xyz_smap_thpost_method=get_option('xyz_smap_thpost_method');
	if(isset($_POST['xyz_smap_thpost_method']))
		$xyz_smap_thpost_method=intval($_POST['xyz_smap_thpost_method']);
		////////////////////////


	$postpp= get_post($post_ID);global $wpdb;
	$reg_exUrl = "/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	$entries0 = $wpdb->get_results($wpdb->prepare( 'SELECT user_nicename,display_name FROM '.$wpdb->base_prefix.'users WHERE ID =%d',$postpp->post_author));
	foreach( $entries0 as $entry ) {
		$user_nicename=$entry->user_nicename;
		$display_name=$entry->display_name;
	}

	if ($postpp->post_status == 'publish')
	{
		$posttype=$postpp->post_type;
		$fb_publish_status=array();
		$ln_publish_status=array();
		$tw_publish_status=array();
		$tg_publish_status=array();
		$th_publish_status=array();
		if ($posttype=="page")
		{

			$xyz_smap_include_pages=get_option('xyz_smap_include_pages');
			if($xyz_smap_include_pages==0)
			{$_POST=$_POST_CPY;return;}
		}

		else if($posttype=="post")
		{
			$xyz_smap_include_posts=get_option('xyz_smap_include_posts');
			if($xyz_smap_include_posts==0)
			{
				$_POST=$_POST_CPY;return;
			}

			$xyz_smap_include_categories=get_option('xyz_smap_include_categories');
			if($xyz_smap_include_categories!="All")
			{
				$carr1=explode(',',$xyz_smap_include_categories);

				$defaults = array('fields' => 'ids');
				$carr2=wp_get_post_categories( $post_ID, $defaults );
				$retflag=1;
				foreach ($carr2 as $key=>$catg_ids)
				{
					if(in_array($catg_ids, $carr1))
						$retflag=0;
				}
				if($retflag==1)
				{$_POST=$_POST_CPY;return;}
			}
		}
		else
		{
			$xyz_smap_include_customposttypes=get_option('xyz_smap_include_customposttypes');
			if($xyz_smap_include_customposttypes!='')
			{
				$carr=explode(',', $xyz_smap_include_customposttypes);

				if(!in_array($posttype, $carr))
				{
					$_POST=$_POST_CPY;return;
				}
			}
			else
			{
				$_POST=$_POST_CPY;return;
			}
		}
		$get_post_meta=get_post_meta($post_ID,"xyz_smap",true);
		if($get_post_meta!=1)
			add_post_meta($post_ID, "xyz_smap", "1");
		include_once ABSPATH.'wp-admin/includes/plugin.php';
		$pluginName = 'bitly/bitly.php';

		if (is_plugin_active($pluginName)) {
			remove_all_filters('post_link');
		}
		$link = get_permalink($postpp->ID);
		$xyz_smap_apply_filters=get_option('xyz_smap_std_apply_filters');
		$ar2=explode(",",$xyz_smap_apply_filters);
		$con_flag=$exc_flag=$tit_flag=0;
		if(isset($ar2))
		{
			if(in_array(1, $ar2)) $con_flag=1;
			if(in_array(2, $ar2)) $exc_flag=1;
			if(in_array(3, $ar2)) $tit_flag=1;
		}

		$content = $postpp->post_content;
		if($con_flag==1)
			$content = apply_filters('the_content', $content);
		$content = html_entity_decode($content, ENT_QUOTES, get_bloginfo('charset'));
		$excerpt = $postpp->post_excerpt;
		if($exc_flag==1)
			$excerpt = apply_filters('the_excerpt', $excerpt);
		$excerpt = html_entity_decode($excerpt, ENT_QUOTES, get_bloginfo('charset'));
		$content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $content);
		$content=  preg_replace("/\\[caption.*?\\].*?\\[.caption\\]/is", "", $content);
		$content = preg_replace('/\[.+?\]/', '', $content);
		$excerpt = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $excerpt);

		if($excerpt=="")
		{
			if($content!="")
			{
				$content1=$content;
				$content1=strip_tags($content1);
				$content1=strip_shortcodes($content1);

				$excerpt=implode(' ', array_slice(explode(' ', $content1), 0, 50));
			}
		}
		else
		{
			$excerpt=strip_tags($excerpt);
			$excerpt=strip_shortcodes($excerpt);
		}
		$description = $content;

		$description_org=$description;
		$attachmenturl=xyz_smap_getimage($post_ID, $postpp->post_content);
		if(!empty($attachmenturl))
			$image_found=1;
		else
			$image_found=0;

		$name = $postpp->post_title;
		$caption=get_bloginfo('title');
		$caption = html_entity_decode($caption, ENT_QUOTES, get_bloginfo('charset'));

		if($tit_flag==1)
			$name = apply_filters('the_title', $name,$post_ID);
		$name = html_entity_decode($name, ENT_QUOTES, get_bloginfo('charset'));
		$name=strip_tags($name);
		$name=strip_shortcodes($name);

		$description=strip_tags($description);
		$description=strip_shortcodes($description);
	 	$description=str_replace("&nbsp;","",$description);

		$excerpt=str_replace("&nbsp;","",$excerpt);
		$xyz_smap_app_sel_mode=get_option('xyz_smap_app_sel_mode');
		$af=get_option('xyz_smap_af');
		$ig_af=get_option('xyz_smap_ig_af');
		if((($useracces_token!="" && $appsecret!="" && $appid!=""&& $xyz_smap_app_sel_mode==0) || $xyz_smap_app_sel_mode==1) && $post_permissin==1 && $af ==0)
		{
			$xyz_smap_clear_fb_cache=get_option('xyz_smap_clear_fb_cache');
			$user_page_id=get_option('xyz_smap_fb_numericid');
			if ($xyz_smap_app_sel_mode==1){
				$xyz_smap_page_names=json_decode(stripslashes(get_option('xyz_smap_page_names')));
				foreach ($xyz_smap_page_names as $xyz_smap_page_id => $xyz_smap_page_name)
				{
					$xyz_smap_pages_ids1[]=$xyz_smap_page_id;
				}
			}
			else{
			$xyz_smap_pages_ids=get_option('xyz_smap_pages_ids');

			$xyz_smap_pages_ids1=explode(",",$xyz_smap_pages_ids);

			}
			foreach ($xyz_smap_pages_ids1 as $key=>$value)
			{
				if ($xyz_smap_app_sel_mode==0){
					$value1=explode("-",$value);
					$acces_token=$value1[1];$page_id=$value1[0];
				}
				else
					$page_id=$value;
				if ($xyz_smap_app_sel_mode==0)
					require_once( dirname( __FILE__ ) . '/../api/facebook.php');
				if($xyz_smap_clear_fb_cache==1 && $xyz_smap_app_sel_mode== 0 && ($posting_method==2 || $posting_method==1))
				{
					xyz_smap_clear_open_graph_cache($link,$acces_token,$appid,$appsecret);
				}
				$message1=str_replace('{POST_TITLE}', $name, $message);
				$message2=str_replace('{BLOG_TITLE}', $caption,$message1);
				$message3=str_replace('{PERMALINK}', ' '.$link.' ', $message2);
				$message4=str_replace('{POST_EXCERPT}', $excerpt, $message3);
				$message5=str_replace('{POST_CONTENT}', $description, $message4);
				$message5=str_replace('{USER_NICENAME}', $user_nicename, $message5);
				$message5=str_replace('{POST_ID}', $post_ID, $message5);
				$publish_time=get_the_time(get_option('date_format'),$post_ID );
				$message5=str_replace('{POST_PUBLISH_DATE}', $publish_time, $message5);
				$message5=str_replace('{USER_DISPLAY_NAME}', $display_name, $message5);
				$message5=str_replace("&nbsp;","",$message5);
				$smap_sslverify= get_option('xyz_smap_peer_verification')=='1' ? true : false;
               $disp_type="feed";
				if($posting_method==1) //attach
				{
					$attachment = array('message' => $message5,
							'link' => $link,
							'actions' => json_encode(array('name' => $name,
							'link' => $link))

					);
				}
				else if($posting_method==2)  //share link
				{
					$attachment = array('message' => $message5,
							'link' => $link,

					);
				}
				else if($posting_method==3) //simple text message
				{

					$attachment = array('message' => $message5,

					);

				}
				else if($posting_method==4 || $posting_method==5) //text message with image 4 - app album, 5-timeline
				{
					if(!empty($attachmenturl))
					{
						if($xyz_smap_app_sel_mode==0)
						{
							try{
							$album_fount=0;$error_net=0;
							$xyz_fb_params[0]=$page_id;
							$xyz_fb_params[1]='albums';
							$xyz_fb_params[2]=$acces_token;
							$xyz_fb_params[3] = $smap_sslverify;
							$xyz_fb_params_enc=json_encode($xyz_fb_params);
							$result=xyz_smap_fb_get_album($xyz_fb_params_enc);
							$result = json_decode($result,true);
							if (isset($result['error']['message']))
								$fb_publish_status[].="<span style=\"color:red\">  ".$page_id."/".$disp_type."/".($result['error']['message'])."<br/>";
							}
						catch (Exception $e){
								$fb_publish_status[$page_id."/albums"]=$e->getMessage();
									}
						if(isset($result))
							{
							if($posting_method==5)
							{
								foreach ($result['data'] as $album) {
									if (isset($album['name']) && $album['name'] == "Timeline photos") {
																					$album_fount=1;$timeline_album = $album; break;										}
									}
								if (isset($timeline_album) && isset($timeline_album['id'])) $page_id = $timeline_album['id'];
								if($album_fount==0)
										$fb_publish_status[$page_id."/albums"]='<span style=\"color:red\">Invalid album name<span><br>';
								}
							else{
								foreach ($result['data'] as $album)
									if (isset($album['name']) && $album['name'] == $app_name) {
										$album_fount=1;$app_album = $album;break;
							}
								if (isset($app_album) && isset($app_album['id'])) $page_id = $app_album['id'];
							if($album_fount==0)
									$fb_publish_status[$page_id."/albums"]='<span style=\"color:red\">Invalid album name<span>';
							}
						}
						else{
							$error_net=1;
							$fb_publish_status[].="<span style=\"color:red\">  ".$page_id."/".$disp_type."/Check network connection <br/>";
							}
							}							
						$disp_type="photos";
						$attachment = array('message' => $message5,
								'url' => $attachmenturl
						);
					}
					else
					{
						$attachment = array('message' => $message5,

						);
					}
				}
				if($posting_method==1 || $posting_method==2)
				{
					update_post_meta($post_ID, "xyz_smap_insert_og", "1");
				}
				try{
					if($xyz_smap_app_sel_mode==1)
					{
						$post_id_string="";
						$smap_smapsoln_userid=get_option('xyz_smap_smapsoln_userid');
						$xyz_smap_secret_key=get_option('xyz_smap_secret_key');
						$xyz_smap_fb_numericid=get_option('xyz_smap_fb_numericid');
						$xyz_smap_xyzscripts_userid=get_option('xyz_smap_xyzscripts_user_id');
						$post_details=array('xyz_smap_userid'=>$smap_smapsoln_userid,//smap_id
								'xyz_smap_attachment'=>$attachment,
								'xyz_smap_disp_type'=>$disp_type,
								'xyz_smap_posting_method'=>$posting_method,
								'xyz_smap_page_id'=>$page_id,
								'xyz_smap_app_name'=>$app_name,
								'xyz_fb_numericid' => $xyz_smap_fb_numericid,
								'xyz_smap_xyzscripts_userid'=>$xyz_smap_xyzscripts_userid,
								'xyz_smap_clear_fb_cache'=>$xyz_smap_clear_fb_cache
						);
						$url=XYZ_SMAP_SOLUTION_PUBLISH_URL.'api/facebook.php';
						$result_smap_solns=xyz_smap_post_to_smap_api($post_details,$url,$xyz_smap_secret_key);
						$result_smap_solns=json_decode($result_smap_solns);
						if(!empty($result_smap_solns))
						{
							$fb_api_count_returned=$result_smap_solns->fb_api_count;
							if($result_smap_solns->status==0)
								$fb_publish_status[].="<span style=\"color:red\">  ".$page_id."/".$disp_type."/".$result_smap_solns->msg."</span><br/><span style=\"color:#21759B\">No. of api calls used: ".$fb_api_count_returned."</span><br/>";
								elseif ($result_smap_solns->status==1)
								{
								if (isset($result_smap_solns->postid) && !empty($result_smap_solns->postid)){
									$fb_postid =$result_smap_solns->postid;
									if (strpos($fb_postid, '_') !== false) {
										$fb_post_id_explode=explode('_', $fb_postid);
										$link_to_fb_post='https://www.facebook.com/'.$fb_post_id_explode[0].'_'.$fb_post_id_explode[1];
										// $link_to_fb_post='https://www.facebook.com/'.$fb_post_id_explode[0].'/posts/'.$fb_post_id_explode[1];
									}
									else {
										$link_to_fb_post='https://www.facebook.com/'.$page_id.'_'.$fb_postid;
										// $link_to_fb_post='https://www.facebook.com/'.$page_id.'/posts/'.$fb_postid;
									}
									$post_id_string="<span style=\"color:#21759B;text-decoration:underline;\"><a  target=\"_blank\" href=".$link_to_fb_post.">View Post</a></span>";
								}


								$fb_publish_status[].="<span style=\"color:green\"> ".$page_id."/".$disp_type."/".$result_smap_solns->msg."</span><br/><span style=\"color:#21759B\">No. of api calls used: ".$fb_api_count_returned."</span><br/>".$post_id_string."<br/>";
								}
						}
					}
					else
					{
						$attachment['access_token']=$acces_token;
						$xyz_fb_params[0]=$page_id;
						$xyz_fb_params[1]=$disp_type;
						$xyz_fb_params[2]=$attachment;
						$xyz_fb_params[3] = $smap_sslverify;
						$xyz_fb_params_enc=json_encode($xyz_fb_params);
						$result=xyz_smap_make_fb_post($xyz_fb_params_enc);
						$result = json_decode($result, true);
						if (isset($result['error']['message'])) {
							$fb_publish_status[].="<span style=\"color:red\">  ".$page_id."/".$disp_type."/".($result['error']['message'])."<br/>";
						}
						$post_id_string_from_ownApp='';
						if(!empty($result))
						{
							$fb_postid = $result['id'];
							if (!empty($fb_postid)){
								if (strpos($fb_postid, '_') !== false) {
									$fb_post_id_explode=explode('_', $fb_postid);
									$link_to_fb_post='https://www.facebook.com/'.$fb_post_id_explode[0].'_'.$fb_post_id_explode[1];
								}
								else {
									$link_to_fb_post='https://www.facebook.com/'.$page_id.'_'.$fb_postid;
								}
								$post_id_string_from_ownApp="<span style=\"color:#21759B;text-decoration:underline;\"><a target=\"_blank\" href=".$link_to_fb_post."> View Post</a></span>";
								$fb_publish_status[]="<span style=\"color:green\">Success</span><br/>".$post_id_string_from_ownApp;
							}
						}
						else{
							if($error_net!=1)
						$fb_publish_status[].="<span style=\"color:red\">  ".$page_id."/".$disp_type."/Check network connection <br/>";
						}
					}
				}
							catch(Exception $e)
							{
								$fb_publish_status[]="<span style=\"color:red\">  ".$page_id."/".$disp_type."/".$e->getMessage()."</span><br/>";
							}
			}

			if(!empty($fb_publish_status))
			  $fb_publish_status_insert=serialize($fb_publish_status);
			else
			{
				$fb_publish_status[]="<span style=\"color:green\">Success</span><br/>".$post_id_string_from_ownApp;
				$fb_publish_status_insert=serialize($fb_publish_status);
			}

			$time=time();
			$post_fb_options=array(
					'postid'	=>	$post_ID,
					'acc_type'	=>	"Facebook",
					'publishtime'	=>	$time,
					'status'	=>	$fb_publish_status_insert
			);

			$smap_fb_update_opt_array=array();

			$smap_fb_arr_retrive=(get_option('xyz_smap_fbap_post_logs'));

			$smap_fb_update_opt_array[0]=isset($smap_fb_arr_retrive[0]) ? $smap_fb_arr_retrive[0] : '';
			$smap_fb_update_opt_array[1]=isset($smap_fb_arr_retrive[1]) ? $smap_fb_arr_retrive[1] : '';
			$smap_fb_update_opt_array[2]=isset($smap_fb_arr_retrive[2]) ? $smap_fb_arr_retrive[2] : '';
			$smap_fb_update_opt_array[3]=isset($smap_fb_arr_retrive[3]) ? $smap_fb_arr_retrive[3] : '';
			$smap_fb_update_opt_array[4]=isset($smap_fb_arr_retrive[4]) ? $smap_fb_arr_retrive[4] : '';
			$smap_fb_update_opt_array[5]=isset($smap_fb_arr_retrive[5]) ? $smap_fb_arr_retrive[5] : '';
			$smap_fb_update_opt_array[6]=isset($smap_fb_arr_retrive[6]) ? $smap_fb_arr_retrive[6] : '';
			$smap_fb_update_opt_array[7]=isset($smap_fb_arr_retrive[7]) ? $smap_fb_arr_retrive[7] : '';
			$smap_fb_update_opt_array[8]=isset($smap_fb_arr_retrive[8]) ? $smap_fb_arr_retrive[8] : '';
			$smap_fb_update_opt_array[9]=isset($smap_fb_arr_retrive[9]) ? $smap_fb_arr_retrive[9] : '';
			array_shift($smap_fb_update_opt_array);
			array_push($smap_fb_update_opt_array,$post_fb_options);
			update_option('xyz_smap_fbap_post_logs', $smap_fb_update_opt_array);




		}
		if((($iguseracces_token!="" && $igappsecret!="" && $igappid!="" && $xyz_smap_ig_app_sel_mode==0) || ($xyz_smap_ig_app_sel_mode==1 && $xyz_smap_secret_key_ig !='')) && $igpost_permission==1  && $ig_af==0 )
		{
		    require_once( dirname( __FILE__ ) .'/../api/insta.php' );
		    $user_page_id=get_option('xyz_smap_ig_numericid');
		    $ig_publish_status_insert='';
		    $xyz_smap_xyzscripts_userid=$api_exceed_err=$remaining_ig_api_count=0; $ig_api_count=0;
		    $xyz_smap_ig_app_sel_mode=get_option('xyz_smap_ig_app_sel_mode');
		    if ($xyz_smap_ig_app_sel_mode==1){
		        $xyz_smap_page_names=json_decode(stripslashes(get_option('xyz_smap_ig_page_names')));
		        foreach ($xyz_smap_page_names as $xyz_smap_page_id => $xyz_smap_page_name)
		        {
		            $xyz_smap_ig_pages_ids1[]=$xyz_smap_page_id;
		        }
		    }
		    else{
		        $xyz_smap_ig_pages_ids=get_option('xyz_smap_ig_pages_ids');

		        $xyz_smap_ig_pages_ids1=explode(",",$xyz_smap_ig_pages_ids);
		    }
		    foreach ($xyz_smap_ig_pages_ids1 as $key=>$value)
		    {

		        if ($xyz_smap_ig_app_sel_mode==0){
		            $value1=explode("-",$value);
		            $acces_token=$value1[1];$ig_id=$value1[2];
		        }
		        else
		        {
		            if($xyz_smap_ig_app_sel_mode==1){
		                $value1=explode("-",$value);
		                $ig_id=$value1[0];
		            }
		        }
		        $message1=str_replace('{POST_TITLE}', $name, $igmessagetopost);
	            $message2=str_replace('{BLOG_TITLE}', $caption,$message1);
	            $message3=str_replace('{PERMALINK}', ' '.$link.' ', $message2);
	            $message4=str_replace('{POST_EXCERPT}', $excerpt, $message3);
	            $message5=str_replace('{POST_CONTENT}', $description, $message4);
	            $message5=str_replace('{USER_NICENAME}', $user_nicename, $message5);
	            $message5=str_replace('{POST_ID}', $post_ID, $message5);
	            $publish_time=get_the_time(get_option('date_format'),$post_ID );
	            $message5=str_replace('{POST_PUBLISH_DATE}', $publish_time, $message5);
	            $message5=str_replace('{USER_DISPLAY_NAME}', $display_name, $message5);
	            $message5=str_replace("&nbsp;","",$message5);
	            //$attachmenturl='https://img.photographyblog.com/reviews/samsung_galaxy_note_20_ultra/photos/samsung_galaxy_note_20_ultra_02.jpg';
				$message5=xyz_smap_string_limit($message5, 2200);
	            if(!empty($attachmenturl))
	            {
                if($xyz_smap_ig_app_sel_mode==1)
                {
                    $xyz_smap_xyzscripts_userid=get_option('xyz_smap_xyzscripts_user_id');
                    $xyz_smap_smapsoln_userid_ig=get_option('xyz_smap_smapsoln_userid_ig');
                    $xyz_smap_smapsoln_sec_key_ig=get_option('xyz_smap_secret_key_ig');
                    $xyz_smap_token_fetch=1;
                    $post_details=array('xyz_smap_userid'=>$xyz_smap_smapsoln_userid_ig,
                        'xyz_smap_connected_fb_pageid'=>$ig_id,
                        'xyz_smap_xyzscripts_userid'=>$xyz_smap_xyzscripts_userid,
                        'xyz_smap_token_fetch'=>$xyz_smap_token_fetch
                    );
                    $url=XYZ_SMAP_SOLUTION_IG_PUBLISH_URL.'api/publish.php';
                    $result=xyz_smap_post_to_smap_api($post_details,$url,$xyz_smap_smapsoln_sec_key_ig);
                    $result=json_decode($result);
                    if(!empty($result))
                    {
                        if (isset($result->status))
                        {
                            if($result->status==0)
                            {
                                $err=$result->msg;
                                $ig_publish_status_insert.="<span style=\"color:red\">statuses/update : ".$err."</span>";
                            }
                            elseif ($result->status==1 && isset($result->business_acc_id))
                            {
                                $acces_token=$result->access_token;
                                $ig_id=$business_acc_id=$result->business_acc_id;
                                $remaining_ig_api_count=$result->ig_api_count;
                            }
                        }
                    }
                }
                if ($xyz_smap_ig_app_sel_mode==1)
                {
                    $required_api_count=2;
                    if (($remaining_ig_api_count-$required_api_count)<1)
                    {
		                            $api_exceed_err=1;goto ig_api_exceed_err;
                    }
                }
                $xyz_media_param[0]=$acces_token;
                $xyz_media_param[1]=$ig_id;
                $xyz_media_param[2]=$attachmenturl;
                $xyz_media_param[3]=$message5;
                $xyz_media_param_enc=json_encode($xyz_media_param);
                $xyz_ig_container_result=xyz_smap_ig_create_media_container($xyz_media_param_enc);
                $xyz_ig_container_result=json_decode($xyz_ig_container_result);
                if($xyz_ig_container_result!=NULL)
                {
                    if(isset($xyz_ig_container_result->error) && $xyz_ig_container_result->error!=NULL){
                        $err_msg=$xyz_ig_container_result->error;
                        $ig_publish_status_insert.="<span style=\"color:red\">Image : Failed.".$err_msg->message."</span><br/><span style=\"color:#21759B\">No. of api calls used: ".$ig_api_count."</span><br/>";
                    }
                    else if(isset($xyz_ig_container_result->id) && $xyz_ig_container_result->id!=NULL){
                        $ig_api_count++;
                        $xyz_media_param[2]=$xyz_ig_container_result->id;
                        $xyz_media_param_enc=json_encode($xyz_media_param);
                        $xyz_ig_publish_result=xyz_smap_ig_publish_media($xyz_media_param_enc);
                        $xyz_ig_publish_result=json_decode($xyz_ig_publish_result);
                        if($xyz_ig_publish_result!=NULL)
                        {
                            if(isset($xyz_ig_publish_result->error) && $xyz_ig_publish_result->error!=NULL)
                            {
                                $err_msg=$xyz_ig_container_result->error;
                                $ig_publish_status_insert.="<span style=\"color:red\">Image : Failed.".$err_msg->message."</span><br/><span style=\"color:#21759B\">No. of api calls used: ".$ig_api_count."</span><br/>";
                            }
                            else if(isset($xyz_ig_publish_result->id) && $xyz_ig_publish_result->id!=NULL)
                            {
                                $token_url = "https://graph.facebook.com/".XYZ_SMAP_IG_API_VERSION."/".$xyz_ig_publish_result->id."?fields=permalink&access_token=".$acces_token;
                                $response = wp_remote_get($token_url,array('sslverify'=> (get_option('xyz_smap_peer_verification')=='1') ? true : false));
                                if(is_array($response) && (isset($response['body'])))
                                {
                                    $params= json_decode($response['body']);
                                    if(isset($params->permalink))
                                        $insta_post_link = $params->permalink;
                                }
                                $ig_publish_status_insert.="<span style=\"color:green\">Image : Success".$xyz_ig_publish_result->id."</span><br/><span style=\"color:#21759B\">No. of api calls used: ".$ig_api_count."</span><br/><span style=\"color:#21759B;text-decoration:underline;\"><a  target=\"_blank\" href=".$insta_post_link.">View Post</a></span><br/>";
                            }
                       }
                    }
                }
                else
                    $ig_publish_status_insert.="<span style=\"color:red\">Image : Failed, check network connection </span>";
		                }
		      else
		         $ig_publish_status_insert.="<span style=\"color:red\">Image : Image  not found </span>";
// 	            }


                if($xyz_smap_ig_app_sel_mode==1)
                {
                    $xyz_smap_token_fetch=0;
                    $xyz_smap_xyzscripts_userid=get_option('xyz_smap_xyzscripts_user_id');
                    $xyz_smap_smapsoln_userid_ig=get_option('xyz_smap_smapsoln_userid_ig');
                    $xyz_smap_smapsoln_sec_key_ig=get_option('xyz_smap_secret_key_ig');
                    $post_details=array('xyz_smap_userid'=>$xyz_smap_smapsoln_userid_ig,
                        'xyz_smap_xyzscripts_userid'=>$xyz_smap_xyzscripts_userid,
                        'xyz_smap_token_fetch'=>$xyz_smap_token_fetch,
                        'ig_response_from_plugin'=>$ig_publish_status_insert,
                        'ig_api_count_from_plugin'=>$ig_api_count
                    );
                    $url=XYZ_SMAP_SOLUTION_IG_PUBLISH_URL.'api/publish.php';
                    $result=xyz_smap_post_to_smap_api($post_details,$url,$xyz_smap_smapsoln_sec_key_ig);
                }
		    }
		    ig_api_exceed_err:
		    if($api_exceed_err==1){
		        $ig_publish_status_insert= "<span style=\"color:red\"> Daily API count limit exceeded,only ".$remaining_ig_api_count." api calls left.</span>";//1;
		    }
		    if(!empty($ig_publish_status_insert))
		    	$ig_publish_status_insert=serialize($ig_publish_status_insert);

		        $time=time();
		        $post_ig_options=array(
		            'postid'	=>	$post_ID,
		            'acc_type'	=>	"Instagram",
		            'publishtime'	=>	$time,
		            'status'	=>	$ig_publish_status_insert
		        );

		        $smap_ig_update_opt_array=array();

		        $smap_ig_arr_retrive=(get_option('xyz_smap_igap_post_logs'));

		        $smap_ig_update_opt_array[0]=isset($smap_ig_arr_retrive[0]) ? $smap_ig_arr_retrive[0] : '';
		        $smap_ig_update_opt_array[1]=isset($smap_ig_arr_retrive[1]) ? $smap_ig_arr_retrive[1] : '';
		        $smap_ig_update_opt_array[2]=isset($smap_ig_arr_retrive[2]) ? $smap_ig_arr_retrive[2] : '';
		        $smap_ig_update_opt_array[3]=isset($smap_ig_arr_retrive[3]) ? $smap_ig_arr_retrive[3] : '';
		        $smap_ig_update_opt_array[4]=isset($smap_ig_arr_retrive[4]) ? $smap_ig_arr_retrive[4] : '';
		        $smap_ig_update_opt_array[5]=isset($smap_ig_arr_retrive[5]) ? $smap_ig_arr_retrive[5] : '';
		        $smap_ig_update_opt_array[6]=isset($smap_ig_arr_retrive[6]) ? $smap_ig_arr_retrive[6] : '';
		        $smap_ig_update_opt_array[7]=isset($smap_ig_arr_retrive[7]) ? $smap_ig_arr_retrive[7] : '';
		        $smap_ig_update_opt_array[8]=isset($smap_ig_arr_retrive[8]) ? $smap_ig_arr_retrive[8] : '';
		        $smap_ig_update_opt_array[9]=isset($smap_ig_arr_retrive[9]) ? $smap_ig_arr_retrive[9] : '';
		        array_shift($smap_ig_update_opt_array);
		        array_push($smap_ig_update_opt_array,$post_ig_options);
		        update_option('xyz_smap_igap_post_logs', $smap_ig_update_opt_array);
		}
		if((($taccess_token!="" && $taccess_token_secret!="" && $tappid!="" && $tappsecret!="" && $xyz_smap_tw_app_sel_mode==0)||($xyz_smap_tw_app_sel_mode==2  && $tauthToken!='' && $tw_af!=1)) && $post_twitter_permission==1)
		{
			////image up start///
			$img_status="";
			$api_exceed_err=$remaining_tw_api_count=0;
			if($post_twitter_image_permission==1)
			{
			    update_post_meta($post_ID, "xyz_smap_insert_twitter_card", "1");
				$img=array();
				if(!empty($attachmenturl))
					$img = wp_remote_get($attachmenturl,array('sslverify'=> (get_option('xyz_smap_peer_verification')=='1') ? true : false));

				if(is_array($img) && ! is_wp_error( $img ) )
				{
					if (isset($img['body'])&& trim($img['body'])!='')
					{
						$image_found = 1;
							if (($img['headers']['content-length']) && trim($img['headers']['content-length'])!='')
							{
							$img_size_bytes=$img['headers']['content-length'];
								$img_size=$img['headers']['content-length']/(1024*1024);
								if($img_size>3){$image_found=0;$img_status="Image skipped(greater than 3MB)";}
							}

						$img = $img['body'];
						///////////////////////Create temp folder ß
											$wp_smap_img_targetfolder = realpath(dirname(__FILE__) . '/../../../')."/uploads/xyz_smap_temp_images";
											if (file_exists($wp_smap_img_targetfolder)==false)
											{
												if (mkdir($wp_smap_img_targetfolder, 0777, true))
												{
													chmod($wp_smap_img_targetfolder,0777);
												}
											}
											////////////upload image to temporary folder and get path
											$xyz_smap_ext = pathinfo($attachmenturl, PATHINFO_EXTENSION);
											$xyz_smap_filename=pathinfo($attachmenturl, PATHINFO_FILENAME);
											$xyz_smap_image_files=$wp_smap_img_targetfolder."/".$xyz_smap_filename.".".$xyz_smap_ext;
										  file_put_contents($xyz_smap_image_files, $img);

											////////////////////////////
					}
					else
						$image_found = 0;
				}
				else {
					$image_found=0;
				}
			}
			///Twitter upload image end/////
			$messagetopost=str_replace("&nbsp;","",$messagetopost);
			$xyz_smap_xyzscripts_userid=0;$xyz_smap_smapsoln_userid_tw=$xyz_smap_smapsoln_sec_key='';
			$substring="";$islink=0;$issubstr=0;

			$substring=xyz_smap_split_replace('{POST_TITLE}', $name, $messagetopost);
			$substring=str_replace('{BLOG_TITLE}', $caption,$substring);
			$substring=str_replace('{PERMALINK}', ' '.$link.' ', $substring);
			$substring=xyz_smap_split_replace('{POST_EXCERPT}', $excerpt, $substring);
			$substring=xyz_smap_split_replace('{POST_CONTENT}', $description, $substring);
			$substring=str_replace('{USER_NICENAME}', $user_nicename, $substring);
			$substring=str_replace('{POST_ID}', $post_ID, $substring);
			$publish_time=get_the_time(get_option('date_format'),$post_ID );
			$substring=str_replace('{POST_PUBLISH_DATE}', $publish_time, $substring);
			$substring=str_replace('{USER_DISPLAY_NAME}', $display_name,$substring );
			preg_match_all($reg_exUrl,$substring,$matches); // @ is same as /
			if(is_array($matches) && isset($matches[0]))
			{
				$matches=$matches[0];
				$final_str='';
				$len=0;
			    $tw_max_len=get_option('xyz_smap_twtr_char_limit');
                if (function_exists('mb_strlen') && function_exists('mb_strpos') && function_exists('mb_substr')) {
				foreach ($matches as $key=>$val)
				{
						$url_max_len=23;//23 for https and 22 for http
					$messagepart=mb_substr($substring, 0, mb_strpos($substring, $val));

					if(mb_strlen($messagepart)>($tw_max_len-$len))
					{
						$final_str.=mb_substr($messagepart,0,$tw_max_len-$len-3)."...";
						$len+=($tw_max_len-$len);
						break;
					}
					else
					{
						$final_str.=$messagepart;
						$len+=mb_strlen($messagepart);
					}

					$cur_url_len=mb_strlen($val);
					if(mb_strlen($val)>$url_max_len)
						$cur_url_len=$url_max_len;

					$substring=mb_substr($substring, mb_strpos($substring, $val)+strlen($val));
					if($cur_url_len>($tw_max_len-$len))
					{
						$final_str.="...";
						$len+=3;
						break;
					}
					else
					{
						$final_str.=$val;
						$len+=$cur_url_len;
					}

				}

				if(mb_strlen($substring)>0 && $tw_max_len>$len)
				{
					if(mb_strlen($substring)>($tw_max_len-$len))
					{
						$final_str.=mb_substr($substring,0,$tw_max_len-$len-3)."...";
					}
					else
					{
						$final_str.=$substring;
					}
				}
			}
			else {
				foreach ($matches as $key=>$val)
				{
					//	if(substr($val,0,5)=="https")
					$url_max_len=23;//23 for https and 22 for http
					// 					else
						// 						$url_max_len=22;//23 for https and 22 for http
						$messagepart=substr($substring, 0, strpos($substring, $val));
						if(strlen($messagepart)>($tw_max_len-$len))
						{
							$final_str.=substr($messagepart,0,$tw_max_len-$len-3)."...";
							$len+=($tw_max_len-$len);
							break;
						}
						else
						{
							$final_str.=$messagepart;
							$len+=strlen($messagepart);
						}
						$cur_url_len=strlen($val);
						if(strlen($val)>$url_max_len)
							$cur_url_len=$url_max_len;
							$substring=substr($substring, strpos($substring, $val)+strlen($val));
							if($cur_url_len>($tw_max_len-$len))
							{
								$final_str.="...";
								$len+=3;
								break;
							}
							else
							{
								$final_str.=$val;
								$len+=$cur_url_len;
							}
				}
				if(strlen($substring)>0 && $tw_max_len>$len)
				{
					if(strlen($substring)>($tw_max_len-$len))
					{
						$final_str.=substr($substring,0,$tw_max_len-$len-3)."...";
					}
					else
					{
						$final_str.=$substring;
					}
					}
				}

				$substring=$final_str;
			}
  		/* if (strlen($substring)>$tw_max_len)
                	$substring=substr($substring, 0, $tw_max_len-3)."...";*/
  		if($xyz_smap_tw_app_sel_mode==0)
			{
								$twobj = new Abraham\TwitterOAuth\TwitterOAuth(
												 $tappid,
												 $tappsecret,
												 $taccess_token,
												 $taccess_token_secret,
										 );
										 $twobj->userId = explode('-', $taccess_token)[0];
										 $twobj->setApiVersion('2');
			}
			elseif($xyz_smap_tw_app_sel_mode==2)
			{
				require_once (dirname(__FILE__) . '/../api/twitter.php');
				// Re-authenticate if 2 hours have passed since the last authorization
				$reauth_err=0;
				$current_time=time();
				$last_auth_time = get_option('xyz_smap_last_auth_time'); 
				$auth_timer = (2 * 60 * 60) - (2 * 60);
				 if ((time() - $last_auth_time) >= $auth_timer)
				 {
					$response=xyz_smap_twitter_auth2_reauth();
					if(isset($response['status']) && $response['status']=='error'){
						$reauth_err=1;
						$tw_publish_status_insert=serialize("<span style=\"color:red\">".$response['code'].':'.$response['message'].".</span>");
					}
					$tauthToken = get_option('xyz_smap_tw_token');
				}		
							}
		$tw_api_count=0;
			 $tw_publish_status='';
 			if($image_found==1 && $post_twitter_image_permission==1){ 
				if($xyz_smap_tw_app_sel_mode==0)
 			{
 				$twobj->setTimeouts( 10, 40 );
 				$twobj->setApiVersion( '1.1' );
 				$response = $twobj->upload( 'media/upload', array( 'media' => $xyz_smap_image_files ) );

 				if ( ! isset( $response->media_id ) ) {
 					$media_upload_id = 0;
 				} else {
 					$media_upload_id = $response->media_id;
 				}

 				if ( $media_upload_id ) {

 				$twobj->setTimeouts( 10, 30 );
 				$twobj->setApiVersion( '2' );
 				$resultfrtw = $twobj->post(
 					'tweets',
 					array('text' =>$substring,'media'=>array(
 							'media_ids' => [ (string) $media_upload_id ],
 						) ),
 					true
 				);

 			if ( isset( $resultfrtw->data ) && ! is_wp_error( $resultfrtw->data ) ) {
 					// Tweet posted successfully
 						$tw_publish_status="<span style=\"color:green\">statuses/update : Success.</span>";
 				} else if( is_wp_error( $resultfrtw->data )) {
 				$error_string = $resultfrtw->data->get_error_message();
 				$tw_publish_status="<span style=\"color:red\">".$error_string.".</span>";

 				}
 				else
 				{
 					if(!empty($resultfrtw->detail))
 						$tw_publish_status="<span style=\"color:red\">".$resultfrtw->status.":".$resultfrtw->detail.".</span>";
 					else
 						$tw_publish_status="<span style=\"color:red\">Not Available</span>";
 				}
 				if($img_status!="")
 					$tw_publish_status.="<span style=\"color:red\">".$img_status.".</span>";
 				}
 				else
 				{
 					$tw_publish_status="<span style=\"color:red\">statuses/update : ".serialize($response)."</span>";
 				}
 				if (is_file($xyz_smap_image_files) === true)
         {
              unlink($xyz_smap_image_files);
         }
				}
				elseif($xyz_smap_tw_app_sel_mode==2 && $reauth_err!=1){
					$response=xyz_smap_upload_media($tauthToken,$attachmenturl,$img_size_bytes);
					if($response['status']=='error'){
						$tw_publish_status="<span style=\"color:red\">".$response['code'].':'.$response['message'].".</span>";
					}
					elseif (isset($response['status']) && $response['status'] === 'success' && !empty($response['data'])) 
					{
						$mediaId=$response['data']['id'];
						$response=xyz_smap_create_post($tauthToken,$mediaId,$substring);
						if($response['status']=='error'){
							$tw_publish_status="<span style=\"color:red\">".$response['code'].':'.$response['message'].".</span>";
						}
						else{
							// $tweet_id=$response['data']['id'];
							$tw_publish_status="<span style=\"color:green\">statuses/update : Success.</span>";
						}
						if($img_status!="")
							$tw_publish_status.="<span style=\"color:red\">".$img_status.".</span>";
					}
				}		

 			}
 			else
 			{
 			    if($xyz_smap_tw_app_sel_mode==0)
 			    {
         		//	$resultfrtw = $twobj->request('POST', $twobj->url('1.1/statuses/update'), array('text' =>$substring));
 							$twobj->setTimeouts( 10, 30 );
 							$twobj->setApiVersion( '2' );
 							$resultfrtw = $twobj->post(
 								'tweets',
 								array('text' =>$substring),
 								true
 							);

 						if ( isset( $resultfrtw->data ) && ! is_wp_error( $resultfrtw->data ) ) {
 								// Tweet posted successfully
 									$tw_publish_status="<span style=\"color:green\">statuses/update : Success.</span>";

 							} else if( is_wp_error( $resultfrtw )) {
 							    // Handle error case

 										$error_string = $resultfrtw->get_error_message();
 										$tw_publish_status="<span style=\"color:red\">".$error_string.".</span>";

 							}
 							else
 							{
 								if(!empty($resultfrtw->detail))
 									$tw_publish_status="<span style=\"color:red\">".$resultfrtw->status.":".$resultfrtw->detail.".</span>";
 								else
 									$tw_publish_status="<span style=\"color:red\">Not Available</span>";
 							}
 			     }
				  elseif($xyz_smap_tw_app_sel_mode==2  && $reauth_err!=1){
					$response=xyz_smap_post_to_twitter($tauthToken,$substring);
					if($response['status']=='error'){
						$tw_publish_status="<span style=\"color:red\">".$response['code'].':'.$response['message'].".</span>";
					}
					else{
						$tweet_id=$response['data']['id'];
						$tw_publish_status="<span style=\"color:green\">statuses/update : Success.</span>";
					}				
			 	}
 			}
 			$tweet_id_string='';
 			if ($xyz_smap_tw_app_sel_mode==0)
 			{
				if(isset($resultfrtw->data))
     			$resp = $resultfrtw->data;
     		if (isset($resp->id) && !empty($resp->id)){
     				$tweet_link="https://twitter.com/".$twid."/status/".$resp->id;
     				$tweet_id_string="<br/><span style=\"color:#21759B;text-decoration:underline;\"><a target=\"_blank\" href=".$tweet_link.">View Tweet</a></span>";
     			}
     			$tw_publish_status_insert=serialize($tw_publish_status.$tweet_id_string);
 	       	}
			elseif ($xyz_smap_tw_app_sel_mode==2  && $reauth_err!=1)
			{
				if(isset($response['data']))
					$resp = $response['data'];
				if (isset($resp['id']) && !empty($resp['id'])){
					$tweet_link="https://twitter.com/".$twid."/status/".$resp['id'];
					$tweet_id_string="<br/><span style=\"color:#21759B;text-decoration:underline;\"><a target=\"_blank\" href=".$tweet_link.">View Tweet</a></span>";
				}
				$tw_publish_status_insert=serialize($tw_publish_status.$tweet_id_string);
			}
			if($xyz_smap_tw_app_sel_mode==1)
			{
			    $xyz_smap_xyzscripts_userid=get_option('xyz_smap_xyzscripts_user_id');
			    $xyz_smap_smapsoln_userid_tw=get_option('xyz_smap_smapsoln_userid_tw');
			    $xyz_smap_smapsoln_sec_key=get_option('xyz_smap_secret_key_tw');

			    $video=$tweet_id_string="";
			    $xyz_smap_publish_video_tw=$supported_urls_count=$xyz_smap_multiphoto_tweet=$count_tw=$xyz_smap_use_tw_img_desc=0;
			    $multiphoto_urls_tw=$desc_array=array();
			    $post_details=array('xyz_smap_userid'=>$xyz_smap_smapsoln_userid_tw,
			        'xyz_use_tw_img_desc'=>$xyz_smap_use_tw_img_desc,
			        'alt_text'=>$desc_array,
			        'tw_username'=>$twid,
			        'video_length_total_bytes'=>$count_tw,
			        'xyz_smap_posting_method'=>$post_twitter_image_permission,
			        'xyz_smap_multiphoto_enable'=>$xyz_smap_multiphoto_tweet,
			        'multiphoto_count'=>$supported_urls_count,
			        'xyz_smap_multiphoto_urls'=>$multiphoto_urls_tw,
			        'xyz_smap_video_url'=>$video,
			        'xyz_smap_xyzscripts_userid'=>$xyz_smap_xyzscripts_userid,
			        'xyz_smap_premium_publish_video'=>$xyz_smap_publish_video_tw,
			        'message'=>$substring,
			        'tw_image_url'=>$attachmenturl
			    );
			    $url=XYZ_SMAP_SOLUTION_TW_PUBLISH_URL.'api/publish.php';
			    $result=xyz_smap_post_to_smap_api($post_details,$url,$xyz_smap_smapsoln_sec_key);
			    $result=json_decode($result);
			    if(!empty($result))
			    {
			        $tw_api_count=$result->tw_api_count;
			        if ($result->status==1)
			            $tw_publish_status_insert=serialize("<span style=\"color:green\"> ".$result->msg."</span>");
		            elseif ($result->status==0)
		            {
		                if(isset($result->msg) && !empty($result->msg))
		                    $tw_publish_status_insert=serialize("<span style=\"color:red\"> ".$result->msg."</span>");
	                    else
	                        $tw_publish_status_insert= serialize("<span style=\"color:red\"> Response Not Available.</span>");//1;
		            }
			    }
			    else
			        $tw_publish_status_insert= serialize("<span style=\"color:red\"> Response Not Available.</span>");//1;
			}
			$time=time();
			$post_tw_options=array(
					'postid'	=>	$post_ID,
					'acc_type'	=>	"Twitter",
					'publishtime'	=>	$time,
					'status'	=>	$tw_publish_status_insert
			);
			$smap_tw_update_opt_array=array();
			$smap_tw_arr_retrive=(get_option('xyz_smap_twap_post_logs'));
			$smap_tw_update_opt_array[0]=isset($smap_tw_arr_retrive[0]) ? $smap_tw_arr_retrive[0] : '';
			$smap_tw_update_opt_array[1]=isset($smap_tw_arr_retrive[1]) ? $smap_tw_arr_retrive[1] : '';
			$smap_tw_update_opt_array[2]=isset($smap_tw_arr_retrive[2]) ? $smap_tw_arr_retrive[2] : '';
			$smap_tw_update_opt_array[3]=isset($smap_tw_arr_retrive[3]) ? $smap_tw_arr_retrive[3] : '';
			$smap_tw_update_opt_array[4]=isset($smap_tw_arr_retrive[4]) ? $smap_tw_arr_retrive[4] : '';
			$smap_tw_update_opt_array[5]=isset($smap_tw_arr_retrive[5]) ? $smap_tw_arr_retrive[5] : '';
			$smap_tw_update_opt_array[6]=isset($smap_tw_arr_retrive[6]) ? $smap_tw_arr_retrive[6] : '';
			$smap_tw_update_opt_array[7]=isset($smap_tw_arr_retrive[7]) ? $smap_tw_arr_retrive[7] : '';
			$smap_tw_update_opt_array[8]=isset($smap_tw_arr_retrive[8]) ? $smap_tw_arr_retrive[8] : '';
			$smap_tw_update_opt_array[9]=isset($smap_tw_arr_retrive[9]) ? $smap_tw_arr_retrive[9] : '';
			array_shift($smap_tw_update_opt_array);
			array_push($smap_tw_update_opt_array,$post_tw_options);
			update_option('xyz_smap_twap_post_logs', $smap_tw_update_opt_array);
		}
		if($tbaccess_token!="" && $tbaccess_token_secret!="" && $tmbappid!="" && $tmbappsecret!="" && $post_tb_permission==1)
		{
		    $data=array();
		    $img_status="";
		    if($post_tumblr_media_permission==1)
		    {
		        $img=array();

		        if(!empty($attachmenturl))
		            $img = wp_remote_get($attachmenturl,array('sslverify'=> (get_option('xyz_smap_peer_verification')=='1') ? true : false));
		            if(is_array($img))
		            {
		                if (isset($img['body'])&& trim($img['body'])!='')
		                {
		                    $image_found = 1;
		                    if (($img['headers']['content-length']) && trim($img['headers']['content-length'])!='')
		                    {
		                        $img_size=$img['headers']['content-length']/(1024*1024);
		                        if($img_size>10){$image_found=0;$img_status="Image skipped(greater than 10MB)";}
		                    }
		                    $img = $img['body'];
		                }
		                else
		                    $image_found = 0;
		            }
		    }
		    $tbmessagetopost=str_replace("&nbsp;","",$tbmessagetopost);
		    $substring="";$islink=0;$issubstr=0;
		    $substring=str_replace('{POST_TITLE}', $name, $tbmessagetopost);
		    $substring=str_replace('{BLOG_TITLE}', $caption,$substring);
		    $substring=str_replace('{PERMALINK}', ' '.$link.' ', $substring);
		    $substring=str_replace('{POST_EXCERPT}', $excerpt, $substring);
		    $substring=str_replace('{POST_CONTENT}', $description, $substring);
		    $substring=str_replace('{USER_NICENAME}', $user_nicename, $substring);
		    $substring=str_replace('{USER_DISPLAY_NAME}', $display_name, $substring);
		    $publish_time=get_the_time(get_option('date_format'),$post_ID );
		    $substring=str_replace('{POST_PUBLISH_DATE}', $publish_time, $substring);
		    $substring=str_replace('{POST_ID}', $post_ID, $substring);
		    $client = new Tumblr\API\Client($tmbappid, $tmbappsecret);
		    $client->setToken($tbaccess_token, $tbaccess_token_secret);
		    $tb_publish_status=array();$tb_publish_status['status_msg']='';
		    if($post_tumblr_media_permission==1)//&& $image_found==1)
		        $data = array('type' => 'photo', 'caption' => $substring, 'source' => $attachmenturl);//image
		        else if($post_tumblr_media_permission==2)
		        {
		            if($image_found==1)
		                $data = array('type' => 'link','title' => $name, 'url' => $link, 'description'=>$substring, 'thumbnail'=>$attachmenturl); //link with img
		                else
		                    $data = array('type' => 'link','title' => $name, 'url' => $link, 'description'=>$substring); //link without image
		        }
		        else
		            $data = array('type' => 'text', 'title' => $name, 'body' => $substring);    //simple text
		            //print_r($data);die;
		            if(!empty($data))
		            {
		                try{
		                    $post_id_string='';
		                    $blog_name = $tbid.'.tumblr.com';
		                    $createPost = $client->createPost($blog_name, $data);
		                    if (isset($createPost->id)){
		                        $posturl = 'https://'.$tbid.'.tumblr.com/post/'.$createPost->id.'/';
		                        $post_id_string="<br/><span style=\"color:#21759B;text-decoration:underline;\"><a target=\"_blank\"  href=".$posturl.">View Post</a></span>";
		                    }
		                    $tb_publish_status['status_msg'].="<span style=\"color:green\">Success.</span>".$post_id_string;
		                }
		                catch (Exception $e)
		                {
		                    $tb_publish_status['status_msg'].="<span style=\"color:red\">".$e->getMessage().".</span>";
		                }
		            }
		            $tb_publish_status_insert=serialize($tb_publish_status['status_msg']);
		            $time=time();
		            $post_tb_options=array(
		                'postid'	=>	$post_ID,
		                'acc_type'	=>	"Tumblr",
		                'publishtime'	=>	$time,
		                'status'	=>	$tb_publish_status_insert
		            );
		            $update_opt_array=array();
		            $arr_retrive=(get_option('xyz_smap_tbap_post_logs'));
		            $update_opt_array[0]=isset($arr_retrive[0]) ? $arr_retrive[0] : '';
		            $update_opt_array[1]=isset($arr_retrive[1]) ? $arr_retrive[1] : '';
		            $update_opt_array[2]=isset($arr_retrive[2]) ? $arr_retrive[2] : '';
		            $update_opt_array[3]=isset($arr_retrive[3]) ? $arr_retrive[3] : '';
		            $update_opt_array[4]=isset($arr_retrive[4]) ? $arr_retrive[4] : '';
		            $update_opt_array[5]=isset($arr_retrive[5]) ? $arr_retrive[5] : '';
		            $update_opt_array[6]=isset($arr_retrive[6]) ? $arr_retrive[6] : '';
		            $update_opt_array[7]=isset($arr_retrive[7]) ? $arr_retrive[7] : '';
		            $update_opt_array[8]=isset($arr_retrive[8]) ? $arr_retrive[8] : '';
		            $update_opt_array[9]=isset($arr_retrive[9]) ? $arr_retrive[9] : '';
		            array_shift($update_opt_array);
		            array_push($update_opt_array,$post_tb_options);
		            update_option('xyz_smap_tbap_post_logs', $update_opt_array);
		}
		if((($lnappikey!="" && $lnapisecret!="" && get_option('xyz_smap_ln_api_permission')!=2)|| get_option('xyz_smap_ln_api_permission')==2 ) && $lnpost_permission==1 && $lnaf==0 && (get_option('xyz_smap_ln_company_ids')!=''|| get_option('xyz_smap_lnshare_to_profile')==1))
		{
			$ln_api_count=0;$api_exceed_err_ln=0;$remaining_api_count_ln=0;
			$contentln=array();
			$image_upload_err='';
			$description_li=xyz_smap_string_limit($description, 100);

			$name_li=xyz_smap_string_limit($name, 200);

			$message1=str_replace('{POST_TITLE}', $name, $lmessagetopost);
			$message2=str_replace('{BLOG_TITLE}', $caption,$message1);
			$message3=str_replace('{PERMALINK}', ' '.$link.' ', $message2);
			$message4=str_replace('{POST_EXCERPT}', $excerpt, $message3);
			$message5=str_replace('{POST_CONTENT}', $description, $message4);
			$message5=str_replace('{USER_NICENAME}', $user_nicename, $message5);

			$publish_time=get_the_time(get_option('date_format'),$post_ID );
			$message5=str_replace('{POST_PUBLISH_DATE}', $publish_time, $message5);
			$message5=str_replace('{POST_ID}', $post_ID, $message5);
			$message5=str_replace('{USER_DISPLAY_NAME}', $display_name, $message5);
			$message5=str_replace("&nbsp;","",$message5);

			$sanitizedDescription = preg_replace_callback('/([\(\)\{\}\[\]])|([@*<>\\\\\_~])/m', function ($matches) {
				return '\\'.$matches[0];
				}, $message5);////added for escaping special characters not supported by linkedin versioned api
				$message5=$sanitizedDescription;
 			$message5=xyz_smap_string_limit($message5, 3000);

		$xyz_smap_application_lnarray=get_option('xyz_smap_application_lnarray');
		$xyz_smap_ln_api_permission=get_option('xyz_smap_ln_api_permission');
		$xyz_smap_smapsoln_sec_key=get_option('xyz_smap_secret_key_ln');
		$xyz_smap_smapsoln_userid_ln=get_option('xyz_smap_smapsoln_userid_ln');
		$xyz_smap_xyzscripts_userid=get_option('xyz_smap_xyzscripts_user_id');

		if ($xyz_smap_ln_api_permission!=2){
		$ln_acc_tok_arr=json_decode($xyz_smap_application_lnarray);
		$xyz_smap_application_lnarray=$ln_acc_tok_arr->access_token;

		$ObjLinkedin = new SMAPLinkedInOAuth2($xyz_smap_application_lnarray);
		}
		elseif ($xyz_smap_ln_api_permission==2){
			$xyz_smap_token_fetch=1;
			$post_details=array('xyz_smap_userid'=>$xyz_smap_smapsoln_userid_ln,
					'xyz_smap_xyzscripts_userid'=>$xyz_smap_xyzscripts_userid,
					'xyz_smap_token_fetch'=>$xyz_smap_token_fetch
			);
			$url=XYZ_SMAP_SOLUTION_LN_PUBLISH_URL.'api/v2/publish.php';
			$result=xyz_smap_post_to_smap_api($post_details,$url,$xyz_smap_smapsoln_sec_key);
			$result=json_decode($result);
			if(!empty($result))
			{
				if (isset($result->status))
				{
					if($result->status==0)
					{
						$err=$result->msg;
						$ln_publish_status["new"]="<span style=\"color:red\">".$err."</span><br/><span style=\"color:#21759B\">No. of api calls used: ".$ln_api_count."</span>";
					}
					elseif ($result->status==1 && isset($result->access_token))
					{
						$xyz_smap_application_lnarray=$result->access_token;
						$ObjLinkedin = new SMAPLinkedInOAuth2($xyz_smap_application_lnarray);
						$remaining_api_count_ln=$result->ln_api_count;
					}
				}
			}
			//////////////////////////////////////////////////
		}
			$contentln['author'] ='urn:li:person:'.get_option('xyz_smap_lnappscoped_userid');
			$contentln['lifecycleState'] ='PUBLISHED';
			$contentln['visibility']='PUBLIC';//new
			// $contentlnauthor ='urn:li:person:'.get_option('xyz_smap_lnappscoped_userid');
				$ln_text=array('text'=>$message5);
			$ln_title=array('text'=>$name_li);
			 $contentln['distribution']=array('feedDistribution'=>'MAIN_FEED','targetEntities'=>[],'thirdPartyDistributionChannels'=>[]);
			// $contentln['commentary']='commentary text msgss to ln';
			$message5 = preg_replace('/(\n\s*){2,}/', "\n\n", $message5);
				if ($ln_posting_method==1 || (empty($attachmenturl) && $ln_posting_method==3))//if simple text message
			{
				if($xyz_smap_ln_api_permission==2)
				{
					$required_api_count_ln=1;
					if (($remaining_api_count_ln-$required_api_count_ln)<1)
					{
						$api_exceed_err_ln=1;goto api_exceed_err_ln;
					}
				}
				//$distribution=array('feedDistribution'=>'MAIN_FEED','targetEntities'=>[],'thirdPartyDistributionChannels'=>[]);
				$contentln['commentary']=$message5;
				// $contentln['distribution']=$distribution;
			}
			elseif ($ln_posting_method==2)//link share
			{
				//$distribution=array('feedDistribution'=>'MAIN_FEED','targetEntities'=>[],'thirdPartyDistributionChannels'=>[]);
				$contentln['commentary']=$message5;
				// $contentln['distribution']=$distribution;
				// if (!empty($attachmenturl))
				if (!empty($attachmenturl) && get_option('xyz_smap_lnshare_to_profile')==1)
			{
				if($xyz_smap_ln_api_permission==2)
				{
						$required_api_count_ln=3;//Change it as 4 if it counts check_status_linkedin_asset() step
					if (($remaining_api_count_ln-$required_api_count_ln)<1)
					{
						$api_exceed_err_ln=1;goto api_exceed_err_ln;
					}
				}
					$registerupload1['initializeUploadRequest']=array('owner'=>'urn:li:person:'.get_option('xyz_smap_lnappscoped_userid'));
					$arrResponse = $ObjLinkedin->getImagePostResponses($registerupload1);//print_r(json_encode($arrResponse));die;
					$ln_api_count++;
					$uploadUrl='';
					if (isset($arrResponse['value']['uploadUrl']) && isset($arrResponse['value']['image']))
				{
						$uploadUrl=$arrResponse['value']['uploadUrl'];
						$image_parameter=$arrResponse['value']['image'];
						$image_param= substr($image_parameter,13);
				}
					if ($uploadUrl!='')
					{
						$arrResponse = $ObjLinkedin->getUploadUrlResponses($uploadUrl,$attachmenturl,array());
						$ln_api_count++;
					}
					$contentln['content']=array('article'=>array('source'=>$link,'thumbnail'=>$image_parameter,'title'=>$name_li,'description'=>$description_li));
					$status_check=$ObjLinkedin->check_status_linkedin_asset('https://api.linkedin.com/rest/assets/'.$image_param);
					$ln_api_count++;
				}
				else if(empty($attachmenturl))
				{
					if($xyz_smap_ln_api_permission==2)
					{
						$required_api_count_ln=1;
						if (($remaining_api_count_ln-$required_api_count_ln)<1)
						{
							$api_exceed_err_ln=1;goto api_exceed_err_ln;
						}
					}
					$contentln['content']=array('article'=>array('source'=>$link,'title'=>$name_li,'description'=>$description_li));
				}
				update_post_meta($post_ID, "xyz_smap_insert_og", "1");
			}
		$ln_publish_status["new"]='';
			if (get_option('xyz_smap_lnshare_to_profile')==1)
			{
				if ($ln_posting_method==3)//Text message with image
			{
 				$image_upload_flag=0;
				if(!empty($attachmenturl))
				{
					if($xyz_smap_ln_api_permission==2)
					{
						$required_api_count_ln=4;//crosscheck count
						if (($remaining_api_count_ln-$required_api_count_ln)<1)
						{
							$api_exceed_err_ln=1;goto api_exceed_err_ln;
						}
					}
					//if($xyz_smap_ln_api_permission!=2)
					//{
					$registerupload1['initializeUploadRequest']=array('owner'=>'urn:li:person:'.get_option('xyz_smap_lnappscoped_userid'));
					$arrResponse = $ObjLinkedin->getImagePostResponses($registerupload1);
						$ln_api_count++;
						$urn_li_digitalmediaAsset=$uploadUrl='';
					if (isset($arrResponse['value']['uploadUrl']) && isset($arrResponse['value']['image']))
						{
						$uploadUrl=$arrResponse['value']['uploadUrl'];
						$image_parameter=$arrResponse['value']['image'];
						$image_param= substr($image_parameter,13);
						}
						if ($uploadUrl!='')
						{
							$arrResponse = $ObjLinkedin->getUploadUrlResponses($uploadUrl,$attachmenturl,array());
							$ln_api_count++;
						$cont=array('media'=>array('title'=>$name_li,'id'=>$image_parameter));
						$contentln['commentary']=$message5;
						$contentln['content']=$cont;
						$status_check=$ObjLinkedin->check_status_linkedin_asset('https://api.linkedin.com/rest/assets/'.$image_param);
							$ln_api_count++;
							$upload_status_arr=$status_check['recipes'][0];
							if (isset($upload_status_arr['status']) && ($upload_status_arr['status'] =="AVAILABLE" || $upload_status_arr['status'] =="PROCESSING"))
							{
								$image_upload_flag=1;
							}
							else
							{
								$ln_image_status='';
								if (isset($upload_status_arr['status']))
									$ln_image_status="-upload status:".$upload_status_arr['status'];
									$image_upload_err.='<br/><span style="color:red">Image upload failed '.$ln_image_status.'</span>';
							}
						}
						else {
								$image_upload_err.='<br/><span style="color:red">Image Upload Failed</span>';
						}
				}
			}
				if($xyz_smap_ln_shareprivate==1)
			{
				$contentln['Visibility']='CONNECTIONS';
				}
				else
				{
				$contentln['visibility']='PUBLIC';
				}
				try{
				$response2 = $ObjLinkedin->shareStatus($contentln);
				$ln_api_count++;
				////////////////////////////////

				if (isset($response2) && !empty($response2)){
					$response_array = explode("\n",$response2);//print_r($response_array);die;
					$post_id_response='';$error_message='';
					foreach ($response_array as $key => $value)
					{
						$splited_array= explode(":",$value,2);
						if(strcasecmp($splited_array[0],'x-restli-id')==0){//If success it contains response header x-restli-id that contains the Post ID
								$post_id_response=trim($splited_array[1]);
								break;
						}
					 else if(stripos($value,"code")>0 && stripos($value,"status")>0)// If error then a response message will be retured
						{
							$error_message_array=json_decode($value);
							$error_message=$error_message_array->message;
							break;
						}

					}
					if(empty($error_message) && empty($post_id_response))
					{
						list($headers, $body) = explode("\r\n\r\n", $response2, 2);

						// Parse the headers into an associative array
						$headerLines = explode("\r\n", $headers);
						$headersArray = [];

						foreach ($headerLines as $line) {
								$parts = explode(': ', $line, 2);
								if (count($parts) === 2) {
										$headerName = $parts[0];
										$headerValue = $parts[1];
										$headersArray[$headerName] = $headerValue;
								}
						}

						// Decode the JSON body into an associative array
						$bodyArray = json_decode($body, true);

						$message="Not Available";	$status = '0:';
						if(isset($bodyArray['message']))
						$message = $bodyArray['message'];
						if(isset($bodyArray['message']))
						$status = $bodyArray['status'];
						$ln_publish_status["new"].=	"<span style=\"color:red\"> Profile:".$status.$message.".</span><br/>";

					}
				}

				if (isset($post_id_response) && !empty($post_id_response)){
					$linkedin_post="www.linkedin.com/feed/update/".$post_id_response;
					$post_link='<br/><span style="color:#21759B;text-decoration:underline;"><a  target=\"_blank\"  href="https://'.$linkedin_post.'">View Post</a></span>';
					$ln_publish_status["new"].="<span style=\"color:green\">profile:Success.</span>".$post_link;
				}
				else if(isset($error_message) && !empty($error_message))
				{
						$ln_publish_status["new"].="<span style=\"color:red\">profile: ".$error_message.".</span>";
				}
				if ($image_upload_err!='')
					$ln_publish_status["new"].=$image_upload_err;

				}
				catch(Exception $e)
				{
				$ln_publish_status["new"].=$e->getMessage();
				}
			}
			////////////////////////////////////////////////////////////////////////////////////////////////
			$xyz_smap_ln_company_id1=$ln_publish_status_comp=array();$ln_publish_status_comp["new"]='';
			if(get_option('xyz_smap_ln_company_ids')!='')//company
				$xyz_smap_ln_company_id1=explode(",",get_option('xyz_smap_ln_company_ids'));
			if (!empty($xyz_smap_ln_company_id1)){
				foreach ($xyz_smap_ln_company_id1 as $xyz_smap_ln_company_id)
				{
							$contentln['lifecycleState'] ='PUBLISHED';
							$contentln['author'] ='urn:li:organization:'.$xyz_smap_ln_company_id;
							$contentln['visibility']='PUBLIC';
							$contentln['commentary']=$message5;
							if($ln_posting_method==2 )
							{
							if (!empty($attachmenturl))
							{
								if($xyz_smap_ln_api_permission==2)
								{
									$required_api_count_ln=3;
									if (($remaining_api_count_ln-$required_api_count_ln)<1)
									{
										$api_exceed_err_ln=1;goto api_exceed_err_ln;
									}
								}
								$registerupload1['initializeUploadRequest']=array('owner'=>'urn:li:organization:'.$xyz_smap_ln_company_id);
								$arrResponse = $ObjLinkedin->getImagePostResponses($registerupload1);//print_r(json_encode($arrResponse));die;
								$ln_api_count++;
								$uploadUrl='';
								if (isset($arrResponse['value']['uploadUrl']) && isset($arrResponse['value']['image']))
								{
									$uploadUrl=$arrResponse['value']['uploadUrl'];
									$image_parameter=$arrResponse['value']['image'];
									$image_param= substr($image_parameter,13);
								}
								if ($uploadUrl!='')
								{
									$arrResponse = $ObjLinkedin->getUploadUrlResponses($uploadUrl,$attachmenturl,array());
									$ln_api_count++;
								}
								$contentln['content']=array('article'=>array('source'=>$link,'thumbnail'=>$image_parameter,'title'=>$name_li,'description'=>$description_li));
								$status_check=$ObjLinkedin->check_status_linkedin_asset('https://api.linkedin.com/rest/assets/'.$image_param);
								$ln_api_count++;
							}
							else if(empty($attachmenturl))
										{
											if($xyz_smap_ln_api_permission==2)
											{
												$required_api_count_ln=1;
												if (($remaining_api_count_ln-$required_api_count_ln)<1)
												{
													$api_exceed_err_ln=1;goto api_exceed_err_ln;
												}
											}
											$contentln['content']=array('article'=>array('source'=>$link,'title'=>$name_li,'description'=>$description_li));
										}
										update_post_meta($post_ID, "xyz_smap_insert_og", "1");
							}

							if ($ln_posting_method==3)//Text with Image
							{
								$image_upload_flag=0;
							if(!empty($attachmenturl))
							{
							if($xyz_smap_ln_api_permission==2)
							{
										$required_api_count_ln=1;
								if (($remaining_api_count_ln-$required_api_count_ln)<1)
								{
									$api_exceed_err_ln=1;goto api_exceed_err_ln;
								}
							}
									$registerupload1['initializeUploadRequest']=array('owner'=>'urn:li:organization:'.$xyz_smap_ln_company_id);
									$arrResponse = $ObjLinkedin->getImagePostResponses($registerupload1);
								$ln_api_count++;
									if (isset($arrResponse['value']['uploadUrl']) && isset($arrResponse['value']['image']))
								{
										$uploadUrl=$arrResponse['value']['uploadUrl'];
										$image_parameter=$arrResponse['value']['image'];
										$image_param= substr($image_parameter,13);
								}
								if ($uploadUrl!='')
								{
									$arrResponse = $ObjLinkedin->getUploadUrlResponses($uploadUrl,$attachmenturl,array());
									$ln_api_count++;
										// $distribution=array('feedDistribution'=>'MAIN_FEED','targetEntities'=>[],'thirdPartyDistributionChannels'=>[]);
										$cont=array('media'=>array('title'=>$name_li,'id'=>$image_parameter));
										$contentln['commentary']=$message5;
										// $contentln['distribution']=$distribution;
										$contentln['content']=$cont;
										$status_check=$ObjLinkedin->check_status_linkedin_asset('https://api.linkedin.com/rest/assets/'.$image_param);
									$ln_api_count++;
									$upload_status_arr=$status_check['recipes'][0];
									if (isset($upload_status_arr['status']) && ( $upload_status_arr['status'] =="AVAILABLE" || $upload_status_arr['status'] =="PROCESSING"))
									{
										$image_upload_flag=1;
									}
									else
									{
										$ln_image_status='';
										if (isset($upload_status_arr['status']))
											$ln_image_status="-upload status:".$upload_status_arr['status'];
											$image_upload_err.='<br/><span style="color:red">Image upload failed '.$ln_image_status.'</span>';
									}
								}
								else {
									$image_upload_err.='<br/><span style="color:red">Image Upload Failed</span>';
						}
					}
					}
					try
						{

							$response2 = $ObjLinkedin->shareStatus($contentln);
							$ln_api_count++;
							if (isset($response2) && !empty($response2)){
								$response_array = explode("\n",$response2);//print_r($response_array);die;
								$post_id_response='';$error_message='';
								foreach ($response_array as $key => $value)
								{
									$splited_array= explode(":",$value,2);
									if(strcasecmp($splited_array[0],'x-restli-id')==0){//If success it contains response header x-restli-id that contains the Post ID
											$post_id_response=trim($splited_array[1]);
											break;
									}
										else if(stripos($value,"code")>0 && stripos($value,"status")>0)// If error then a response message will be retured
									{
												$error_message_array=json_decode($value);
												$error_message=$error_message_array->message;
												break;
									}

								}
								if(empty($error_message) && empty($post_id_response))
								{
									list($headers, $body) = explode("\r\n\r\n", $response2, 2);

									// Parse the headers into an associative array
									$headerLines = explode("\r\n", $headers);
									$headersArray = [];

									foreach ($headerLines as $line) {
									    $parts = explode(': ', $line, 2);
									    if (count($parts) === 2) {
									        $headerName = $parts[0];
									        $headerValue = $parts[1];
									        $headersArray[$headerName] = $headerValue;
									    }
									}

									// Decode the JSON body into an associative array
									$bodyArray = json_decode($body, true);

									$message="Not Available";	$status = '0:';
									if(isset($bodyArray['message']))
									$message = $bodyArray['message'];
									if(isset($bodyArray['message']))
									$status = $bodyArray['status'];
									$ln_publish_status_comp["new"].=	"<br/><span style=\"color:red\"> company/".$xyz_smap_ln_company_id.":".$status.$message.".</span><br/>";

								}
							}
							if (isset($post_id_response) && !empty($post_id_response)){
								$linkedin_post="www.linkedin.com/feed/update/".$post_id_response;
								// $linkedin_post="https://www.linkedin.com/feed/update/urn:li:share:".$image_param;
								$post_link='<br/><span style="color:#21759B;text-decoration:underline;"><a target=\"_blank\" href="https://'.$linkedin_post.'">View Post</a></span>';
								$ln_publish_status_comp["new"].="<br/><span style=\"color:green\">company/".$xyz_smap_ln_company_id." :Success.</span>".$post_link;
							}
							else if(isset($error_message) && !empty($error_message))
							{
									$ln_publish_status_comp["new"].="<br/><span style=\"color:red\">company/".$xyz_smap_ln_company_id.": ".$error_message.".</span>";
							}
							if ($image_upload_err!='')
								$ln_publish_status_comp["new"].=$image_upload_err;
					}
					catch(Exception $e)
					{
						$ln_publish_status_comp["new"].="<br/><span style=\"color:red\">company/".$xyz_smap_ln_company_id.":".$e->getMessage().".</span><br/>";
					}
				}
			}
			///////////////////////////////////////////////////////////////////////////////////////
			$ln_publish_status_insert='';
			if(!empty($ln_publish_status['new']))
				$ln_publish_status_insert.=$ln_publish_status['new'];
				if(isset($ln_publish_status_comp["new"]))
					$ln_publish_status_insert.=$ln_publish_status_comp["new"];


		if($xyz_smap_ln_api_permission==2)
			$ln_publish_status_insert.="<br/><span style=\"color:#21759B\">No. of api calls used: ".$ln_api_count."</span>";
			api_exceed_err_ln:
			if($api_exceed_err_ln==1){
				$ln_publish_status_insert.="<span style=\"color:red\"> Daily API count limit exceeded,only ".$remaining_api_count_ln." api calls left.</span>";//1;
			}
			$ln_publish_status_insert=serialize($ln_publish_status_insert);
			if($xyz_smap_ln_api_permission==2)
			{
				$xyz_smap_token_fetch=0;
				$post_details=array('xyz_smap_userid'=>$xyz_smap_smapsoln_userid_ln,
						'xyz_smap_xyzscripts_userid'=>$xyz_smap_xyzscripts_userid,
						'xyz_smap_token_fetch'=>$xyz_smap_token_fetch,
						'ln_response_from_plugin'=>$ln_publish_status_insert,
						'ln_api_count_from_plugin'=>$ln_api_count
				);
				$url=XYZ_SMAP_SOLUTION_LN_PUBLISH_URL.'api/v2/publish.php';
				$result=xyz_smap_post_to_smap_api($post_details,$url,$xyz_smap_smapsoln_sec_key);
			}

		$time=time();
		$post_ln_options=array(
				'postid'	=>	$post_ID,
				'acc_type'	=>	"Linkedin",
				'publishtime'	=>	$time,
				'status'	=>	$ln_publish_status_insert
		);

		$smap_ln_update_opt_array=array();

		$smap_ln_arr_retrive=(get_option('xyz_smap_lnap_post_logs'));

		$smap_ln_update_opt_array[0]=isset($smap_ln_arr_retrive[0]) ? $smap_ln_arr_retrive[0] : '';
		$smap_ln_update_opt_array[1]=isset($smap_ln_arr_retrive[1]) ? $smap_ln_arr_retrive[1] : '';
		$smap_ln_update_opt_array[2]=isset($smap_ln_arr_retrive[2]) ? $smap_ln_arr_retrive[2] : '';
		$smap_ln_update_opt_array[3]=isset($smap_ln_arr_retrive[3]) ? $smap_ln_arr_retrive[3] : '';
		$smap_ln_update_opt_array[4]=isset($smap_ln_arr_retrive[4]) ? $smap_ln_arr_retrive[4] : '';
		$smap_ln_update_opt_array[5]=isset($smap_ln_arr_retrive[5]) ? $smap_ln_arr_retrive[5] : '';
		$smap_ln_update_opt_array[6]=isset($smap_ln_arr_retrive[6]) ? $smap_ln_arr_retrive[6] : '';
		$smap_ln_update_opt_array[7]=isset($smap_ln_arr_retrive[7]) ? $smap_ln_arr_retrive[7] : '';
		$smap_ln_update_opt_array[8]=isset($smap_ln_arr_retrive[8]) ? $smap_ln_arr_retrive[8] : '';
		$smap_ln_update_opt_array[9]=isset($smap_ln_arr_retrive[9]) ? $smap_ln_arr_retrive[9] : '';

		array_shift($smap_ln_update_opt_array);
		array_push($smap_ln_update_opt_array,$post_ln_options);
		update_option('xyz_smap_lnap_post_logs', $smap_ln_update_opt_array);

		}

	if($xyz_smap_bot_token!="" && $xyz_smap_bot_username!="" && $tgpost_permission==1){
		require_once( dirname( __FILE__ ) .'/../api/telegram.php' );
		$tg_publish_status_insert=$xyz_media_param=$media_type='';$image_spec=0;
		$message1=str_replace('{POST_TITLE}', $name, $tgmessagetopost);
		$message2=str_replace('{BLOG_TITLE}', $caption,$message1);
		$message3=str_replace('{PERMALINK}', ' '.$link.' ', $message2);
		$message4=str_replace('{POST_EXCERPT}', $excerpt, $message3);
		$message5=str_replace('{POST_CONTENT}', $description, $message4);
		$message5=str_replace('{USER_NICENAME}', $user_nicename, $message5);
		$message5=str_replace('{POST_ID}', $post_ID, $message5);
		$publish_time=get_the_time(get_option('date_format'),$post_ID );
		$message5=str_replace('{POST_PUBLISH_DATE}', $publish_time, $message5);
		$message5=str_replace('{USER_DISPLAY_NAME}', $display_name, $message5);
		$message5=str_replace("&nbsp;","",$message5);
		
	    if($tg_posting_method==1)
			$message5=xyz_smap_string_limit($message5, 4096);
		else
			$message5=xyz_smap_string_limit($message5, 1024);
			if(!empty($attachmenturl))
			{
			$img = wp_remote_get($attachmenturl,array('sslverify'=> (get_option('xyz_smap_premium_peer_verification')=='1') ? true : false));

			if(is_array($img) && ! is_wp_error( $img ) )
			{
				if (isset($img['body']) && trim($img['body'])!='')
				{
					if (($img['headers']['content-length']) && trim($img['headers']['content-length'])!='')
					{
						$img_size=$img['headers']['content-length']/(1024*1024);
						if( $img_size <= 10 )                               
							$image_spec=1;                                
						else
							$tg_publish_status_insert.="<span style=\"color:red\">Image size is greater than 10MB</span>";
					}
				}
			}
		}
		$xyz_smap_tgchannel_id=stripslashes(get_option('xyz_smap_tgchannel_id'));
		$xyz_smap_tggroup_id=stripslashes(get_option('xyz_smap_tggroup_id'));
	
		if(!empty($xyz_smap_tggroup_id))
			$xyz_smap_tggroup_id = unserialize($xyz_smap_tggroup_id);
		if(!empty($xyz_smap_tgchannel_id))
			$xyz_smap_tgchannel_id = unserialize($xyz_smap_tgchannel_id);

		// Ensure both are arrays, or initialize them as empty arrays
		$xyz_smap_tggroup_id = is_array($xyz_smap_tggroup_id) ? $xyz_smap_tggroup_id : [];
	
		$xyz_smap_tgchannel_id = is_array($xyz_smap_tgchannel_id) ? $xyz_smap_tgchannel_id : [];
		
		//merge array with key value pairs
		$xyz_smap_tgchannel_group_id = $xyz_smap_tggroup_id + $xyz_smap_tgchannel_id; 
		foreach($xyz_smap_tgchannel_group_id as $channel_id => $channel_name){

		if($tg_posting_method==1)//Simple text
		{ 
			if($message5==''){
				$message5=$name;
			}
			$media_type='text';          
			$xyz_media_param = array(
				'body' => array(
					'chat_id' => $channel_id,
					'text' => $message5, 
				),
			);                 
		}  
		if($tg_posting_method==2) 
		{					
			if($image_spec!=0)
			{
				$media_type='photo';
				if($attachmenturl!='')
				{                                                        
						$xyz_media_param = array(
						'body' => array(
							'chat_id' => $channel_id,
							'photo' => $attachmenturl, // URL of the audio file
						'caption' => $message5
						),
					);                                         
				}
			} 
			else
			{
				if($message5==''){
					$message5=$name;
				}
				$media_type='text';          
				$xyz_media_param = array(
					'body' => array(
						'chat_id' => $channel_id,
						'text' => $message5, 
					),
				);  			
			}              
		} 
				$xyz_tg_media_result='';                         
                $xyz_tg_media_result=xyz_smap_make_tg_post($xyz_smap_bot_token,
                $media_type,$xyz_media_param);
                if(isset($xyz_tg_media_result['error']) && $xyz_tg_media_result['error']!='')//wp_error
                {
                    $err_msg=$xyz_tg_media_result['error'];
                    $tg_publish_status_insert.="<span style=\"color:red\">".$channel_id."/".$media_type." : Failed.".$err_msg."</span>"; 
                }
                else
                {
                    $xyz_tg_media_result=json_decode($xyz_tg_media_result['body']); 
					if($xyz_tg_media_result!=NULL){      
						if(isset($xyz_tg_media_result) && $xyz_tg_media_result->ok!=true){
							$err_msg=$xyz_tg_media_result->description;                
                    $tg_publish_status_insert.="<span style=\"color:red\">".$channel_id."/".$media_type." : Failed.".$err_msg."</span>"; 
                }
				else
				{
								$tg_publish_status_insert.="<span style=\"color:green\">".$channel_id."/".$media_type." : Success.</span>"; 
				}
			}
				}
			}
			if(!empty($tg_publish_status_insert))
		    	$tg_publish_status_insert=serialize($tg_publish_status_insert);

		        $time=time();
		        $post_tg_options=array(
		            'postid'	=>	$post_ID,
		            'acc_type'	=>	"Telegram",
		            'publishtime'	=>	$time,
		            'status'	=>	$tg_publish_status_insert
		        );

		        $smap_tg_update_opt_array=array();
		        $smap_tg_arr_retrive=(get_option('xyz_smap_tgap_post_logs'));
		        $smap_tg_update_opt_array[0]=isset($smap_tg_arr_retrive[0]) ? $smap_tg_arr_retrive[0] : '';
		        $smap_tg_update_opt_array[1]=isset($smap_tg_arr_retrive[1]) ? $smap_tg_arr_retrive[1] : '';
		        $smap_tg_update_opt_array[2]=isset($smap_tg_arr_retrive[2]) ? $smap_tg_arr_retrive[2] : '';
		        $smap_tg_update_opt_array[3]=isset($smap_tg_arr_retrive[3]) ? $smap_tg_arr_retrive[3] : '';
		        $smap_tg_update_opt_array[4]=isset($smap_tg_arr_retrive[4]) ? $smap_tg_arr_retrive[4] : '';
		        $smap_tg_update_opt_array[5]=isset($smap_tg_arr_retrive[5]) ? $smap_tg_arr_retrive[5] : '';
		        $smap_tg_update_opt_array[6]=isset($smap_tg_arr_retrive[6]) ? $smap_tg_arr_retrive[6] : '';
		        $smap_tg_update_opt_array[7]=isset($smap_tg_arr_retrive[7]) ? $smap_tg_arr_retrive[7] : '';
		        $smap_tg_update_opt_array[8]=isset($smap_tg_arr_retrive[8]) ? $smap_tg_arr_retrive[8] : '';
		        $smap_tg_update_opt_array[9]=isset($smap_tg_arr_retrive[9]) ? $smap_tg_arr_retrive[9] : '';
		        array_shift($smap_tg_update_opt_array);
		        array_push($smap_tg_update_opt_array,$post_tg_options);
		        update_option('xyz_smap_tgap_post_logs', $smap_tg_update_opt_array);
		}
		//threads
		if($thaccess_token!=""  && $thappid!="" && $thappsecret!="" && $post_threads_permission==1)
		{
			///threads upload image end/////
			$thmessagetopost=str_replace("&nbsp;","",$thmessagetopost);	
			$thmessage="";
			$thmessage=xyz_smap_split_replace('{POST_TITLE}', $name, $thmessagetopost);
			$thmessage=str_replace('{BLOG_TITLE}', $caption,$thmessage);
			$thmessage=str_replace('{PERMALINK}', ' '.$link.' ', $thmessage);
			$thmessage=xyz_smap_split_replace('{POST_EXCERPT}', $excerpt, $thmessage);
			$thmessage=xyz_smap_split_replace('{POST_CONTENT}', $description, $thmessage);
			$thmessage=str_replace('{USER_NICENAME}', $user_nicename, $thmessage);
			$thmessage=str_replace('{POST_ID}', $post_ID, $thmessage);
			$publish_time=get_the_time(get_option('date_format'),$post_ID );
			$thmessage=str_replace('{POST_PUBLISH_DATE}', $publish_time, $thmessage);
			$thmessage=str_replace('{USER_DISPLAY_NAME}', $display_name,$thmessage );
			$thmessage = xyz_smap_string_limit($thmessage, 497);
			// Choose the appropriate function based on the availability of mb_strlen
			$strlen = function_exists('mb_strlen') ? 'mb_strlen' : 'strlen';
			// Check if the message exceeds 497 characters
			if ($strlen($thmessage) > 497) {
				// Append the "..." to indicate continuation
				$thmessage .= '...';
			}
		//$th_api_count=0;
		$postFields=array();
		require_once(dirname(__FILE__) . '/../api/threads.php');
		if($xyz_smap_thpost_method==1) //simple text message
		{
			if($thmessage=='')
				$thmessage=$name;
				$postFields = [
					'media_type' => 'TEXT',
					'text' => $thmessage,
					'access_token' => $thaccess_token,
				];     
		}
		else if($xyz_smap_thpost_method==2)  //share link
		{
			$postFields = [
				'media_type' => 'TEXT',
				'text' => $thmessage,
				'access_token' => $thaccess_token,
				'link_attachment' => $link // Default link attachment
			];
		}
		elseif ($xyz_smap_thpost_method==3 )  //text message with image 
		{
			if ($attachmenturl!='')  //text message with image 
			{
				$postFields = [
					'media_type' => 'IMAGE',
					'text' => $thmessage,
					'access_token' => $thaccess_token,
					'image_url' => $attachmenturl // Default link attachment
				];
			}
			else{
				//no images , post simple text message
				if($thmessage=='')
				$thmessage=$name;
				$postFields = [
					'media_type' => 'TEXT',
					'text' => $thmessage,
					'access_token' => $thaccess_token,
				]; 
			}
		}
			if(!empty($postFields ))
			{
			try
			{
			
					$th_publish_status['status_msg'] = xyz_smap_post_to_threads_single_threaded($th_user_id, $thaccess_token, $postFields);
					update_option('test_status_msg',$th_publish_status);
			}
			catch (Exception $e)
			{
				$th_publish_status['status_msg'].="<span style=\"color:red\">".$e->getMessage().".</span>";
			}	   
			if(isset($th_publish_status['status_msg']))
			{
				$th_publish_status_insert=serialize($th_publish_status['status_msg']);
			}
			else
			{
				$th_publish_status_insert=serialize("<span style=\"color:green\">".$user_profile_name." : Success.</span>");
			}
		}
		else {
			$th_publish_status_insert=serialize("Not published");
		}
			$time=time();
			$post_th_options=array(
					'postid'	=>	$post_ID,
					'acc_type'	=>	"Threads",
					'publishtime'	=>	$time,
					'status'	=>	$th_publish_status_insert
			);
			$smap_th_update_opt_array=array();
			$smap_th_arr_retrive=(get_option('xyz_smap_thap_post_logs'));
			$smap_th_update_opt_array[0]=isset($smap_th_arr_retrive[0]) ? $smap_th_arr_retrive[0] : '';
			$smap_th_update_opt_array[1]=isset($smap_th_arr_retrive[1]) ? $smap_th_arr_retrive[1] : '';
			$smap_th_update_opt_array[2]=isset($smap_th_arr_retrive[2]) ? $smap_th_arr_retrive[2] : '';
			$smap_th_update_opt_array[3]=isset($smap_th_arr_retrive[3]) ? $smap_th_arr_retrive[3] : '';
			$smap_th_update_opt_array[4]=isset($smap_th_arr_retrive[4]) ? $smap_th_arr_retrive[4] : '';
			$smap_th_update_opt_array[5]=isset($smap_th_arr_retrive[5]) ? $smap_th_arr_retrive[5] : '';
			$smap_th_update_opt_array[6]=isset($smap_th_arr_retrive[6]) ? $smap_th_arr_retrive[6] : '';
			$smap_th_update_opt_array[7]=isset($smap_th_arr_retrive[7]) ? $smap_th_arr_retrive[7] : '';
			$smap_th_update_opt_array[8]=isset($smap_th_arr_retrive[8]) ? $smap_th_arr_retrive[8] : '';
			$smap_th_update_opt_array[9]=isset($smap_th_arr_retrive[9]) ? $smap_th_arr_retrive[9] : '';
			array_shift($smap_th_update_opt_array);
			array_push($smap_th_update_opt_array,$post_th_options);
			update_option('xyz_smap_thap_post_logs', $smap_th_update_opt_array);
		}
	}

	$_POST=$_POST_CPY;
}
