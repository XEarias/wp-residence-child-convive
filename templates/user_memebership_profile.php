<?php
$current_user           =   wp_get_current_user();
$userID                 =   $current_user->ID;
$parent_userID          =   wpestate_check_for_agency($userID);
$user_login             =   $current_user->user_login;  
$add_link               =   wpestate_get_template_link('user_dashboard_add.php');
$dash_profile           =   wpestate_get_template_link('user_dashboard_profile.php');
$dash_favorite          =   wpestate_get_template_link('user_dashboard_favorite.php');
$dash_link              =   wpestate_get_template_link('user_dashboard.php');
$activeprofile          =   '';
$activedash             =   '';
$activeadd              =   '';
$activefav              =   '';
$user_pack              =   get_the_author_meta( 'package_id' , $parent_userID );    
$clientId               =   esc_html( get_option('wp_estate_paypal_client_id','') );
$clientSecret           =   esc_html( get_option('wp_estate_paypal_client_secret','') );  
$user_registered        =   get_the_author_meta( 'user_registered' , $userID );
$user_package_activation=   get_the_author_meta( 'package_activation' , $parent_userID );
$is_membership          =   0;

$paid_submission_status = esc_html ( get_option('wp_estate_paid_submission','') );  

$user_role = get_user_meta( $current_user->ID, 'user_estate_role', true) ;



// kul

?>


<div class="col-md-12 top_dahsboard_wrapper dashboard_package_row"> 
<?php
if ($paid_submission_status == 'membership'){
    wpestate_get_pack_data_for_user_top($parent_userID,$user_pack,$user_registered,$user_package_activation); 
    $is_membership=1;  
   
}

?>

