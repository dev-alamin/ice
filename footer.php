



<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package petapalozza
 */
?>
<style type="text/css">
.copyright {text-align: center;}
.bottom-footer-container-overlay {height: auto;}

</style>
</div><!-- #content / .container end-->
<div class="bottom-footer-container">
    
    <div class="bottom-footer-container-overlay">
    <?php
        if (is_active_sidebar('bottom-1') || is_active_sidebar('bottom-2') || is_active_sidebar('bottom-3') ) {
    ?>
        <div id="bottom">
            <div class="container">	
                <?php petapalozza_count_bottom_sidebar() ?>
            </div>
        </div><!-- .container end-->
    <?php } ?>

    <footer id="colophon" class="site-footer">
        <div class="container">
            <?php if (petapalozzawp_option('enable_disable_footer_copyright') != '0') : ?>
                <div class="row">
                    
                        <div class="copyright">
                            <?php
                                if ($copyright_text = petapalozzawp_option('custom_copyright')) :

                                    echo html_entity_decode(esc_attr($copyright_text));

                                else:

                                    echo html_entity_decode(esc_attr(PETAPALOZZA_COPYRIGHT));

                                endif;

                                /* Theme Credit Notes */

                                /**$credit_notes = " Theme By <a href='https://essentialwebapps.com/' target='_blank'>EssentialWebApps</a>."; **/

                                if (1 == petapalozzawp_option('disable_theme_credit')) {
                                    $credit_notes = "";
                                }

                                echo esc_html( $credit_notes );
                                ?>
                            <div class="footerlnk">
                <?php if (has_nav_menu('footer-menu', 'petapalozza')) : ?>
                            <nav>
                            <?php
                            wp_nav_menu(array(
                                'container' => '',
                                'menu_class' => 'footer-menu',
                                'theme_location' => 'footer-menu')
                            );
                            ?>
                            </nav>
    <?php endif; ?>
                    </div>
                        </div> <!-- end .copyright  -->
                   
                    
                </div><!-- .row -->
<?php endif; ?>
        </div> <!-- .container end -->
    </footer><!-- #colophon -->
    
    </div> <!-- end .bottom-footer-container-overlay  -->
</div> <!-- end .bottom-footer-container  -->
</div><!-- #page -->
<?php 
wp_body_open();
wp_footer(); ?>
<script src="<?php echo get_stylesheet_directory_uri() . '/assets/js/jquery.PrintArea.min.js' ?>"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/jquery.PrintArea.min.js"></script> -->
<script language="javascript">
/**function PrintMe(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}
**/


jQuery(document).ready(function(){
    jQuery("#printButton").click(function(){
        var mode = 'iframe'; //popup
        var close = mode == "popup";
        var options = { mode : mode, popClose : close};
        jQuery("#divid").printArea( options );
    });
});
</script>

<script>

 
 /**const elm = document.querySelector(".logo_qr");
 html2canvas(elm).then(function(canvas) {
    document.querySelector(".result")
	append(canvas);
}); **/
 

 
 </script>
 
 <script src="https://files.codepedia.info/files/uploads/iScripts/html2canvas.js"></script>
 </body>
</html>