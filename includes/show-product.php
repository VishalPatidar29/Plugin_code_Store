<?php

/*side bar and show Product */

add_action('wp_ajax_custom_sidebar_search', 'custom_sidebar_search_handler');
add_action('wp_ajax_nopriv_custom_sidebar_search', 'custom_sidebar_search_handler');

function my_posts()
{

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
    );

    $query = new WP_Query($args);
    $min_price = 0;
    $max_price = 0;

    if ($query->have_posts()):


        while ($query->have_posts()):
            $query->the_post(); // Set up the post data

            $id = get_the_ID(); // Get the post ID after setting up the post data
            $price = get_post_meta($id, '_custom_product_price', true);

            if (!$min_price || $price < $min_price) {
                $min_price = $price;
            }

            if (!$max_price || $price > $max_price) {
                $max_price = $price;
            }

        endwhile;

    endif;

    ob_start();
    ?>

    <div id="custom-sidebar">

        <!-- Search bar -->
        <div class="sidebar-section">
            <h2>Search</h2>
            <input type="text" id="search-input" placeholder="Search...">

        </div>

        <!-- Category box -->
        <div class="sidebar-section">
            <h2>Categories</h2>
            <?php
            $categories = get_categories();
            if ($categories) {
                echo '<ul>';
                foreach ($categories as $category) {

                    echo '<li>';
                    // Replace the <a> tag with an <input type="checkbox"> for each category
                    echo '<label>';
                    echo '<input type="checkbox" class="category-filter" name="category-filter" value="' . esc_attr($category->slug) . '"> ';
                    echo esc_html($category->name);
                    echo '</label>';
                    echo '</li>';



                }
                echo '</ul>';
            }
            ?>
        </div>

        <!-- Price range filter -->
        <div class="sidebar-section">

            <input type="hidden" id="maxprice" name="maxprice" value="<?php echo $max_price ?>">
            <input type="hidden" id="minprice" name="minprice" value="<?php echo $min_price ?>">

            <h2>Price Range</h2>

            <p>

                <input type="text" id="amount" readonly style="border:0; color:#f6931f; font-weight:bold;">
            </p>

            <div id="slider-range"></div>

        </div>


    </div>

    <div id="custom-sidebar-results">

    </div>

    <?php

    wp_reset_postdata();

    $html = ob_get_clean();

    return $html;
}




// AJAX handler for searching products by title
function custom_sidebar_search_handler()
{
    $search_query = sanitize_text_field($_POST['search_query']);
    $selected_categories = $_POST['categories'];
    $priceRange = ($_POST['price_range']);
    $page = $_POST['page'];


    // Query products by title
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 6,
        's' => $search_query,
        'paged' => $page,

    );


    if (!empty($selected_categories)) {
        $args['category_name'] = implode(',', $selected_categories);
    }
    if (!empty($priceRange)) {
        $priceval = explode("-", $priceRange);
        $mindata = $priceval[0];
        $maxdata = $priceval[1];

        $args['meta_query'] = array(
            'relation' => 'AND',
            array(
                'key' => '_custom_product_price',
                'value' => $mindata,
                'type' => 'NUMERIC',
                'compare' => '>='
            ),
            array(
                'key' => '_custom_product_price',
                'value' => $maxdata,
                'type' => 'NUMERIC',
                'compare' => '<=',
            ),
        );
    }

    $query = new WP_Query($args);

    if ($query->have_posts()):

        echo '<div class="product-container">';

        while ($query->have_posts()):
            $query->the_post(); 

            $id = get_the_ID(); 
            $price = get_post_meta($id, '_custom_product_price', true);

            ?>

            <div class="product">
                <?php echo the_post_thumbnail(); ?>

                <h5>
                    <?php echo get_the_title(); ?>
                </h5>

                <p>₹<?php echo $price; ?>
                </p>

                <h5>
                    <?php
                    $terms = get_the_terms(get_the_ID(), 'category');
                    foreach ($terms as $term) {

                        $product_cat = $term->name;
                        echo 'Category - ' . $product_cat;
                        break;
                    }
                    ?>
                </h5>
                <a href="<?php the_permalink($id); ?>"><button type="button" class="read-button">Read More</button></a>


            </div>

            <?php

                endwhile;
        echo '</div>';

        if ($query->max_num_pages > 1) {
            
            ?>
            
            <div class="pagination">

                <?php 
                
                echo paginate_links(
                    array(
                        'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                        'format' => '?page=%#%',
                        'current' => max(1, $page),
                        'total' => $query->max_num_pages,
                        'prev_text' => '<i class="fas fa-angle-left"></i>Previous',
                        'next_text' => 'Next <i class="fas fa-angle-right"></i>',
                    )
                );
                echo '</div>';
        } 
        
        ?>

        <?php else: ?>
            <h3 id="nopro">
                No Product Found
            </h3>
        <?php endif; 
        
        wp_reset_postdata();
        wp_die();
}





/* AJAX handler for show all the products */
function load_posts() {

    $page = $_POST['page'];
    
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 6, /* Retrieve all posts -1 */
        'paged' => $page,
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) :
        
        echo '<div class="product-container">';
    
        while ($query->have_posts()) : $query->the_post(); 
    
            $id = get_the_ID(); 
            $price = get_post_meta($id, '_custom_product_price', true);
    
            ?>
           
            <div class="product">

                <?php echo the_post_thumbnail();
                 ?>

                <h5>
                    <?php echo get_the_title(); ?>
                </h5>
                
                <p>₹ <?php echo $price; ?></p>
                      
                <h5>
           <?php
                        $terms = get_the_terms(get_the_ID(), 'category');
           foreach($terms as $term) {
    
             $product_cat = $term->name;
                echo 'Category - '.$product_cat;
                  break;
              }
              ?>
            </h5>

                <a href="<?php the_permalink($id); ?>"><button type="button" class="read-button">Read More</button></a>
    
    
            </div>
    
            <?php
    
        endwhile;
        echo '</div>';
       
        if ($query->max_num_pages > 1) {  ?>
            <div class="pagination">
              <?php echo paginate_links(array(
                'base'    => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                'format'  => '?page=%#%',
                'current' => max(1, $page),
                'total'   => $query->max_num_pages,
                'prev_text' => '<i class="fas fa-angle-left"></i>Previous',
                'next_text' => 'Next <i class="fas fa-angle-right"></i>',
              )); 
             echo '</div>';
         } ?>  
    
       <?php else :  ?>
        <h3 id="nopro">No Product Found</h3>

    <?php 
    endif; 
    wp_reset_postdata();
    wp_die();

    }


?>