<?php
/*

* Plugin Name: Final Task

* Description:The Product Plugin is for Demo Purpose.

* Version: 1.0.0

* Author: Zehntech Technologies Pvt. Ltd.

* Author URI: https://www.zehntech.com/

* License: GPL2

* License URI: https://www.gnu.org/licenses/gpl-2.0.html

* Text Domain: Demo


*/

defined('ABSPATH') || exit;


class finaltask
{

   function __construct()
   {

      wp_enqueue_script('jquery');

      /*  On Activation Create the Product Page and custom Post type product(Code in Post-type.php) */
      add_action('init', 'custom_product_post_type');



      /*Add the Css files */

      wp_enqueue_style('my-custom-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
      wp_enqueue_style('my-custom-bootstrap', plugin_dir_url(__FILE__) . 'assets/css/bootstrap.css');
      wp_enqueue_style('my-custom-slider', plugin_dir_url(__FILE__) . 'assets/css/image-slider.css');
      wp_enqueue_style('my-custom-price-css', plugin_dir_url(__FILE__) . 'assets/css/price-range.css');


     /*Add the js files */

      wp_enqueue_script('image-product-js', plugin_dir_url(__FILE__) . 'assets/js/lightsider.js');
      wp_enqueue_script('product-show-js', plugin_dir_url(__FILE__) . 'assets/js/custom-filter.js');
      wp_localize_script('product-show-js', 'custom_sidebar_ajax', array('ajax_url' => admin_url('admin-ajax.php')));

      wp_register_script( 'for_jquery', 'https://code.jquery.com/ui/1.13.2/jquery-ui.js', null, null, true );
       wp_enqueue_script('for_jquery');



      /* override the single product page */
      add_filter('single_template', array($this, 'custom_single_post_template'));


      add_shortcode('products_page', 'my_posts');

       /*show product on window load*/
      add_action('wp_ajax_load_posts', 'load_posts'); 
      add_action('wp_ajax_nopriv_load_posts', 'load_posts');
       

      $this->required();


   }



   private function required()
   {

      /*Create the Custom Post Type (Product) */
      require_once __DIR__ . '/includes/post-type.php';

      /* meta gallery for Product */
      require_once __DIR__ . '/includes/meta-gallery.php';

   
     /* Short code function & Search */
      require_once __DIR__ . '/includes/show-product.php';

   }

   function custom_single_post_template($template)
   {
      if (is_single()) {
         $template = plugin_dir_path(__FILE__) . 'templates/single-product.php';

      }
      return $template;
   }


}

$finalobj = new finaltask();