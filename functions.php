<?php
/**
 * @version    1.0
 * @package    petapalozza
 * @author     Md Mahbub Alam Khan
 * @copyright  https://themeforest.net/user/xenioushk
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * * Websites: http://bluewindlab.net
 */

/**
 * Enqueue style of child theme
 */
add_action( 'wp_enqueue_scripts', 'petapalozza_enqueue_styles' );
function petapalozza_enqueue_styles() {
 
    $petapalozza_parent_style = 'petapalozza-style';
 
    wp_enqueue_style( $petapalozza_parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $petapalozza_parent_style ),
        wp_get_theme()->get('Version')
    );
}

/**
 * Register post type for Services
**/
function aki_service_posttype() {
  register_post_type('qrcode',
    array(
      'labels' => array(
        'name' => __( 'QrCode'),
		'post_status' => 'publish',
        'singular_name' => __( 'qrcode' )
      ),
      'public' => true,
      'has_archive' => true,
      'supports'     => array( 'title', 'editor', 'thumbnail',),
      'menu_icon'   => 'dashicons-admin-site-alt3'
    )
  );
}
add_action( 'init', 'aki_service_posttype' );

function add_author_support_to_posts() {
   add_post_type_support( 'qrcode', 'author' ); 
}
add_action( 'init', 'add_author_support_to_posts' );

function wpufe_set_disply_name( $post_id ) {
$post_title = get_post_field( 'post_title', $post_id );

wp_update_user(array(
'ID' => get_current_user_id(),
'display_name' => $post_title
));
}
add_action( 'wpuf_add_post_after_insert', 'wpufe_set_disply_name' );


function generatewp_quickedit_javascript() {
    $current_screen = get_current_screen();
    if ( $current_screen->id != 'edit-post' || $current_screen->post_type != 'qrcode' )
        return;

    // Ensure jQuery library loads
    wp_enqueue_script( 'jquery' );
    ?>
    <script type="text/javascript">
        jQuery( function( $ ) {
            $( '#the-list' ).on( 'click', 'a.editinline', function( e ) {
                e.preventDefault();
                var editTime = $(this).data( 'edit-time' );
                inlineEditPost.revert();
                $( '.generatewpedittime' ).val( editTime ? editTime : '' );
            });
        });
    </script>
    <?php
}
add_action( 'admin_print_footer_scripts-edit.php', 'generatewp_quickedit_javascript' );

function generatewp_quickedit_set_data( $actions, $post ) {
    $found_value = get_post_meta( $post->ID, 'qr_code_url', true );

    if ( $found_value ) {
        if ( isset( $actions['inline hide-if-no-js'] ) ) {
            $new_attribute = sprintf( 'data-edit-time="%s"', esc_attr( $found_value ) );
            $actions['inline hide-if-no-js'] = str_replace( 'class=', "$new_attribute class=", $actions['inline hide-if-no-js'] );
        }
    }

    return $actions;
}
add_filter('inventory_row_actions', 'generatewp_quickedit_set_data', 10, 2);

/** Session timeout **/

function myplugin_cookie_expiration( $expiration, $user_id, $remember ) {
    return $remember ? $expiration : 6000;
}
add_filter( 'auth_cookie_expiration', 'myplugin_cookie_expiration', 99, 3 );

add_filter( 'manage_qrcode_posts_columns', 'set_custom_edit_qrcode_columns' );
function set_custom_edit_qrcode_columns($columns) {
    // unset( $columns['author'] );
     $columns['qr_code_url'] = __( 'Qr Code' );
    // $columns['publisher'] = __( 'Publisher', 'your_text_domain' );

     return $columns;
    //print_r($columns);
}

add_action( 'manage_qrcode_posts_custom_column' , 'custom_qrcode_column', 10, 2 );
function custom_qrcode_column( $column, $post_id ) {
    switch ( $column ) {

        case 'qr_code_url' :
            $terms = get_post_meta( $post_id , 'qr_code_url' , true);
            if ($terms)
                echo $terms;
            else
                _e( 'Null', 'your_text_domain' );
            break;

        

    }
}


add_filter( 'manage_qrcode_posts_columns', 'set_custom_edit_qrcode_columns2' );
function set_custom_edit_qrcode_columns2($columns) {
    // unset( $columns['author'] );
     $columns['pod_status'] = __( 'POD Status' );
    // $columns['publisher'] = __( 'Publisher', 'your_text_domain' );

     return $columns;
    //print_r($columns);
}

