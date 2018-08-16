<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:
        
if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css' ); 
    }
endif;

load_child_theme_textdomain('wpestate', get_stylesheet_directory().'/languages');
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css' );

// END ENQUEUE PARENT ACTION


require_once __DIR__."/libs/membership.php";


//constantes
define('listing_positions', [
    2 => "Minima",
    1 => "Media",    
    0 => "Máxima"    
]);


function add_card_js(){

    //wp_enqueue_style( 'card-css', get_stylesheet_directory_uri() . '/js/card/dist/card.css' );
    wp_register_script('card-js', get_stylesheet_directory_uri() . '/js/card/dist/card.js');
    wp_enqueue_script('card-js');

}

add_action( 'wp_enqueue_scripts', 'add_card_js');



function add_metabox_listing_position(){



    add_meta_box('linstig_position_metabox', 'Visibilidad de los Anuncios', 'linstig_position_metabox_html', 'membership_package');

}

add_action('add_meta_boxes', 'add_metabox_listing_position');

function linstig_position_metabox_html($post){

    $positions = listing_positions;

    $listing_position = get_post_meta($post->ID, "listing_position", true);

    ?>

    <select name="listing_position" >
        <?php foreach($positions as $key => $position):?>
        <option value="<?php echo $key;?>" <?php selected($key, $listing_position);?>> <?php echo $position;?></option>
        <?php endforeach;?>
    </select>

    <?php

}




