<div class="ec_featured_product prod<?php echo $i; ?>">
  <div class="ec_featured_product_image">
    <?php $featured_product->display_product_image_set( "medium", "ec_featured_product_", "ec_featured_product_click" ); ?>
  </div>
  <div class="ec_featured_product_title">
    <?php $featured_product->display_product_title_link(); ?>
  </div>
  <div class="ec_featured_product_rating">
    <div class="ec_featured_product_rating_stars">
      <?php $featured_product->display_product_stars(); ?>
    </div>
    <div class="ec_featured_product_rating_num_ratings">(
      <?php $featured_product->display_product_number_reviews(); ?>
      )</div>
  </div>
  
</div>
