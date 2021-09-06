<div class="ec_manufacturer_widget">
  
  <?php for($i=0; $i<count( $manufacturers ); $i++){ ?>
  	<?php if( $manufacturers[$i]->product_count > 0 ){ ?>
	  <div><a href="<?php echo $filter->get_link_string( 3 ) . "&amp;manufacturer=" . $manufacturers[$i]->manufacturer_id; ?>" class="menu_link"><?php if( isset( $_GET['manufacturer'] ) && $_GET['manufacturer'] == $manufacturers[$i]->manufacturer_id ){ echo "<b>"; } ?><?php echo $manufacturers[$i]->name; ?> (<?php echo $manufacturers[$i]->product_count; ?>)<?php if( isset( $_GET['manufacturer'] ) && $_GET['manufacturer'] == $manufacturers[$i]->manufacturer_id ){ echo "</b>"; } ?></a>
      <?php if( isset( $_GET['manufacturer'] ) && $_GET['manufacturer'] == $manufacturers[$i]->manufacturer_id ){ ?>
      <a href="<?php echo $filter->get_link_string( 3 ); ?>" class="menu_link"> X </a>
      <?php }?>
      </div>
    <?php }?>
  <?php }?>
  
</div>
