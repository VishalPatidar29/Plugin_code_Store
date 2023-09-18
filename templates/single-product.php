<?php 

get_header();

/* Display gallery images */
             global $post;

            $gallery_images = array();
            $gallery_images = get_post_meta( $post->ID, 'gallery_data', true );
            $price = get_post_meta($post->ID, '_custom_product_price', true);
            $Description = get_post_meta($post->ID, '_product_description', true);



?>

<div class="container-fluid mt-2 mb-3">
        <div class="row no-gutters">
        <div class="col-md-5 pr-2">
        <div class="card">
        <div class="demo">
        <ul id="lightSlider">

<?php
            if ($gallery_images) {
                $i = 0;
      foreach ($gallery_images as $image) {
      foreach($image as $data)
      {
    
                     
    echo '<li data-thumb="'. esc_url($data).'"> <img src="' . esc_url($data).'"></li>';
                    $i++;
                         
                       }   
                  }
              }
              ?>

        </ul>
        </div>
        </div>
        </div>

        <div class="col-md-7">
        <div class="card">

        <div class="about"> <span class="font-weight-bold"><?php the_title(); ?></span>
        <h4 class="font-weight-bold">₹ <?php echo $price;?></h4>
        </div>
       
        <hr>
        <div class="product-description">
       
        <div class="mt-2"> <span class="font-weight-bold">Description :- </span>
        <p><?php  echo $Description; ?></p>
       
        </div>
        <div class="buttons"> <button class="btn btn-warning btn-long buy">Buy it Now</button></div>
       
        </div>
        </div>
        </div>
        
        </div>
        </div>


    <script>
        jQuery('#lightSlider').lightSlider({
        gallery: true,
        item: 1,
        loop: true,
        slideMargin: 0,
        thumbItem: 9
        });
        </script> 



   <?php

get_footer();

?>     	
