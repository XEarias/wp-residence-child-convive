<?php

////////////////////////////////////////////////////////////////////////////////
/// Get a package details from user top profile
////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_get_pack_data_for_user_top') ):
function wpestate_get_pack_data_for_user_top($userID,$user_pack,$user_registered,$user_package_activation){     
    print '<div class="pack_description">
                <div class="pack-unit">';
            $remaining_lists=wpestate_get_remain_listing_user($userID,$user_pack);
            if($remaining_lists==-1){
                $remaining_lists=__('unlimited','wpestate');
            }
               
      
               
            if ($user_pack!=''){
                $title              = get_the_title($user_pack);
                $pack_time          = get_post_meta($user_pack, 'pack_time', true);
                $pack_list          = get_post_meta($user_pack, 'pack_listings', true);
                $pack_featured      = get_post_meta($user_pack, 'pack_featured_listings', true);
                $pack_price         = get_post_meta($user_pack, 'pack_price', true);
                $unlimited_lists    = get_post_meta($user_pack, 'mem_list_unl', true);
                $date               = strtotime ( get_user_meta($userID, 'package_activation',true) );
                $biling_period      = get_post_meta($user_pack, 'biling_period', true);
                $billing_freq       = intval(get_post_meta($user_pack, 'billing_freq', true));  
            
                
                $seconds=0;
                switch ($biling_period){
                   case 'Day':
                       $seconds=60*60*24;
                       break;
                   case 'Week':
                       $seconds=60*60*24*7;
                       break;
                   case 'Month':
                       $seconds=60*60*24*30;
                       break;    
                   case 'Year':
                       $seconds=60*60*24*365;
                       break;    
                }
               
                $time_frame      =   $seconds*$billing_freq;
                $expired_date    =   $date+$time_frame;
                $expired_date    =   date('Y-m-d',$expired_date); 
                $pack_image_included  =   get_post_meta($user_pack, 'pack_image_included', true);
                if (intval($pack_image_included)==0){
                    $pack_image_included=__('Unlimited', 'wpestate');
                }
               
                
                
                print '<div class="pack_description_unit_head"><h4>'.__('Your Current Package :','wpestate').'</h4> 
                       <span class="pack-name">'.$title.' </span></div> ';
                
                if($unlimited_lists==1){
                    print '<div class="pack_description_unit pack_description_details">';
                    print __('  unlimited','wpestate');
                    print '<p class="package_label">'.__('Listings Included','wpestate').'</p></div>';
                    
                    print '<div class="pack_description_unit pack_description_details">';
                    print __('  unlimited','wpestate');
                    print '<p class="package_label">'.__('Listings Remaining','wpestate').'</p></div>';
                }else{
                    print '<div class="pack_description_unit pack_description_details">';
                    print ' '.$pack_list;
                    print '<p class="package_label">'.__('Listings Included','wpestate').'</p></div>';
                    
                    print '<div class="pack_description_unit pack_description_details">';
                    print '<span id="normal_list_no"> '.$remaining_lists.'</span>';
                    print '<p class="package_label">'.__('Listings Remaining','wpestate').'</p></div>';
                }
                
                print '<div class="pack_description_unit pack_description_details">';
                print '<span id="normal_list_no"> '.$pack_featured.'</span>';
                print '<p class="package_label">'.__('Featured Included','wpestate').'</p></div>';
                
                print '<div class="pack_description_unit pack_description_details">';
                print '<span id="featured_list_no"> '.wpestate_get_remain_featured_listing_user($userID).'</span>';
                print '<p class="package_label">'.__('Featured Remaining','wpestate').'</p></div>';
                
                print '<div class="pack_description_unit pack_description_details">';
                print ' '.$pack_image_included;
                print '<p class="package_label">'.__('Images / per listing','wpestate').'</p></div>';
                
                print '<div class="pack_description_unit pack_description_details">';
                print ' '.$expired_date;
                print '<p class="package_label">'.__('Ends On','wpestate').'</p></div>';
             
            }else{

                $free_mem_list      =   esc_html( get_option('wp_estate_free_mem_list','') );
                $free_feat_list     =   esc_html( get_option('wp_estate_free_feat_list','') );
                $free_mem_list_unl  =   get_option('wp_estate_free_mem_list_unl', '' );
                $free_pack_image_included  =  esc_html( get_option('wp_estate_free_pack_image_included ','') );
                print '<div class="pack_description_unit_head"><h4>'.__('Your Current Package:','wpestate').'</h4>
                      <span class="pack-name">'.__('Free Membership','wpestate').'</span></div>';
                
                print '<div class="pack_description_unit pack_description_details">';
                if($free_mem_list_unl==1){
                    print __('  unlimited','wpestate');
                }else{
                    print ' '.$free_mem_list;
                }
                print '<p class="package_label">'.__('Listings Included','wpestate').'</p></div>';
                 
                print '<div class="pack_description_unit pack_description_details">';
                print '<span id="normal_list_no"> '.$remaining_lists.'</span>';
                print '<p class="package_label">'.__('Listings Remaining','wpestate').'</p></div>';
             
                print '<div class="pack_description_unit pack_description_details">';
                print '<span id="normal_list_no"> '.$free_feat_list.'</span>';
                print '<p class="package_label">'.__('Featured Included','wpestate').'</p></div>';
                
                print '<div class="pack_description_unit pack_description_details">';
                print '<span id="featured_list_no"> '.wpestate_get_remain_featured_listing_user($userID).'</span>';
                print '<p class="package_label">'.__('Featured Remaining','wpestate').'</p></div>';
                
                print '<div class="pack_description_unit pack_description_details">';
                print '<span id="free_pack_image_included"> '.$free_pack_image_included.'</span>';
                print '<p class="package_label">'.__('Images / listing','wpestate').'</p></div>';
                
                print '<div class="pack_description_unit pack_description_details">';
                print '&nbsp;<p class="package_label">'.__('Ends On: -','wpestate').'</p></div>';
                
            }
            print '</div></div>';
          
}
endif; // end   wpestate_get_pack_data_for_user_top  



?>