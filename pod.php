<?php  
/* 
Template Name: POD Template 
*/  
get_header();   
?>
<style>
#qrid {display:none;}
input#pid {padding: 8px 10px 10px 8px;border: 1px solid #000;}
input.pod-submit {padding: 9px 15px 9px 15px;font-weight: 600;background: #2E385C;color: #fff; border: 0px;
    border-radius: 4px !important;}
</style>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div class="entry-content" id="podclass">
<div class="col-sm-4"></div>

<div class="col-sm-4">
<form method='GET' action='' class="podform">
<input type="text" value="" name="pid" id="pid" class="podid">
<input type='submit' value='Submit' class="pod-submit">
</form>
<?php $author = $_GET['pid']; ?>
<div class="qrclass" id="qrid<?php echo $author ?>">  
<?php
$args = array(
    'post_type' => 'qrcode',
    'author' => $author, //Author ID
	'posts_per_page' => 1,
    );

$custom_query = new WP_Query( $args );

if ( $custom_query->have_posts() ) :
    while ( $custom_query->have_posts() ) : $custom_query->the_post(); ?>
       
    <a class="author-pet-info" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >View</a>
	<a class="author-qrcode" href="<?php the_permalink(); ?>/?q=qrcode" title="<?php the_title_attribute(); ?>" >View QR Code</a>
       
 <?php endwhile;
else :
    echo 'No posts found...';
endif;
?>
</div>
</div>
</div>

<div class="col-sm-4"></div>
</article>

<script>
jQuery(document).ready(function(){
    
jQuery('#qrid.pod-submit').click( function(){
    if ( jQuery(this).hasClass('qrclass') ) {
        jQuery(this).removeClass('qrclass');
    } else {
        jQuery('.qrclass').removeClass('qrclass');
        jQuery(this).addClass('qrclass');    
    }
});	
});
</script>

<?php get_footer();
