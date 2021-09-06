<table class="ec_store_table">
	<thead>
    	<tr>
        	<?php for( $i=0; $i<count( $columns ); $i++ ){ ?>
        	<td><?php if( count( $labels ) > $i ){ echo $labels[$i]; } ?></td>
            <?php }?>
        </tr>
    </thead>
    <tbody>
    	<?php
		foreach( $products as $product ){ 
		if( get_option( 'ec_option_use_old_linking_style' ) ){
			$link = $storepage . $permalink_divider . "model_number=" . $product['model_number'];
		}else{
			$link = get_permalink( $product['post_id'] );
		}
		?>
    	<tr>
        	<?php for( $i=0; $i<count( $columns ); $i++ ){ ?>
            
            	<?php if( $columns[$i] == 'price' ){ ?>
        			<td><?php echo $GLOBALS['currency']->get_currency_display( $product['price'] ); ?></td>
            	
				<?php }else if( $columns[$i] == 'details_link' ){ ?>
            		<td><a href="<?php echo $link; ?>"><?php echo $view_details; ?></a></td>
            	
				<?php }else if( $columns[$i] == 'title' ){ ?>
                	<td><a href="<?php echo $link; ?>"><?php echo $product['title']; ?></a></td>
                
				<?php }else if( isset( $product[$columns[$i]] ) ){ ?>
        			<td><?php echo $product[$columns[$i]]; ?></td>
            	
				<?php }?>
			
			<?php }?>
        </tr>
        <?php }?>
    </tbody>
</table>