add_action( 'manage_qrcode_posts_custom_column' , 'custom_qrcode_column2', 10, 2 );
function custom_qrcode_column2( $column, $post_id ) {
    switch ( $column ) {

        case 'pod_status' :
            global $wpdb;
 
// Select Product ID
$product_id = 676;
       
// Find billing emails in the DB order table
$statuses = array_map( 'esc_sql', wc_get_is_paid_statuses() );
$customer_emails = $wpdb->get_col("
   SELECT DISTINCT pm.meta_value FROM {$wpdb->posts} AS p
   INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id
   INNER JOIN {$wpdb->prefix}woocommerce_order_items AS i ON p.ID = i.order_id
   INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id
   WHERE p.post_status IN ( 'wc-" . implode( "','wc-", $statuses ) . "' )
   AND pm.meta_key IN ( '_billing_email' )
   AND im.meta_key IN ( '_product_id', '_variation_id' )
   AND im.meta_value = $product_id
");
 
// Print array on screen
//print_r( $customer_emails );
foreach ($customer_emails as $email)
{
$user = get_user_by( 'email', $email );
$userId = $user->ID;

$customer = new WC_Customer( $userId );

$first_name   = $customer->get_first_name();
$last_name    = $customer->get_last_name();
$billing_city       = $customer->get_billing_city();
$billing_state      = $customer->get_billing_state();
$billing_postcode   = $customer->get_billing_postcode();
$billing_addrss_1   = $customer->get_billing_address_1();
$billing_addrss_2   = $customer->get_billing_address_2();

$shipping_first_name   = $customer->get_shipping_first_name();
$shipping_last_name   = $customer->get_shipping_last_name();
$shipping_company   = $customer->get_shipping_company();
$shipping_country   = $customer->get_shipping_country();
$shipping_address_1   = $customer->get_shipping_address_1();
$shipping_addrss_2   = $customer->get_shipping_address_2();
$shipping_city   = $customer->get_shipping_city();
$shipping_state   = $customer->get_shipping_state();
$shipping_postcode   = $customer->get_shipping_postcode();

$args = array(
    'post_type' => 'qrcode',
    'author'        =>  $userId,
    'orderby'       =>  'post_date',
    'order'         =>  'ASC',
    'posts_per_page' => 1
    );
$current_user_posts = get_posts( $args );
//echo '<pre>';

//print_r($current_user_posts[0]->ID);
$podvalue = update_post_meta( $current_user_posts[0]->ID , 'pod_apply' , true);
$first_name_val = update_post_meta( $current_user_posts[0]->ID , 'first_name' , $first_name);
$last_name_val = update_post_meta( $current_user_posts[0]->ID , 'last_name' , $last_name);
$billing_city_val = update_post_meta( $current_user_posts[0]->ID , '_billing_city ' , $billing_city);
$billing_state_val = update_post_meta( $current_user_posts[0]->ID , '_billing_state' , $billing_state );
$billing_postcode_val = update_post_meta( $current_user_posts[0]->ID , '_billing_postcode' , $billing_postcode);
$billing_address_1_val = update_post_meta( $current_user_posts[0]->ID , '_billing_address_1' , $billing_addrss_1);

$shipping_first_name_val = update_post_meta( $current_user_posts[0]->ID , '_shipping_first_name' , $shipping_first_name);
$shipping_last_name_val = update_post_meta( $current_user_posts[0]->ID , '_shipping_last_name' , $shipping_last_name);
$shipping_company_val = update_post_meta( $current_user_posts[0]->ID , '_shipping_company' , $shipping_company);
$shipping_country_val = update_post_meta( $current_user_posts[0]->ID , '_shipping_country' , $shipping_country);
$shipping_addrss_1_val = update_post_meta( $current_user_posts[0]->ID , '_shipping_address_1' , $shipping_address_1);
$shipping_addrss_2_val = update_post_meta( $current_user_posts[0]->ID , '_shipping_address_2' , $shipping_addrss_2);
$shipping_city_val = update_post_meta( $current_user_posts[0]->ID , '_shipping_city' , $shipping_city);
$shipping_state_val = update_post_meta( $current_user_posts[0]->ID , '_shipping_state' , $shipping_state);
$shipping_postcode_val = update_post_meta( $current_user_posts[0]->ID , '_shipping_postcode' , $shipping_postcode);
//echo '</pre>';
}
			$terms = get_post_meta( $post_id , 'pod_apply' );
			//print_r($terms);
            if ($terms)
                echo $terms[0];
            else
                _e( 'Null', 'your_text_domain' );
            break;

        

    }
}


function admin_post_list_add_export_button( $which ) {
    global $typenow;
    // echo "<pre>";
    // print_r($which);
    // print_r($typenow);
  
    if ( 'qrcode' === $typenow && 'top' === $which ) {
        ?>
        <input type="submit" name="export_all_posts" class="button button-primary" value="<?php _e('Export Data'); ?>" />
        <?php
    }
}
 
add_action( 'manage_posts_extra_tablenav', 'admin_post_list_add_export_button', 20, 1 );

    
function func_export_all_posts() {
    if(isset($_GET['export_all_posts'])) {
        $arg = array(
            'post_type' => 'qrcode',
            'post_status' => 'publish',
            'posts_per_page' => -1,
			'meta_key'         => 'pod_apply',
			'meta_value'       => true,
        );
        $csv_name = 'QrcodeData'.date(Ymdhis);
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="'.$csv_name.'.csv"');
            $file = fopen('php://output', 'wb');
            fputcsv($file, array('Order ID','Post ID','First Name', 'Last Name', 'City','State','Zipcode','Street address','Shipping First Name','Shipping Last Name','Shipping Country','Shipping Street address  1','Shipping Street address 2','Shipping City','Shipping State','Shipping Zipcode','Qrcode'));
  
        global $post;
        $arr_post = get_posts($arg);
        if ($arr_post && $arr_post!='' ) {
            
            foreach ($arr_post as $post) {
               
                setup_postdata($post);
                $id = get_the_ID();
                $userId = get_the_author_meta( 'ID' );
                    $customer_orders = get_posts(array(
                        'meta_value' => $userId,
                        'post_type' => wc_get_order_types(),
                        'post_status' => array_keys(wc_get_order_statuses()), 'post_status' => array('completed'),
                    ));
                    //$Order_id = []; 
                    foreach ($customer_orders as $customer_order) {
                        $orderq = wc_get_order($customer_order);
                        $Order_id =  $orderq->get_id();
                    }
                
                
                //$owner = get_post_meta($id,'owner_s_name',true);
                //$owner_address = get_post_meta($id,'address_owner',true);
                //$owner_phone = get_post_meta($id,'phoneowner',true);
                $owner_email = get_post_meta($id,'email_owner',true);
				 $first_name = get_post_meta($id,'first_name',true);
				  $last_name = get_post_meta($id,'last_name',true);
				   $billing_city = get_post_meta($id,'_billing_city ',true);
				    $billing_state = get_post_meta($id,'_billing_state',true);
					$billing_postcode = get_post_meta($id,'_billing_postcode',true);
					$billing_address_1 = get_post_meta($id,'_billing_address_1',true);
					
					$shipping_address_first_name = get_post_meta($id,'_shipping_first_name',true);
					$shipping_address_last_name = get_post_meta($id,'_shipping_last_name',true);
					$shipping_address_country = get_post_meta($id,'_shipping_country',true);
					$shipping_address_1 = get_post_meta($id,'_shipping_address_1',true);
                    $shipping_address_2 = get_post_meta($id,'_shipping_address_2',true);
                    $shipping_address_city = get_post_meta($id,'_shipping_city',true);
                    $shipping_address_state = get_post_meta($id,'_shipping_state',true);
                    $shipping_address_postcode = get_post_meta($id,'_shipping_postcode',true);

                $qrcode = get_post_meta($id,'qr_code_url',true);
                fputcsv($file, array($Order_id,$id,$first_name, $last_name,$billing_city,$billing_state,$billing_postcode,$billing_address_1,$shipping_address_first_name,$shipping_address_last_name,$shipping_address_country,$shipping_address_1,$shipping_address_2,$shipping_address_city, $shipping_address_state,$shipping_address_postcode,$qrcode));
            }
  
            exit();
        }
    }
}
 
add_action( 'init', 'func_export_all_posts' );

include(get_stylesheet_directory().'/qrcode.php');
add_action( 'save_post', 'wpdocs_notify_subscribers', 10, 2 );
 
function wpdocs_notify_subscribers( $post_id, $post ) {
    if ( $post->post_type == 'qrcode' ){
        $qrname = $post_id.'.png';
        update_post_meta( $post_id, 'qr_code_url', $qrname);
        // global $wpdb;

        $qrlink = get_permalink( $post );
        // $qc = new QrCode();
        // // Create URL Code
        // $qc->URL($qrlink);
        // // Save QR Code
        // $qc->QRCODE(100,$qrname);
        $qc = new QRCODE();
// Create URL Code
$qc->URL($qrlink);
// Save QR Code
$qc->QRCODE(100,$qrname);
// die;

     }
  }
  
function wpd_default_title_filter( $post_title, $post ) {
    if( 'qrcode' == $post->post_type ) {
        return $post->ID;
    }
    return $post_title;
}
add_filter( 'default_title', 'wpd_default_title_filter', 20, 2 );



function append_slug($data) {
global $post_ID;

if (!empty($data['post_name']) && $data['post_status'] == "publish" && $data['post_type'] == "qrcode") {

        if( !is_numeric(substr($data['post_name'], -4)) ) {
            $random = rand(1111,9999);
            $data['post_name'] = sanitize_title($data['post_title'], $post_ID);
            $data['post_name'] .= '-' . $random;
        }

}
 return $data; } add_filter('wp_insert_post_data', 'append_slug', 10);
 
 /** Function to Redirect http to https **/
add_action('template_redirect', 'redirect_core', 50);
add_action('init', 'redirect_core', 50);
add_action('wp_loaded', 'redirect_core', 50);
function redirect_core(){
  if (!is_ssl()) {
    wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 301);
    exit();
  }
}
 
 add_action('wp_logout','ps_redirect_after_logout');
