<?php

//////////////////////// COMMON HTTP REQUEST ///////////////////////

if (!function_exists('xyz_smap_wp_remote_request')) {
    function xyz_smap_wp_remote_request($url, $args = array(), $method = 'POST')
    {
        if (!isset($args['sslverify'])) {
            $args['sslverify'] = (get_option('xyz_smap_peer_verification') == '1');
        }

        if (!isset($args['timeout'])) {
            $args['timeout'] = 50;
        }

        $args['method'] = strtoupper($method);

        return wp_remote_request($url, $args);
    }
}

//////////////////////// BOT VERIFICATION ///////////////////////

if (!function_exists("xyz_smap_tg_verify_bot_token")) {
    function xyz_smap_tg_verify_bot_token($botApiToken)
    {
        $apiUrl = "https://api.telegram.org/bot{$botApiToken}/getMe";

        $response = xyz_smap_wp_remote_request($apiUrl, array(), 'GET');
        
        if ( is_wp_error( $response ) ) {
            return array(
                'error' => $response->get_error_message()
            );
        }

        $responseData = json_decode(wp_remote_retrieve_body($response), true);

        if (!empty($responseData['ok'])) {
            return $responseData['result']['first_name'];
            }

        if (!empty($responseData['error_code'])) {
            $error = $responseData['error_code'];

            if (!empty($responseData['description'])) {
                $error .= ': ' . $responseData['description'];
        }

            return array(
                'error' => $error
            );
     }

        return array(
            'error' => 'Unknown error'
        );
    }
}

//////////////////////// VERIFY CHANNEL / GROUP ///////////////////////

if (!function_exists("xyz_smap_tg_get_channel_group_name")) {
    function xyz_smap_tg_get_channel_group_name($botApiToken, $channel_Ids, $type)
    {
        $apiUrl = "https://api.telegram.org/bot{$botApiToken}/getChat";

        $channels_groups = array();
        $channelids_with_error='';

        foreach($channel_Ids as $channel_Id) {

            $args = array(
                'body' => array(
                    'chat_id' => $channel_Id
                )
            );

            $response = xyz_smap_wp_remote_request($apiUrl, $args, 'POST');

            if (is_wp_error($response)) {
                $channelids_with_error.=$channel_Id.',';  
                continue;
            }

            $chatInfo = json_decode(
                wp_remote_retrieve_body($response),
                true
            );

            if (
                !empty($chatInfo['ok']) &&
                isset($chatInfo['result']['type']) &&
                $chatInfo['result']['type'] == $type
            ) {
                $channels_groups[$channel_Id] =
                    $chatInfo['result']['title'];
            } else {         
                    $channelids_with_error.=$channel_Id.',';                
            }
        }

        return array(
            'success' => $channels_groups,
            'error'   => $channelids_with_error
        );
    }
}

//////////////////////// TELEGRAM POST ///////////////////////

if (!function_exists("xyz_smap_make_tg_post")) {
    function xyz_smap_make_tg_post($botApiToken, $media_type, $xyz_media_param_enc)
    {
        $baseUrl = "https://api.telegram.org/bot{$botApiToken}/";

        $mediaEndpoints = array(
            'text' => 'sendMessage',
            'photo' => 'sendPhoto'
        );

        if (empty($mediaEndpoints[$media_type])) {
            return array(
                'error' => 'Invalid media type.'
            );
        }

            $url = $baseUrl . $mediaEndpoints[$media_type];

        if (isset($xyz_media_param_enc['body'])) {
            $args = $xyz_media_param_enc;
        } else {
            $args = array(
                'body' => $xyz_media_param_enc
            );
        }

        $response = xyz_smap_wp_remote_request(
            $url,
            $args,
            'POST'
        );

        if (is_wp_error($response)) {
            return array(
                'error' => $response->get_error_message()
            );
        }

        return array(
                'media_type' => $media_type,
            'body'       => wp_remote_retrieve_body($response)
        );
        }
    }
