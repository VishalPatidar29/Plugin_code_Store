<?php

/* Add the meta gallery Option in Product */
add_action('admin_init', 'property_gallery_add_metabox');
add_action('admin_head-post.php', 'property_gallery_styles_scripts');
add_action('admin_head-post-new.php', 'property_gallery_styles_scripts');
add_action('save_post', 'property_gallery_save');

function property_gallery_add_metabox(){
    add_meta_box(
      'post_custom_gallery',
      'Image Gallery',
      'property_gallery_metabox_callback',
      'product', 
      'normal',
      'core'
    );
  }

//  Call Back Function of meta box
  function property_gallery_metabox_callback(){

    wp_nonce_field( basename(__FILE__), 'sample_nonce' );

    global $post;

    $gallery_data = get_post_meta( $post->ID, 'gallery_data', true );
    ?>

    <div id="gallery_wrapper">
      <div id="img_box_container">
      <?php 
      if ( isset( $gallery_data['image_url'] ) ){

        for( $i = 0; $i < count( $gallery_data['image_url'] ); $i++ ){

        ?>

        <div class="gallery_single_row dolu">
          <div class="gallery_area image_container ">

          <img class="gallery_img_img" src="<?php esc_html_e( $gallery_data['image_url'][$i] ); ?>" height="55" width="55" onclick="open_media_uploader_image_this(this)"/>
         
          <input type="hidden"
               class="meta_image_url"
               name="gallery[image_url][]"
               value="<?php esc_html_e( $gallery_data['image_url'][$i] ); ?>"
            />
          </div>
          <div class="gallery_area">

          <span class="button remove" onclick="remove_img(this)" title="Remove"><i class="fas fa-trash-alt"></i></span>

          </div>
          <div class="clear" >
        </div> 
        </div>
        
        <?php
        }
      }
      ?>
      </div>
      <div style="display:none" id="master_box">
        <div class="gallery_single_row">
          <div class="gallery_area image_container" onclick="open_media_uploader_image(this)">
            <input class="meta_image_url" value="" type="hidden" name="gallery[image_url][]" />
          </div> 
          <div class="gallery_area"> 
            <span class="button remove" onclick="remove_img(this)" title="Remove"><i class="fas fa-trash-alt"></i></span>
          </div>
          <div class="clear"></div>
        </div>
      </div>
      <div id="add_gallery_single_row">
        <input class="button add" type="button" value="+" onclick="open_media_uploader_image_plus();" title="Add image"/>
      </div>
    </div>
    <?php
  }


  function property_gallery_styles_scripts(){


    global $post;
    if( 'product' != $post->post_type )
        return;
    
?>

<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/solid.js" integrity="sha384-+Ga2s7YBbhOD6nie0DzrZpJes+b2K1xkpKxTFFcx59QmVPaSA8c7pycsNaFwUK6l" crossorigin="anonymous"></script>
<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/fontawesome.js" integrity="sha384-7ox8Q2yzO/uWircfojVuCQOZl+ZZBg2D2J5nkpLqzH1HY0C1dHlTKIbpRz/LG23c" crossorigin="anonymous"></script>

        <script type="text/javascript">

        function remove_img(value) {
            var parent=jQuery(value).parent().parent();
            parent.remove();
        }

	var media_uploader = null;

	function open_media_uploader_image(obj){
		media_uploader = wp.media({
			frame:    "post", 
			state:    "insert", 
			multiple: false
		});

		media_uploader.on("insert", function(){

			var json = media_uploader.state().get("selection").first().toJSON();
			var image_url = json.url;
			var html = '<img class="gallery_img_img" src="'+image_url+'" height="55" width="55" onclick="open_media_uploader_image_this(this)"/>';
			console.log(image_url);
			jQuery(obj).append(html);
			jQuery(obj).find('.meta_image_url').val(image_url);
		});
		media_uploader.open();
	}

	function open_media_uploader_image_this(obj){
		media_uploader = wp.media({
			frame:    "post", 
			state:    "insert", 
			multiple: false
		});

		media_uploader.on("insert", function(){
			var json = media_uploader.state().get("selection").first().toJSON();
			var image_url = json.url;
			console.log(image_url);
			jQuery(obj).attr('src',image_url);
			jQuery(obj).siblings('.meta_image_url').val(image_url);
		});
		media_uploader.open();
	}

	function open_media_uploader_image_plus(){

		media_uploader = wp.media({
			frame:    "post", 
			state:    "insert", 
			multiple: true 
		});

		media_uploader.on("insert", function(){

			var length = media_uploader.state().get("selection").length;
			var images = media_uploader.state().get("selection").models

			for(var i = 0; i < length; i++){

				var image_url = images[i].changed.url;
				var box = jQuery('#master_box').html();
				jQuery(box).appendTo('#img_box_container');
				var element = jQuery('#img_box_container .gallery_single_row:last-child').find('.image_container');
				var html = '<img class="gallery_img_img" src="'+image_url+'" height="55" width="55" onclick="open_media_uploader_image_this(this)"/>';
				element.append(html);
				element.find('.meta_image_url').val(image_url);
				console.log(image_url);		
			}
		});
        
		media_uploader.open();
	}
	jQuery(function() {
            jQuery("#img_box_container").sortable();
        });
    </script>

    <?php


  }


  function property_gallery_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$is_autosave = wp_is_post_autosave( $post_id );
	$is_revision = wp_is_post_revision( $post_id );
	$is_valid_nonce = ( isset( $_POST[ 'sample_nonce' ] ) && wp_verify_nonce( $_POST[ 'sample_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

	if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
			return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}


         
        if ( isset( $_POST['post_type'] ) && 'product' != $_POST['post_type'] ) {
	   return;
        }

       if ( isset( $_POST['gallery'] ) && 'product' != $_POST['gallery'] ) {

		// Build array for saving post meta
		$gallery_data = array();
		for ($i = 0; $i < count( $_POST['gallery']['image_url'] ); $i++ ){
			if ( '' != $_POST['gallery']['image_url'][$i]){
				$gallery_data['image_url'][]  = $_POST['gallery']['image_url'][ $i ];
			}
		}

		if ( $gallery_data ) 
			update_post_meta( $post_id, 'gallery_data', $gallery_data );
		else 
			delete_post_meta( $post_id, 'gallery_data' );
	} 
	// Nothing received, all fields are empty, delete option
	else{
		delete_post_meta( $post_id, 'gallery_data' );
	}
// }

  }



?>