function ps_redirect_after_logout(){
         wp_redirect( 'https://icepetcare.com/member-login' );
         exit();
}


/** Function to Remove field from checkout page **/
// Removes Order Notes Title - Additional Information & Notes Field
add_filter( 'woocommerce_enable_order_notes_field', '__return_false', 9999 );



// Remove Order Notes Field
add_filter( 'woocommerce_checkout_fields' , 'remove_order_notes' );

function remove_order_notes( $fields ) {
     unset($fields['order']['order_comments']);
     return $fields;
}

/* Change Default Order Status from On-HOld for Purchase Orders to Completed */
add_action( 'woocommerce_order_status_changed', 'woocommerce_auto_processing_orders');
function woocommerce_auto_processing_orders( $order_id ) {
    if ( ! $order_id )
        return;

    $order = wc_get_order( $order_id );

    // If order is "on-hold" update status to "processing"
    if( $order->has_status( 'on-hold' ) ) {
        $order->update_status( 'Completed' );
    }
}


add_action( 'woocommerce_payment_complete_order_status', 'wc_auto_complete_paid_order', 10, 3 );
function wc_auto_complete_paid_order( $status, $order_id, $order ) {
    return 'completed';
}

function auto_update_orders_status_from_processing_to_completed(){
    // Get all current "processing" customer orders
    $processing_orders = wc_get_orders( $args = array(
        'numberposts' => -1,
        'post_status' => 'wc-pending',
    ) );
    if(!empty($processing_orders))
        foreach($processing_orders as $order)
            $order->update_status( 'completed' );
}
add_action( 'init', 'auto_update_orders_status_from_processing_to_completed' );



/* Function to Customize Reset password mail content */

add_filter( 'retrieve_password_message', 'my_retrieve_password_message', 10, 4 );
function my_retrieve_password_message( $msg, $key, $user_login, $user_data ) {
// Create new message

  $msg  = '<div><p>Hello!</p1>';
  $msg .= '<p>You asked us to reset your password for your account. </p>';
  
  $msg .= '<p>To reset your password, please <b><a href='.site_url( "wp-login.php?action=rp&key=$key&login=".rawurlencode($user_login),'login').'> click here.</a></b>';

  //$msg .= '<p>'.site_url( "wp-login.php?action=rp&key=$key&login=".rawurlencode($user_login),'login').'</p>';
  $msg .= '<p>If you did not make this request, please contact us at <b><a href="mailto:info@icepetcare.com">info@icepetcare.com</a></b></p> <br><br> <p>Thank you,</p><p>ICEpetcare Team</p></div>';

 
  return $msg;

}


