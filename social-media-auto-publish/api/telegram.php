<?php
////////////////////////Bot verification///////////////////////
if (!function_exists("xyz_smap_tg_verify_bot_token")) {
    function xyz_smap_tg_verify_bot_token($botApiToken)
    {
        $responseDataReturn=array();
        $apiUrl = "https://api.telegram.org/bot{$botApiToken}/getMe";
        $response = wp_remote_get( $apiUrl, array(
            'sslverify' => get_option('xyz_smap_peer_verification') == '1',
        ));
        if ( is_wp_error( $response ) ) {
            // Handle error
            $responseDataReturn['error']=$response->get_error_message();
        }
        else{
        $response_body = wp_remote_retrieve_body( $response );
        $responseData = json_decode( $response_body, true );  // Convert 
        if ( isset( $responseData['ok'] ) && $responseData['ok'] == 1 ) {
            return $responseData['result']['first_name'];
        } else {
            if(isset($responseData['error_code'])){
                $responseDataReturn['error']= $responseData['error_code'];
                if($responseData['description']!='')
                 $responseDataReturn['error'].= ": ".$responseData['description'];
            }
        }
     }
     return $responseDataReturn;
    }
}
//Verify channel,group 
if (!function_exists("xyz_smap_tg_get_channel_group_name")) {
    function xyz_smap_tg_get_channel_group_name($botApiToken,$channel_Ids,$type){
        $apiUrl = "https://api.telegram.org/bot{$botApiToken}/getChat";
        $channels_groups_details=$channels_groups=array();
        $channelids_with_error='';
        foreach($channel_Ids as $channel_Id) {
            $channel_details = array(
                'body' => array(
                    'chat_id' => $channel_Id
                ),
                'sslverify' => get_option('xyz_smap_peer_verification') == '1',
            );
            // Make the request using wp_remote_post
            $response = wp_remote_post($apiUrl, $channel_details);
            // Check for errors in the response
            if (is_wp_error($response)) {
                $channelids_with_error.=$channel_Id.',';  
            } else {
            // Get the body of the response
                $result = wp_remote_retrieve_body($response);//print_r($result);
                $chatInfo = json_decode($result, true);
                if ($chatInfo['ok']) {
                    if($chatInfo['result']['type']==$type){
                    $channelGroupName = $chatInfo['result']['title'];
                    $channels_groups[$channel_Id] = $channelGroupName;
                    }
                }
                else               
                    $channelids_with_error.=$channel_Id.',';                
            }
        }
        if(!empty($channelids_with_error))
         $channels_groups_details['error']=$channelids_with_error;
         $channels_groups_details['success']=$channels_groups;
    return $channels_groups_details;
    }
}

if (!function_exists("xyz_smap_make_tg_post")) {
    function xyz_smap_make_tg_post($botApiToken,$media_type,$xyz_media_param_enc){
        $baseUrl = "https://api.telegram.org/bot{$botApiToken}/";
        $mediaEndpoints = [
            'text' => 'sendMessage',
            'photo' => 'sendPhoto',
        ]; 
        // Check if media type is valid
        if (array_key_exists($media_type, $mediaEndpoints)) {
            $url = $baseUrl . $mediaEndpoints[$media_type];
        $xyz_media_param_enc['sslverify'] = get_option('xyz_smap_peer_verification') == '1';
        // Make the request using wp_remote_post
        $response = wp_remote_post($url, $xyz_media_param_enc);
        if (is_wp_error($response)) {
            // Handle error if wp_remote_post fails
            return ['error' => $response->get_error_message()];
        }

            $body = wp_remote_retrieve_body($response);
        return [
                'media_type' => $media_type,
            'body' => $body,
        ];
    } else {
        // Handle invalid media type
        return ['error' => 'Invalid media type.'];
        }
    }
}

