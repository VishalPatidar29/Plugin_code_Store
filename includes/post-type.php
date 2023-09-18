<?php

function custom_product_post_type()
{

    $labels = array(
        'name' => 'Products',
        'singular_name' => 'Product',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Product',
        'edit_item' => 'Edit Product',
        'new_item' => 'New Product',
        'view_item' => 'View Product',
        'search_items' => 'Search Products',
        'not_found' => 'No products found',
        'not_found_in_trash' => 'No products found in Trash',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'publicly_queryable' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'product'),
        'capability_type' => 'post',
        'menu_icon' => 'dashicons-cart',
        'supports' => array('title', 'editor', 'thumbnail', ),
    );

    register_post_type('product', $args);

    /*add the category option in custom post type*/
    register_taxonomy_for_object_type('category', 'product');



    /*add the Product page with shortcode*/
    $page_title = 'Product';
    $page_content = '[products_page]';
    $page_check = get_page_by_title($page_title);

    if (!$page_check) {
        $page = array(
            'post_type' => 'page',
            'post_title' => $page_title,
            'post_name' => 'product-item',
            'post_content' => $page_content,
            'post_status' => 'publish',
            'post_author' => 1,
        );

        wp_insert_post($page);
    }




    function custom_product_price_meta_box()
    {
        add_meta_box('custom_product_price', 'Product Price', 'custom_product_price_callback', 'product', 'normal', 'high');
    }


    /* Callback function to display the 'price' field in the meta box */
    function custom_product_price_callback($post)
    {

        $price = get_post_meta($post->ID, '_custom_product_price', true);
        ?>
        <label for="custom_product_price">Price:</label>
        <input type="text" id="custom_product_price" name="custom_product_price" value="<?php echo esc_attr($price); ?>">
        <?php
    }

    /* Save 'price' field data when saving/updating a product*/
    function custom_product_save_price($post_id)
    {
        if (isset($_POST['custom_product_price'])) {
            update_post_meta($post_id, '_custom_product_price', sanitize_text_field($_POST['custom_product_price']));
        }
    }



    /*  Add the meta box for Price */
    add_action('add_meta_boxes', 'custom_product_price_meta_box');
    add_action('save_post_product', 'custom_product_save_price');




    /* Add custom meta box for product description */
    function add_product_description_meta_box()
    {
        add_meta_box(
            'product_description_meta_box',
            'Product Description',
            'display_product_description_meta_box',
            'product',
            'normal',
            'high'
        );
    }


    /* Callback function to display the meta box content */
    function display_product_description_meta_box($post)
    {

        $product_description = get_post_meta($post->ID, '_product_description', true);

        ?>
        <textarea id="product_description" name="product_description" rows="5"
            style="width: 100%;"><?php echo esc_textarea($product_description); ?></textarea>
        <?php
    }

    /* Save the meta box data when the post is saved */
    function save_product_description_meta_data($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;
        if (!current_user_can('edit_post', $post_id))
            return;

        if (isset($_POST['product_description'])) {
            $product_description = sanitize_text_field($_POST['product_description']);
            update_post_meta($post_id, '_product_description', $product_description);
        }
    }


    /* Add the Product Description Custom meta field  */
    add_action('add_meta_boxes', 'add_product_description_meta_box');
    add_action('save_post', 'save_product_description_meta_data');







}