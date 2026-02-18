<?php 
if ( ! function_exists( 'xyz_smap_tumblr_auth2_reauth' ) ) {
    function xyz_smap_tumblr_auth2_reauth( $client_id, $client_secret, $refresh_token ) {
        $url = 'https://api.tumblr.com/v2/oauth2/token';
        $ssl_verify = ( get_option( 'xyz_smap_peer_verification' ) == '1' ) ? true : false;
        $response = wp_remote_post( $url, [
            'sslverify' => $ssl_verify,
            'body' => [
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'grant_type'    => 'refresh_token',
                'refresh_token' => $refresh_token,
            ],
        ]);
        if ( is_wp_error( $response ) ) {
            return [
                'status'  => 'error',
                'message' => 'WP_Error: ' . $response->get_error_message(),
                'code'    => $response->get_error_code() ?: 0,
            ];
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        $body        = wp_remote_retrieve_body( $response );
        $data        = json_decode( $body, true );

        if ( $status_code == 200 && isset( $data['access_token'] ) ) {
            $access_token  = $data['access_token'];
            $refresh_token = $data['refresh_token'] ?? $refresh_token;
            return [
                'status' => 'success',
                'data'   => [
                    'access_token'  => $access_token,
                    'refresh_token' => $refresh_token,
                ],
                'code'   => 0,
            ];
        }
        $error_msg = isset( $data['error_description'] )
            ? $data['error_description']
            : ( $data['meta']['msg'] ?? $data['errors'][0] ?? "Unknown error occurred." );

        $error_msg .= ' Try reauthorize';

        return [
            'status'  => 'error',
            'message' => $error_msg,
            'code'    => $status_code,
        ];
    }
}
if (!function_exists('xyz_smap_tb_create_post')) {
    /**
     * Main Tumblr Post Creator
     */
    function xyz_smap_tb_create_post($blog_name, $tbaccess_token, $data) {
        $type = strtolower(trim($data['type'] ?? ''));
        if (empty($type)) {
            return [
                'status'  => 'error',
                'message' => 'Missing post type. Allowed: text, link, photo, multiphoto, video.'
            ];
        }
        switch ($type) {
            case 'text':
                return xyz_smap_tb_create_text_post($blog_name, $tbaccess_token, $data);

            case 'link':
                return xyz_smap_tb_create_link_post($blog_name, $tbaccess_token, $data);

            case 'photo': // single image
                return xyz_smap_tb_create_image_post($blog_name, $tbaccess_token, $data);

            default:
                return [
                    'status'  => 'error',
                    'message' => "Unsupported post type: $type"
                ];
        }
    }
}
if (!function_exists('xyz_smap_tb_create_text_post')) {
    /**
     * Creates a standard text post on Tumblr using the NPF API .
     */
    function xyz_smap_tb_create_text_post($blog_name, $tbaccess_token, $data) {
        $access_token    = $tbaccess_token;
        $blog_identifier = $blog_name;
        $text            = $data['body'] ?? '';
        $tags            = $data['tags'] ?? '';

        if (empty($text)) {
            return [
                'status'  => 'error',
                'message' => 'No text content provided.'
            ];
        }
        // Build NPF content blocks
        $content_blocks = [
            [
                'type' => 'text',
                'text' => $text
            ]
        ];
        $final_json_body = [
            'tags'    => $tags,
            'content' => $content_blocks
        ];
        $url = XYZ_SMAP_TB_API_OAUTH2_URL . "{$blog_identifier}/posts";
        $args = [
            'method'      => 'POST',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking'    => true,
            'headers'     => [
                'Authorization' => "Bearer $access_token",
                'Content-Type'  => 'application/json'
            ],
            'body'        => wp_json_encode($final_json_body),
            'sslverify'   => (get_option('xyz_smap_peer_verification') == '1'),
        ];
        // Send request
        $response = wp_remote_post($url, $args);
        // WP error (network issue, SSL failure, timeout, etc.)
        if (is_wp_error($response)) {
            return [
                'status'  => 'error',
                'code'    => 'wp_error',
                'message' => $response->get_error_message()
            ];
        }
        $http_code = wp_remote_retrieve_response_code($response);
        $result    = wp_remote_retrieve_body($response);
        $body_response = json_decode($result, true);
        if ($http_code == 201 && isset($body_response['response']['id'])) {
            $post_id = $body_response['response']['id'];

            return [
                'status'   => 'success',
                'id'       => $post_id,
                'post_url' => "https://$blog_name/post/$post_id/"
            ];
        }

        // ERROR handling
        $message = $body_response['meta']['msg'] ?? 'Unknown error';

        if (isset($body_response['errors']) && is_array($body_response['errors'])) {
            $error_details = array_column($body_response['errors'], 'detail');
            $message = implode('; ', array_filter($error_details)) ?: $message;
        }

        return [
            'status'  => 'error',
            'code'    => $http_code,
            'message' => $message
        ];
    }
}
if (!function_exists('xyz_smap_tb_create_link_post')) {
    function xyz_smap_tb_create_link_post($blog_name, $tbaccess_token, $data) {
        $access_token    = $tbaccess_token;
        $blog_identifier = $blog_name;

        $url         = $data['url'] ?? '';
        $title       = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $tags        = $data['tags'] ?? [];
        $thumbnail   = $data['thumbnail'] ?? '';

        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return [
                'status'  => 'error',
                'message' => 'A valid URL is required for a link post.'
            ];
        }
        // Build NPF link block
        $link_block = [
            'type' => 'link',
            'url'  => $url
        ];
        if (!empty($title))       $link_block['title']       = $title;
        if (!empty($description)) $link_block['description'] = $description;
        if (!empty($thumbnail) && filter_var($thumbnail, FILTER_VALIDATE_URL)) {
            $link_block['poster'] = [[
                'url'  => $thumbnail,
                'type' => 'image/jpeg'
            ]];
        }

        // Assemble final payload
        $final_json_body = [
            'content' => [$link_block]
        ];

        if (!empty($tags)) {
            $final_json_body['tags'] = $tags;
        }

        $request_url = XYZ_SMAP_TB_API_OAUTH2_URL . "{$blog_identifier}/posts";

        // Prepare WP Remote Request
        $args = [
            'method'      => 'POST',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking'    => true,
            'headers'     => [
                'Authorization' => "Bearer $access_token",
                'Content-Type'  => 'application/json'
            ],
            'body'        => wp_json_encode($final_json_body),
            'sslverify'   => (get_option('xyz_smap_peer_verification') == '1'),
        ];
        // Execute request
        $response = wp_remote_post($request_url, $args);
        // Check for WP errors
        if (is_wp_error($response)) {
            return [
                'status'  => 'error',
                'code'    => 'wp_error',
                'message' => $response->get_error_message()
            ];
        }

        $http_code     = wp_remote_retrieve_response_code($response);
        $result        = wp_remote_retrieve_body($response);
        $body_response = json_decode($result, true);
        if ($http_code == 201) {
            $post_id  = $body_response['response']['id'] ?? '';
            $post_url = $body_response['response']['post_url'] ?? "https://$blog_name/post/$post_id/";
            return [
                'status'   => 'success',
                'id'       => $post_id,
                'post_url' => $post_url
            ];
        }
        // ERROR handling
        $message = $body_response['meta']['msg'] ?? 'Unknown error';

        if (isset($body_response['errors']) && is_array($body_response['errors'])) {
            $error_details = array_column($body_response['errors'], 'detail');
            $message = implode('; ', array_filter($error_details)) ?: $message;
        }
        return [
            'status'  => 'error',
            'code'    => $http_code,
            'message' => $message,
        ];
    }
}
if (!function_exists('xyz_smap_tb_create_image_post')) {
    /**
     * Creates a single image post on Tumblr using NPF. Handles both local files and remote URLs.
     */
    function xyz_smap_tb_create_image_post($blog_name, $tbaccess_token, $data) {
        $access_token    = $tbaccess_token;
        $blog_identifier = $blog_name;
        $image           = $data['source'] ?? ''; // single URL or local path
        $caption         = $data['caption'] ?? '';
        $tags            = $data['tags'] ?? '';
        if (empty($image)) {
            return ['status'  => 'error', 'message' => 'No image provided.'];
        }
        $tmp_file = '';
        if ( ! function_exists( 'wp_tempnam' ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        $cleanup_temp_file = false;
        if (filter_var($image, FILTER_VALIDATE_URL)) {
            $tmp_file = wp_tempnam(basename($image)); // Get a temp filename
            $img_content = wp_remote_get($image, ['timeout' => 20, 'sslverify' => false]);
            
            if (is_wp_error($img_content)) {
                return ['status'=>'error','message'=>'Failed to fetch remote image: '.$img_content->get_error_message()];
            }
            $body = wp_remote_retrieve_body($img_content);
            if (empty($body)) {
                 return ['status'=>'error','message'=>'Remote image content was empty.'];
            }
            if (false === file_put_contents($tmp_file, $body)) {
                 return ['status'=>'error','message'=>'Failed to write remote image to temporary file.'];
            }
            $cleanup_temp_file = true; // Flag to delete this file later
        } elseif (file_exists($image)) {
            $tmp_file = $image;
        } else {
            return ['status'=>'error','message'=>'Invalid image source'];
        }
        $mime_type = mime_content_type($tmp_file);
        if (!$mime_type || strpos($mime_type, 'image') === false) {
             // Clean up temp file
             if ($cleanup_temp_file && file_exists($tmp_file)) { @unlink($tmp_file); }
             return ['status'=>'error','message'=>'File is not a recognizable image type.'];
        }

        // --- Prepare Payload ---
        $identifier = 'media_' . time() . rand(1, 99); 

        $json_content = [
            'type'    => 'image',
            'caption' => $caption,
            'media'   => [
                'type'       => $mime_type,
                'identifier' => $identifier,
            ]
        ];
        $final_json_body = [
            'tags'    => $tags,
            'content' => [$json_content]
        ];
        
        $payload_data = [
            'json' => json_encode($final_json_body),
        ];

        // --- Build Multipart/Form-Data Body Manually ---
        $boundary = wp_generate_password(24, false); // Generate a random boundary string
        $body     = '';
        
        // 1. Add JSON part
        foreach ( $payload_data as $name => $value ) {
            $body .= "--{$boundary}\r\n";
            $body .= "Content-Disposition: form-data; name=\"{$name}\"\r\n\r\n";
            $body .= $value . "\r\n";
        }
        
        // 2. Add File part (image)
        $file_contents = file_get_contents($tmp_file);
        
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Disposition: form-data; name=\"{$identifier}\"; filename=\"" . basename($tmp_file) . "\"\r\n";
        $body .= "Content-Type: {$mime_type}\r\n";
        $body .= "Content-Transfer-Encoding: binary\r\n\r\n"; // Optional but good practice
        $body .= $file_contents . "\r\n";
        
        // 3. Add closing boundary
        $body .= "--{$boundary}--\r\n";
        
        // --- Prepare wp_remote_post arguments ---
        $headers = [
            "Authorization" => "Bearer $access_token",
            // MUST set Content-Type for multipart/form-data with the boundary
            "Content-Type" => "multipart/form-data; boundary={$boundary}",
        ];
        $url = XYZ_SMAP_TB_API_OAUTH2_URL . "{$blog_identifier}/posts";
        $args = [
            'method'    => 'POST',
            'timeout'   => 45, // Increased timeout for file uploads
            'sslverify' => (get_option('xyz_smap_peer_verification') == '1'),
            'headers'   => $headers,
            'body'      => $body, // Manually constructed multipart body
        ];
        // --- Execute the Request ---
        $response = wp_remote_post($url, $args);
        // --- Clean up temp file ---
        if ($cleanup_temp_file && file_exists($tmp_file)) {
            @unlink($tmp_file);
        }
        // --- Handle Response ---
        if (is_wp_error($response)) {
            return [
                'status'  => 'error',
                'message' => 'wp_remote_post failed: ' . $response->get_error_message()
            ];
        }
        $http_code = wp_remote_retrieve_response_code($response);
        $result    = wp_remote_retrieve_body($response);
        $body_response = json_decode($result, true);

        if ($http_code == 201) {
            $post_id = $body_response['response']['id'] ?? '';
            return [
                'status' => 'success',
                'id' => $post_id,
                'post_url' => "https://$blog_name/post/$post_id/"
            ];
        }
        // Handle API error response
        $message = $body_response['meta']['msg'] ?? 'Unknown API error';
        if (isset($body_response['errors']) && is_array($body_response['errors'])) {
            $error_details = array_column($body_response['errors'], 'detail');
            $message = implode('; ', array_filter($error_details)) ?: $message;
        }
        return [
            'status' => 'error',
            'code' => $http_code,
            'message' => $message,
        ];
    }
}
