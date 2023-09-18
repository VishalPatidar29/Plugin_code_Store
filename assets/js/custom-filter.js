jQuery(document).ready(function () {

  /* for max and min price */
  var max_value = jQuery("#maxprice").val();
  var min_value = jQuery("#minprice").val();

  /* for price slide*/
  jQuery("#slider-range").slider({
    range: true,
    min: 0,
    max: max_value,
    values: [min_value, max_value],
    slide: function (event, ui) {
      jQuery("#amount").val(ui.values[0] + " - " + ui.values[1]);
    },
    stop: function (event, ui) {
      search_item(1);
    },
  });




  jQuery("#amount").val(
    jQuery("#slider-range").slider("values", 0) +
      " - " +
      jQuery("#slider-range").slider("values", 1)
  );


  /* filter product pagination */
  jQuery(document).on("click", ".paginationsearch a", function (e) {
    e.preventDefault();
    var page = jQuery(this).attr("href").split("=").pop();
    search_item(page);
  });



  /* Search function foProduct*/

  function search_item(page) 
  {

    var searchQuery = jQuery("#search-input").val();
    var pricerange = jQuery("#amount").val();

    var selectedCategories = [];

    jQuery(".category-filter:checked").each(function () {
      selectedCategories.push(jQuery(this).val());
    });

    jQuery.ajax({
      url: custom_sidebar_ajax.ajax_url,
      type: "POST",
      data: {
        action: "custom_sidebar_search", 
        search_query: searchQuery,
        categories: selectedCategories,
        price_range: pricerange,
        page: page,
      },
      success: function (response) {

        jQuery("#custom-sidebar-results").html(response);

      },
    });
  }


  /* Search bar ajax */
  jQuery("#search-input").on("input", function () {
    search_item(1);
  });

  /* Category clicked ajax */
  jQuery(".category-filter").on("click", function () {
    search_item(1);
  });
});


/*first time window load  show product */
window.onload = function(){
  load_item(1);

  function load_item(page) {
    jQuery.ajax({
      url: custom_sidebar_ajax.ajax_url,
      type: "POST",
      data: {
        action: "load_posts",
        page: page,
      },
      success: function (response) {

        jQuery("#custom-sidebar-results").html(response);

      },
    });
  }

  jQuery(document).on("click", ".pagination a", function (e) {
    e.preventDefault();
    var page = jQuery(this).attr("href").split("=").pop();
    load_item(page);
  });

  
};
