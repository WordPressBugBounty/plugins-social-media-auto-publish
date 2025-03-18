<?php 
if( !defined('ABSPATH') ){ exit();}
?>

<div>
    <form method="post" name="xyz_smap_logs_form">
        <fieldset style="width: 99%; border: 1px solid #F7F7F7; padding: 10px 0;">
            <div style="text-align: left; padding-left: 7px;">
                <h3><?php _e('Auto Publish Logs', 'social-media-auto-publish'); ?></h3>
            </div>
            <span><?php _e('Last ten logs of each social media account', 'social-media-auto-publish'); ?></span>

            <table class="widefat" style="width: 99%; margin: 0 auto; border-bottom: none;">
                <thead>
                    <tr class="xyz_smap_log_tr">
                        <th scope="col" width="1%">&nbsp;</th>
						<th scope="col" width="12%"> <?php _e('Post Id','social-media-auto-publish');?> </th>
                        <th scope="col" width="20%"><?php _e('Post Title', 'social-media-auto-publish'); ?></th>
                        <th scope="col" width="12%"><?php _e('Account Type', 'social-media-auto-publish'); ?></th>
                        <th scope="col" width="15%"><?php _e('Published On', 'social-media-auto-publish'); ?></th>
                        <th scope="col" width="40%"><?php _e('Status', 'social-media-auto-publish'); ?></th>
                    </tr>
                </thead>

                <?php 
					$social_media_logs = [
						'xyz_smap_fbap_post_logs',
						'xyz_smap_twap_post_logs',
						'xyz_smap_lnap_post_logs',
						'xyz_smap_igap_post_logs',
						'xyz_smap_tbap_post_logs',
						'xyz_smap_tgap_post_logs',
						'xyz_smap_thap_post_logs'
					];

					$logs_array = [];
					foreach ($social_media_logs as $log_key) {
						$log_data = get_option($log_key);
						if (is_array($log_data)) {
							$logs_array = array_merge($logs_array, $log_data);
						}
					}
                if (!empty($logs_array)) {
                    foreach ($logs_array as $log) {
                        if (!is_array($log)) continue; // Ensure $log is an array
                        
                        $postid = esc_html($log['postid'] ?? '');
                        $acc_type = esc_html($log['acc_type'] ?? '');
                        $publishtime = !empty($log['publishtime']) ? xyz_smap_local_date_time('Y/m/d g:i:s A', $log['publishtime']) : '';
                        $status = $log['status'] ?? '';

                        echo '<tr>';
                        echo '<td>&nbsp;</td>';
                        echo '<td style="vertical-align: middle;">' . $postid . '</td>';
                        echo '<td style="vertical-align: middle;">' . esc_html(get_the_title($postid)) . '</td>';
                        echo '<td style="vertical-align: middle;">' . $acc_type . '</td>';
                        echo '<td style="vertical-align: middle;">' . esc_html($publishtime) . '</td>';
                        echo '<td class="xyz_smap_td_custom" style="vertical-align: middle;">';

                            $arrval = unserialize($status);
                            if ($arrval !== false && is_array($arrval)) //for facebook
                                echo implode('<br>', $arrval);
                            else if(($arrval))//other social medias
								print_r($arrval);
                            
							else //status empty
								echo '<span style="color:red">Response Not Available</span>';
                        echo '</td></tr>';
                    }
                }
                ?>

            </table>
        </fieldset>
    </form>

    <div style="padding: 5px; color: #e67939; font-size: 14px;"> 
        <?php _e('For publishing a simple text message, it will take 1 API call, Upload image option will take 2-3 API calls in Facebook and 4 API calls in LinkedIn, and attach link option takes 1 API call (2 API calls for Facebook if enabled option for clearing cache).', 'social-media-auto-publish'); ?> 
    </div>
</div>