<?php
if (    $is_membership==1  ){ 
    $stripe_profile_user    =   get_user_meta($userID,'stripe',true);
    $subscription_id        =   get_user_meta( $userID, 'stripe_subscription_id', true );
    $enable_stripe_status   =   esc_html ( get_option('wp_estate_enable_stripe','') );
 

    if( $stripe_profile_user!='' && $subscription_id!='' && $enable_stripe_status==='yes'){
        echo '<span id="stripe_cancel" data-original-title="'.__('subscription will be cancelled at the end of current period','wpestate').'" data-stripeid="'.$userID.'">'.__('cancel stripe subscription','wpestate').'</span>';
    }
    ?>
    <?php if($userID==$parent_userID){?>
    <div class="pack_description ">
        <?php 
        print '<div id="open_packages" class="wrapper_packages">'.__('See Available Packages and Payment Methods', 'wpestate');
        print ' '.'<i class="fa fa-angle-up" aria-hidden="true"></i>'.'</div>';
        ?>
    </div>

     
        <div class="pack_description_row ">
            <div class="add-estate profile-page profile-onprofile row"> 
                <div class="pack-unit">
                    <div class="pack_description_unit_head">     
                        <?php print '<h4>'.__('Packages Available', 'wpestate').'</h4>'; ?>
                    </div>    

                    <?php
                    $user_role          =   get_user_meta( $userID, 'user_estate_role', true) ;
                    if( intval($user_role) ==0 ){
                        $user_role=0;
                    }else{
                        $user_role          =   $user_role-1;
                    }
                  
                    $roles              =   array( __('User','wpestate') ,__('Agent','wpestate'),__('Agency','wpestate'),__('Developer','wpestate'));
                    $user_role          =   $roles[$user_role];
                    
                    
                    $user_role_array    =   array(
                                                'key'       => 'pack_visible_user_role',
                                                'value'     => $user_role,
                                                'compare'   => 'LIKE',
                                            );
                    
                    $currency           =   esc_html( get_option('wp_estate_submission_curency', '') );
                    $where_currency     =   esc_html( get_option('wp_estate_where_currency_symbol', '') );
                    $args = array(
                        'post_type'         => 'membership_package',
                        'posts_per_page'    => -1,
                        'meta_query'        =>  array(
                                                    array(
                                                        'key' => 'pack_visible',
                                                        'value' => 'yes',
                                                        'compare' => '=',
                                                    ),
                                                )
                    );
                    
                    
                    if($user_role!=''){
                       $args['meta_query'][]= $user_role_array;
                    }
                    
                 
                    $pack_selection = new WP_Query($args);

                    $listing_positions = listing_positions;

                    while($pack_selection->have_posts() ){
                        $pack_selection->the_post();
                        $postid                 = $post->ID;
                        $pack_list              = get_post_meta($postid, 'pack_listings', true);
                        $pack_featured          = get_post_meta($postid, 'pack_featured_listings', true);
                        $pack_image_included    = get_post_meta($postid, 'pack_image_included', true);
                        $pack_price             = get_post_meta($postid, 'pack_price', true);
                        $unlimited_lists        = get_post_meta($postid, 'mem_list_unl', true);
                        $biling_period          = get_post_meta($postid, 'biling_period', true);
                        $billing_freq           = get_post_meta($postid, 'billing_freq', true);
                        $pack_time              = get_post_meta($postid, 'pack_time', true);
                        $unlimited_listings     = get_post_meta($postid, 'mem_list_unl', true);
                        $listing_position       = get_post_meta($postid, 'listing_position', true);

                        

                        if($billing_freq>1){
                            $biling_period.='s';
                        }
                        if ($where_currency == 'before') {
                            $price = $currency . ' ' . $pack_price;
                        }else {
                            $price = $pack_price . ' ' . $currency;
                        }
                        if (intval($pack_image_included)==0){
                            $pack_image_included=__('Unlimited', 'wpestate');
                        }

                        $title = get_the_title();
                        print'<div class="pack-listing" data-id="'.$postid.'">';
                            print'<div class="pack-listing-title" data-stripetitle2="'.sanitize_title($title).'"  data-stripetitle="'.sanitize_title($title).' '.__('Package Payment','wpestate').'" data-stripepay="'.($pack_price*100).'" data-packprice="'.$pack_price.'" data-packid="'.$postid.'">'.$title.' </div>';
                            print'<div class="submit-price">'.$price.' / '.$billing_freq.' '.wpestate_show_bill_period($biling_period).'</div>';

                            $linsting_position_text = isset($listing_positions[$listing_position]) ? $listing_positions[$listing_position] : $listing_positions[2];

                            print'<div class="pack-listing-period">Visibilidad <b>'.$linsting_position_text.'</b></div> ';


                            if($unlimited_listings==1){
                                print'<div class="pack-listing-period">'.__('Unlimited', 'wpestate').' '.__('listings ', 'wpestate').' </div>';
                            }else{
                                print'<div class="pack-listing-period">'.$pack_list.' '.__('Listings', 'wpestate').' </div>';
                            }
                            

                            print'<div class="pack-listing-period">'.$pack_featured.' '.__('Featured', 'wpestate').'</div> ';
                            
                            print'<div class="pack-listing-period">'.$pack_image_included.' '.__('Images / per listing', 'wpestate').'</div> ';

                            print'<div class="pack-listing-period">'.$pack_image_included.' '.__('Images / per listing', 'wpestate').'</div> ';

                        print'<div class="buypackage">';
                            print'<input type="checkbox" name="packagebox" id="pack_box" value="'.$postid.'" style="display:block;" />'.__('Switch to this package', 'wpestate');  
                        print '</div>';
                        print'</div>';//end pack listing;
                    }//end while 
                    wp_reset_query();?>
                </div>
            </div>
        </div>

        <div class="pack_description_row ">
            <div class="add-estate profile-page profile-onprofile row"> 
                <div class="pack-unit">
                    <div class="pack_description_unit_head">
                        <?php  print '<h4>'.__('Payment Method','wpestate').'</h4>'; ?>
                    </div>

                    <div id="package_pick">
                           <!-- <div class="recuring_wrapper">
                                <input type="checkbox" name="pack_recuring" id="pack_recuring" value="1" style="display:block;" /> 
                                <label for="pack_recurring"><?php _e('make payment recurring ','wpestate');?></label>
                            </div>
-->
                        <?php

                            
                            $enable_paypal_status= esc_html ( get_option('wp_estate_enable_paypal','') );
                            $enable_stripe_status= esc_html ( get_option('wp_estate_enable_stripe','') );
                            $enable_direct_status= esc_html ( get_option('wp_estate_enable_direct_pay','') );

                            $enable_payu_status = esc_html ( get_option('wp_estate_enable_payu','') );


/*
                            if($enable_paypal_status==='yes'){
                                print '<div id="pick_pack"></div>';
                            }
                            if($enable_stripe_status==='yes'){
                                wpestate_show_stripe_form_membership();
                            }
*/
                            //if($enable_payu_status === 'yes'){
                            if('yes' === 'yes'){


                                $deviceSessionId = md5($userID.microtime());

                                ?>
                                
                                <script type="text/javascript" src="https://maf.pagosonline.net/ws/fp/tags.js?id=<?php echo $deviceSessionId; ?>80200"></script>
                                <noscript>
                                    <iframe style="width: 100px; height: 100px; border: 0; position: absolute; top: -5000px;" src="https://maf.pagosonline.net/ws/fp/tags.js?id=<?php echo $deviceSessionId; ?>80200"></iframe>
                                </noscript>
                                <script>
                                        (function($){

                                            $(document).ready(function(){

                                                var completed = true;

                                                function payuSubmit(e){


                                                    e.preventDefault();

                                                    if(!completed){
                                                            return;
                                                    }

                                                    var pack_id = $("input[name=packagebox]:checked").val();

                                                    $(this).find('[name=pack_id]').val(pack_id);

                                                    var dataPayu = $(this).serialize();

                                                    $.get(ajax_vars.ajaxurl, dataPayu+"&action=payu_pay").done(function(res){
                                                        
                                                        if(!res.success){
                                                            return;
                                                        }

                                                        window.location.reload();
                                                    
                                                    })

                                                }

                                                $("#payu_pay").click(function(){
                                                    if($('.payu_form').length){
                                                        return;
                                                    }
                                                    var payu_form_container = $(
                                                        '<div class="payu_form">'+
                                                            '<form>'+
                                                                '<span class="close-payu">X</span>'+
                                                                '<input type="hidden" name="pack_id"/>'+
                                                                '<input type="hidden" name="device_session_id" value="<?php echo $deviceSessionId;?>"/>'+
                                                                '<div>'+
                                                                    '<label>Número de Tarjeta</label>'+
                                                                    '<input type="text" name="credit_card[number]" placeholder="Número de Tarjeta"/>'+
                                                                '</div>'+
                                                                '<div class="half">'+
                                                                    '<label>Fecha de Expiración</label>'+
                                                                    '<input type="text" name="credit_card[expiration_date]" placeholder="MM/YYYY" />'+
                                                                '</div>'+
                                                                '<div class="half" style="margin-left: 9.5%;">'+
                                                                    '<label>Codigo de Seguridad</label>'+
                                                                    '<input type="text" name="credit_card[security_code]" placeholder="Codigo de Seguridad"/>'+
                                                                '</div>'+
                                                                '<div>'+
                                                                    '<label>Tipo de Tarjeta</label>'+
                                                                    '<select name="payment_method">'+
                                                                        '<option value="VISA">Visa</option>'+
                                                                        '<option value="MASTERCARD">Mastercard</option>'+
                                                                    '</select>'+
                                                                '</div>'+
                                                                '<div>'+
                                                                    '<button class="btn small">PAGAR</button>'+
                                                                '</div>'+
                                                            '</form>'+
                                                        '</div>');

                                                        
                                                    
                                                    payu_form_container.find('form').submit(payuSubmit);  
                                                    payu_form_container.find('span.close-payu').click(function(){
                                                        payu_form_container.remove();
                                                    });  

                                                    $('body').append(payu_form_container);                                            

                                                })

                                                //$("#payu_pay_form").submit()

                                            })
                                        })(jQuery);
                                </script>

                                <?php

                                $payu_logo = get_stylesheet_directory_uri() . '/img/payu-logo.png';
                                print '<div id="payu_pay" class="wpresidence_button" style="cursor: pointer; height: 69px; text-align: center; width: 169px; background-color: #e3e8eb; margin-right: 15px;"><img style="max-width:100%; max-height: 100%;" src="'.$payu_logo.'"/></div>';
                                
                            }
/*
                            if($enable_direct_status==='yes'){
                                print '<div id="direct_pay" class="wpresidence_button">'.__('Wire Transfer','wpestate').'</div>';
                            }
*/
                            
                        ?>
                    </div>
                </div> 
            </div>
        </div>
    
        <?php } ?>
   
<?php } ?>
                
       
    
</div>



        
               
        
     

            
<?php

function wpestate_show_bill_period($biling_period){

    if($biling_period=='Day' || $biling_period=='Days'){
        return  __('days','wpestate');
    }
    else if($biling_period=='Week' || $biling_period=='Weeks'){
       return  __('weeks','wpestate');
    }
    else if($biling_period=='Month' || $biling_period=='Months'){
        return  __('months','wpestate');
    }
    else if($biling_period=='Year'){
        return  __('year','wpestate');
    }else if($biling_period=='Years'){
        return  __('years','wpestate');
    }

}


?>
            