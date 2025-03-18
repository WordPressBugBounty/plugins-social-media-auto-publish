<?php
/**
 * Exchange authorization code for a short-lived access token using wp_remote_post.
 */
function xyz_smap_exchange_code_for_token($app_id, $app_secret, $code, $redirect_uri) {
    $url = 'https://graph.threads.net/oauth/access_token';
    $body = [
        'client_id' => $app_id,
        'client_secret' => $app_secret,
        'code' => $code,
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code',
    ];

    $response = wp_remote_post($url, [
        'body' => $body,
        'timeout' => 10,
        'sslverify' => get_option('xyz_smap_peer_verification') == '1',
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    return json_decode(wp_remote_retrieve_body($response), true);
}

/**
 * Exchange a short-lived token for a long-lived token using wp_remote_get.
 */
function xyz_smap_exchange_for_long_lived_token($app_secret, $short_lived_token) {
    $url = add_query_arg([
        'grant_type' => 'th_exchange_token',
        'client_secret' => $app_secret,
        'access_token' => $short_lived_token,
    ], 'https://graph.threads.net/access_token');

    $response = wp_remote_get($url, [
        'timeout' => 10,
        'sslverify' => get_option('xyz_smap_peer_verification') == '1',
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    return json_decode(wp_remote_retrieve_body($response), true);
}
// /**
//  * Refresh a long-lived token using wp_remote_get.
//  *
//  * @param string $app_secret The Threads App Secret.
//  * @param string $long_lived_token The current long-lived token.
//  * @return array|string|WP_Error The refreshed token data, or an error object.
//  */
// function xyz_smap_refreshLongLivedToken($app_secret, $long_lived_token) {
//     $url = add_query_arg([
//         'grant_type' => 'th_refresh_token',
//         'client_secret' => $app_secret,
//         'access_token' => $long_lived_token,
//     ], 'https://graph.threads.net/refresh_access_token');

//     $response = wp_remote_get($url, [
//         'timeout' => 10,
//         'sslverify' => get_option('xyz_smap_peer_verification') == '1',
//     ]);

//     if (is_wp_error($response)) {
//         return $response;
//     }

//     $body = wp_remote_retrieve_body($response);
//     $decoded_body = json_decode($body, true);

//     if (isset($decoded_body['access_token'])) {
//         return $decoded_body;
//     }

//     return new WP_Error('token_refresh_error', 'Failed to refresh the long-lived token.', $decoded_body);
// }


function xyz_smap_post_to_threads_single_threaded($threadUserId, $accessToken, $postFields) 
{
    try {
        // Step 1: Create Threads Media Container
        $url = "https://graph.threads.net/".XYZ_SMAP_TH_API_VERSION."/{$threadUserId}/threads";
        $postFields['access_token'] = $accessToken;

        $response = wp_remote_post($url, [
            'body' => $postFields,
            'sslverify' => get_option('xyz_smap_peer_verification') == '1',
            'timeout' => 10,
        ]);

        if (is_wp_error($response)) {
            throw new Exception("Error creating media container: " . $response->get_error_message());
        }

        $decodedResponse = json_decode(wp_remote_retrieve_body($response), true);
        if (empty($decodedResponse['id'])) {
            throw new Exception("Media Container ID not found in response: " . wp_remote_retrieve_body($response));
        }
        $mediaContainerId = $decodedResponse['id'];

        // Step 2: Poll Media Container Status
        $statusUrl = "https://graph.threads.net/".XYZ_SMAP_TH_API_VERSION."/{$mediaContainerId}";
        $statusParams = [
            'fields' => 'status,error_message',
            'access_token' => $accessToken,
        ];
        $statusUrl = add_query_arg($statusParams, $statusUrl);

        $maxRetries = 5;
        $retryInterval = 5; // 5 seconds ////// worked in 4 reattempts and 6 sec delay
        $status = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $statusResponse = wp_remote_get($statusUrl, [
                'sslverify' => get_option('xyz_smap_peer_verification') == '1',
                'timeout' => 10,
            ]);
            if (is_wp_error($statusResponse)) {
                throw new Exception("Error retrieving container status: " . $statusResponse->get_error_message());
            }

            $decodedStatusResponse = json_decode(wp_remote_retrieve_body($statusResponse), true);
            $status = $decodedStatusResponse['status'] ?? 'UNKNOWN';
            if ($status === 'FINISHED' || $status === 'PUBLISHED') {
                break;
            } elseif ($status === 'ERROR') {
                $errorMessage = $decodedStatusResponse['error_message'] ?? 'Unknown error';
                throw new Exception("Media Container Error: " . $errorMessage);
            } elseif ($status === 'EXPIRED') {
                throw new Exception("Media Container Expired: It was not published within 24 hours.");
            }
            // Wait before retrying
            sleep($retryInterval);
        }

        if ($status !== 'FINISHED' && $status !== 'PUBLISHED') {
            throw new Exception("Media Container status is still '$status' after {$maxRetries} retries.");
        }

        // Step 3: Publish Threads Media Container
        $publishUrl = "https://graph.threads.net/".XYZ_SMAP_TH_API_VERSION."/{$threadUserId}/threads_publish";
        $publishFields = [
            'creation_id' => $mediaContainerId,
            'access_token' => $accessToken,
        ];

        $publishResponse = wp_remote_post($publishUrl, [
            'body' => $publishFields,
            'timeout' => 10,
            'sslverify' => get_option('xyz_smap_peer_verification') == '1',
        ]);

        if (is_wp_error($publishResponse)) {
            throw new Exception("Publish Error: " . $publishResponse->get_error_message());
        }

        $decodedPublishResponse = json_decode(wp_remote_retrieve_body($publishResponse), true);
        if (empty($decodedPublishResponse['id'])) {
            throw new Exception("Publish ID not found in response: " . wp_remote_retrieve_body($publishResponse));
        }
        $postId = $decodedPublishResponse['id'];

        // Step 4: Retrieve Permalink
        $tokenUrl = "https://graph.threads.net/".XYZ_SMAP_TH_API_VERSION."/{$postId}";
        $tokenParams = [
            'fields' => 'permalink',
            'access_token' => $accessToken,
        ];
        $tokenUrl = add_query_arg($tokenParams, $tokenUrl);

        $tokenResponse = wp_remote_get($tokenUrl, [
            'sslverify' => get_option('xyz_smap_peer_verification') == '1',
            'timeout' => 10,
        ]);

        if (is_wp_error($tokenResponse)) {
            throw new Exception("Permalink Error: " . $tokenResponse->get_error_message());
        }

        $decodedTokenResponse = json_decode(wp_remote_retrieve_body($tokenResponse), true);
        if (empty($decodedTokenResponse['permalink'])) {
            throw new Exception("Permalink not found in response: " . wp_remote_retrieve_body($tokenResponse));
        }

        // Return Success Message with Permalink
        $permalink = $decodedTokenResponse['permalink'];
        return "<span style=\"color:green\">Success: Post ID: {$postId}</span><br>
                <span style=\"color:#21759B;text-decoration:underline;\"><a target=\"_blank\" href=\"{$permalink}\">View Post</a></span>";

    } catch (Exception $e) {
        return "<span style=\"color:red\">{$e->getMessage()}</span>";
    }
}