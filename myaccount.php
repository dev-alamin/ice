<?php
/**
 * Template Name: My Aaccount 
 *
 **/
get_header(); ?>

<style type="text/css">
.qrcode-download.htqr-btn.htqr-btn-download {
    margin-top: 10px;
    width: 26%;
}
.post_count { display: none; }
.wpuf-author {display: none;}
.fldedit a:nth-child(2) {display: none;}
td.fldedit a {font-weight: 600;}
table tbody tr td {width: 20%;}

table tbody tr td:first-child {width: 40%;}
</style>


<?php  if(is_user_logged_in() && function_exists('pmpro_hasMembershipLevel') && pmpro_hasMembershipLevel())
{ ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="entry-content">
	  
	 <?php if (( 0 == count_user_posts( get_current_user_id(), "qrcode" ) && is_user_logged_in() && current_user_can('subscriber') && !current_user_can( 'manage_options' ))) { ?>
       <p class"btnhere">Please complete your Pet Information Form to view your QR code.</p>
	   <a href="<?php echo home_url('/my-pet-information-form'); ?>" class="button qrcode">Here</a>

	  <?php } else { ?>

	 <?php //echo do_shortcode('[htqrcode download = "true" print = "false" download_btn_txt ="Download Qrcode" colordark = "#2b3844"]Your Content Here.[/htqrcode]'); ?>
	<?php } ?>
	
	  
	  <?php echo do_shortcode('[wpuf_dashboard post_type="qrcode"]'); ?>
	
	
    	<?php echo do_shortcode('[pmpro_account]'); ?>
	
        <?php //the_content(); ?>
       
 
	<?php   
  $args = array(
    'status'    => array('wc-completed','wc-on-hold','wc-pending','wc-processing'),  
    'customer'  => get_current_user_id(),  
    );
  $pending_orders = wc_get_orders( $args );
  if( ! empty( $pending_orders ) ) { ?>
	 
	
    <?php } else { ?>
			<!--<a href="<?php echo home_url('/product/print-on-demand'); ?>" class="button qrcode">Print On Demand</a> -->
	<?php } ?>
     
		<?php
        wp_link_pages(array(
            'before' => '<div class="page-links">' . esc_html__('Pages:', 'petapalozza'),
            'after' => '</div>',
        ));
        ?>

    </div><!-- .entry-content -->
    
    <?php 
    ## ==> Define HERE the statuses of that orders 
$order_statuses = array('wc-completed');

## ==> Define HERE the customer ID
$customer_user_id = get_current_user_id(); // current user ID here for example

// Getting current customer orders
$customer_orders = wc_get_orders( array(
    'meta_key' => '_customer_user',
    'meta_value' => $customer_user_id,
    'post_status' => $order_statuses,
    'numberposts' => -1
) );


// Loop through each customer WC_Order objects
foreach($customer_orders as $order ){

    // Order ID (added WooCommerce 3+ compatibility)
    $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;

    // Iterating through current orders items
    foreach($order->get_items() as $item_id => $item){

        // The corresponding product ID (Added Compatibility with WC 3+) 
        $product_id = method_exists( $item, 'get_product_id' ) ? $item->get_product_id() : $item['product_id'];

        // Order Item data (unprotected on Woocommerce 3)
        if( method_exists( $item, 'get_data' ) ) {
             $item_data = $item->get_data();
             $subtotal = $item_data['order_id'];
        } else {
        $order = wc_get_order( $subtotal );
        }
        $date_obj = $order->get_date_completed(); ?>
        
        <div id="pmpro_account-invoices" class="pmpro_box">
         <table class="pmpro_table podtbl" width="100%">
        
         <tbody>
         <tr id="tblpod">
         <td><?php echo $date_obj->date('F d Y'); ?></td>
         <td class="cltbl voice">Member Card</td>
         <td class="cltbl amount">$<?php echo $order->get_total(); ?></td>
         <td class="cltbl tpd">Paid</td>
         </tr>
         </tbody>
         </table>
         </div>
        
    <?php } }  ?>
    
    
</article><!-- #post-## -->
<?php 
global $current_user;
	$current_user->membership_level = pmpro_getMembershipLevelForUser($current_user->ID);
	//echo 'Membership Level: ' . $current_user->membership_level->name;
} else { ?>   <?php wp_redirect( home_url('/member-checkout') ); exit; ?> <?php } ?>

<?php get_footer(); ?>