function admin_save_membership_package ($post_id, $post_object){
    

    if( !isset( $post_object->post_type ) or ($post_object->post_type != 'membership_package')){
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    if(isset($_POST["listing_position"])){

        $listing_position = $_POST["listing_position"];
        update_post_meta( $post_id, 'listing_position', $listing_position);
    }
    
}

add_action('post_updated', 'admin_save_membership_package', 10, 2);


//////////////////////////////////////////////////////////////////////
///////////////////////////// PAYU SDK ///////////////////////////////
//////////////////////////////////////////////////////////////////////


require_once __DIR__.'/libs/payu_sdk/PayU.php';


function payu($options = [], $data = []) {
	// CONF INICIAL
	PayU::$apiKey = $options["apiKey"];
	PayU::$apiLogin = $options["apiLogin"];
	PayU::$merchantId = $options["merchantId"];
	PayU::$language = $options["language"];
	PayU::$isTest = $options["isTest"];
	Environment::setPaymentsCustomUrl("https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi");
	Environment::setReportsCustomUrl("https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi");
	Environment::setSubscriptionsCustomUrl("https://sandbox.api.payulatam.com/payments-api/rest/v4.3/");
	$reference = $data["reference"];
	$value = $data["value"];
	$parameters = [
		PayUParameters::ACCOUNT_ID => $data["ACCOUNT_ID"],
		PayUParameters::REFERENCE_CODE => $reference,
		PayUParameters::DESCRIPTION => $data["DESCRIPTION"],
		PayUParameters::VALUE => $value,
		PayUParameters::TAX_VALUE => $data["TAX_VALUE"],
		PayUParameters::TAX_RETURN_BASE => $data["TAX_RETURN_BASE"],
		PayUParameters::CURRENCY => $data["CURRENCY"],
		PayUParameters::BUYER_NAME => $data["BUYER"]["NAME"],
		PayUParameters::BUYER_EMAIL => $data["BUYER"]["EMAIL"],
		PayUParameters::BUYER_CONTACT_PHONE => $data["BUYER"]["CONTACT_PHONE"],
		PayUParameters::BUYER_DNI => $data["BUYER"]["DNI"],
		PayUParameters::BUYER_STREET => $data["BUYER"]["STREET"],
		PayUParameters::BUYER_STREET_2 => $data["BUYER"]["STREET_2"],
		PayUParameters::BUYER_CITY => $data["BUYER"]["CITY"],
		PayUParameters::BUYER_STATE => $data["BUYER"]["STATE"],
		PayUParameters::BUYER_COUNTRY => $data["BUYER"]["COUNTRY"],
		PayUParameters::BUYER_POSTAL_CODE => $data["BUYER"]["POSTAL_CODE"],
		PayUParameters::BUYER_PHONE => $data["BUYER"]["PHONE"],
		PayUParameters::PAYER_NAME => $data["PAYER"]["NAME"],
		PayUParameters::PAYER_EMAIL => $data["PAYER"]["EMAIL"],
		PayUParameters::PAYER_CONTACT_PHONE => $data["PAYER"]["CONTACT_PHONE"],
		PayUParameters::PAYER_DNI => $data["PAYER"]["DNI"],
		PayUParameters::PAYER_STREET => $data["PAYER"]["STREET"],
		PayUParameters::PAYER_STREET_2 => $data["PAYER"]["STREET_2"],
		PayUParameters::PAYER_CITY => $data["PAYER"]["CITY"],
		PayUParameters::PAYER_STATE => $data["PAYER"]["STATE"],
		PayUParameters::PAYER_COUNTRY => $data["PAYER"]["COUNTRY"],
		PayUParameters::PAYER_POSTAL_CODE => $data["PAYER"]["POSTAL_CODE"],
		PayUParameters::PAYER_PHONE => $data["PAYER"]["PHONE"],
		PayUParameters::CREDIT_CARD_NUMBER => $data["CREDIT_CARD"]["NUMBER"],
		PayUParameters::CREDIT_CARD_EXPIRATION_DATE => $data["CREDIT_CARD"]["EXPIRATION_DATE"],
		PayUParameters::CREDIT_CARD_SECURITY_CODE=> $data["CREDIT_CARD"]["SECURITY_CODE"],
		PayUParameters::PAYMENT_METHOD => $data["PAYMENT_METHOD"],
		PayUParameters::INSTALLMENTS_NUMBER => $data["INSTALLMENTS_NUMBER"],
		PayUParameters::COUNTRY => $data["COUNTRY"],
		PayUParameters::DEVICE_SESSION_ID => $data["DEVICE_SESSION_ID"],
		PayUParameters::IP_ADDRESS => $data["IP_ADDRESS"],
		PayUParameters::PAYER_COOKIE => $data["PAYER_COOKIE"],
		PayUParameters::USER_AGENT => $data["USER_AGENT"]
	];
	$response_array = [];
	$response = PayUPayments::doAuthorizationAndCapture($parameters);
	if ($response) {
		$response_array["orderId"] = $response->transactionResponse->orderId;
		$response_array["transactionId"] = $response->transactionResponse->transactionId;
		$response_array["state"] = $response->transactionResponse->state;
		if ($response->transactionResponse->state=="PENDING") {
			$response_array["pendingReason"] = $response->transactionResponse->pendingReason;
		}
		
		if (property_exists($response->transactionResponse, 'paymentNetworkResponseErrorMessage')) {
            $response_array["paymentNetworkResponseErrorMessage"] = $response->transactionResponse->paymentNetworkResponseErrorMessage;
		}
		
		if(property_exists($response->transactionResponse, 'trazabilityCode')) {
			$response_array["trazabilityCode"] = $response->transactionResponse->trazabilityCode;
		}
		
		$response_array["responseCode"] = $response->transactionResponse->responseCode;
		
		if(property_exists($response->transactionResponse, 'responseMessage')) {
			$response_array["responseMessage"] = $response->transactionResponse->responseMessage;
		}
		return $response_array;
	}
	return false;
}


function payu_proccess_pay () {

    $current_user = wp_get_current_user();
        
    if ( !is_user_logged_in() ) {   
        wp_send_json_error('El usuario no esta logueado');
    }
    
    $userID                   =   $current_user->ID;
    $user_email               =   $current_user->user_email ;
    $selected_pack            =   intval( $_GET['pack_id'] );
    $total_price              =   get_post_meta($selected_pack, 'pack_price', true);
    $currency                 =   esc_html( get_option('wp_estate_currency_symbol', '') );
    $where_currency           =   esc_html( get_option('wp_estate_where_currency_symbol', '') );

    if ($total_price != 0) {
        if ($where_currency == 'before') {
            $total_price = $currency . ' ' . $total_price;
        }   else {
            $total_price = $total_price . ' ' . $currency;
        }
    }

    $credit_card = (isset($_GET["credit_card"])) ? $_GET["credit_card"] : false ;

    if(!$credit_card){
        wp_send_json_error('No hay datos de tarjeta de credito o debito');
    }

    if(!$credit_card["number"] || !$credit_card["expiration_date"] || !$credit_card["security_code"]){
        wp_send_json_error('Falta algun dato de la tarjeta');
    }

    $device_session_id = (isset($_GET["device_session_id"])) ? $_GET["device_session_id"] : false ;

    if(!$device_session_id){
        wp_send_json_error('Falta el id de la sesion');
    }

    $payment_method = (isset($_GET["payment_method"])) ? strtoupper($_GET["payment_method"]) : false;

    if(!$payment_method){
        wp_send_json_error('No hay metodo de pago');
    }

    

    $data = [
        "reference" => 'Pago membresía ' . time(),
        "value" => "20000",
        "ACCOUNT_ID" => "512321",
        "DESCRIPTION" => "payment test",
        "TAX_VALUE" => "3193",
        "TAX_RETURN_BASE" => "16806",
        "CURRENCY" => 'COP',
        "BUYER" => [
            "NAME" => "First name and second buyer name",
            "EMAIL" => "buyer_test@test.com",
            "CONTACT_PHONE" => "7563126",
            "DNI" => "5415668464654",
            "STREET" => "calle 100",
            "STREET_2" => "5555487",
            "CITY" => "Medellin",
            "STATE" => "Antioquia",
            "COUNTRY" => "CO",
            "POSTAL_CODE" => "000000",
            "PHONE" => "7563126",
        ],
        "PAYER" => [
            "NAME" => "First name and second payer name",
            "EMAIL" => "payer_test@test.com",
            "CONTACT_PHONE" => "7563126",
            "DNI" => "5415668464654",
            "STREET" => "calle 93",
            "STREET_2" => "125544",
            "CITY" => "Bogota",
            "STATE" => "Bogota",
            "COUNTRY" => "CO",
            "POSTAL_CODE" => "000000",
            "PHONE" => "7563126",
        ],
        "CREDIT_CARD" => [
            "NUMBER" => $credit_card["number"],
            "EXPIRATION_DATE" => $credit_card["expiration_date"],
            "SECURITY_CODE"=> $credit_card["security_code"],
        ],
        "PAYMENT_METHOD" => $payment_method,
        "INSTALLMENTS_NUMBER" => "1",
        "COUNTRY" => PayUCountries::CO,
        "DEVICE_SESSION_ID" => $device_session_id,
        "IP_ADDRESS" => '127.0.0.1',
        "PAYER_COOKIE" => LOGGED_IN_COOKIE,
        "USER_AGENT" =>"Mozilla/5.0 (Windows NT 5.1; rv:18.0) Gecko/20100101 Firefox/18.0"
    ];
 

    $options = [
        "apiKey" => "4Vj8eK4rloUd272L48hsrarnUA",
        "apiLogin" => "pRRXKOl8ikMmt9u",
        "merchantId" => "508029",
        "language" => SupportedLanguages::ES,
        "isTest" => false
    ];

    //try {
        $response = payu($options, $data);
   // } catch (Exception $e){
       // wp_send_json_error($e->getMessage()); 
   // }

    
    if(!is_array($response) && $response === false){
        wp_send_json_error(); 
    }

    if(!isset($response["state"]) || $response["state"] != 'APPROVED' || !isset($response["responseCode"]) || $response["responseCode"] != 'APPROVED'){
        wp_send_json_error($response); 
    }
  


    wpestate_upgrade_user_membership($userID,$selected_pack,'One Time', '',1);

    wp_send_json_success();
    /*
    // insert invoice
    $time           =   time(); 
    $date           =   date('Y-m-d H:i:s',$time); 
    $is_featured    =   0;
    $is_upgrade     =   0;
    $paypal_tax_id  =   '';
             
    $invoice_no = wpestate_insert_invoice('Package','One Time',$selected_pack,$date,$userID,$is_featured,$is_upgrade,$paypal_tax_id);

    update_post_meta($invoice_no, 'pay_status', 1);
    */
    

}

add_action("wp_ajax_nopriv_payu_pay", 'payu_proccess_pay');
add_action("wp_ajax_payu_pay", 'payu_proccess_pay');




/*

add_action( 'wp_ajax_wpestate_payu_pack', 'wpestate_payu_pack' );

if( !function_exists('wp_ajax_wpestate_payu_pack') ):
    
    function wp_ajax_wpestate_payu_pack(){
        $current_user = wp_get_current_user();
        
        if ( !is_user_logged_in() ) {   
            exit('out pls');
        }
        
        $userID                   =   $current_user->ID;
        $user_email               =   $current_user->user_email ;
        $selected_pack            =   intval( $_POST['selected_pack'] );
        $total_price              =   get_post_meta($selected_pack, 'pack_price', true);
        $currency                 =   esc_html( get_option('wp_estate_currency_symbol', '') );
        $where_currency           =   esc_html( get_option('wp_estate_where_currency_symbol', '') );
        
        if ($total_price != 0) {
            if ($where_currency == 'before') {
                $total_price = $currency . ' ' . $total_price;
            }   else {
                $total_price = $total_price . ' ' . $currency;
            }
        }
        
        
        // insert invoice
        $time           =   time(); 
        $date           =   date('Y-m-d H:i:s',$time); 
        $is_featured    =   0;
        $is_upgrade     =   0;
        $paypal_tax_id  =   '';
                 
        $invoice_no = wpestate_insert_invoice('Package','One Time',$selected_pack,$date,$userID,$is_featured,$is_upgrade,$paypal_tax_id);
        
        // send email
        $headers    = 'From: No Reply <noreply@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";
        $message    = __('Hi there,','wpestate') . "\r\n\r\n";
        
        if (function_exists('icl_translate') ){
            $mes                  =     strip_tags( get_option('wp_estate_direct_payment_details','') );
            $payment_details      =     icl_translate('wpestate','wp_estate_property_direct_payment_text', $mes );
        }else{
            $payment_details      =     strip_tags( get_option('wp_estate_direct_payment_details','') );
        }
        
        update_post_meta($invoice_no, 'pay_status', 0);
        $arguments=array(
            'invoice_no'        =>  $invoice_no,
            'total_price'       =>  $total_price,
            'payment_details'   =>  $payment_details,
        );
     
        // email sending
        wpestate_select_email_type($user_email,'new_wire_transfer',$arguments);
        $company_email      =  get_bloginfo('admin_email');
        wpestate_select_email_type($company_email,'admin_new_wire_transfer',$arguments);
         
         
    }

endif;

*/
