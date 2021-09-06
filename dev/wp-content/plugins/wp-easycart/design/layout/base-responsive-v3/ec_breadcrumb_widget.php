<?php
$ec_store_page_id = get_option('ec_option_storepage');
$store_page = get_permalink( $ec_store_page_id );

if( substr_count( $store_page, '?' ) )						$permalink_divider = "&";
else														$permalink_divider = "?";

$ec_filter = new ec_filter( 1 );
?>
<div id="breadcrumbs">
<?php 
	if( function_exists( 'bcn_display' ) ){ 
		bcn_display(); 
	}else { ?>
        <a href="<?php echo esc_url( home_url( ) ); ?>" class="breadcrumbs_home"><?php esc_html_e( 'Home' ) ?></a> <span class="raquo"><?php echo $divider; ?></span>
        <?php if( is_tag( ) ){ ?>
            <?php esc_html_e( 'Posts Tagged ' ) ?><span class="raquo">&quot;</span><?php single_tag_title( ); echo( '&quot;' ); ?>
        <?php } elseif (is_day()) { ?>
            <?php esc_html_e( 'Posts made in' ) ?> <?php the_time( 'F jS, Y' ); ?>
        <?php } elseif (is_month()) { ?>
            <?php esc_html_e( 'Posts made in' ) ?> <?php the_time( 'F, Y' ); ?>
        <?php } elseif (is_year()) { ?>
            <?php esc_html_e( 'Posts made in' ) ?> <?php the_time( 'Y' ); ?>
        <?php } elseif (is_search()) { ?>
            <?php esc_html_e( 'Search results for' ) ?> <?php the_search_query( ) ?>
        <?php } elseif (is_single( )) { ?>
        <?php
            if ( 'listing' == get_post_type() ) {
                $categories = get_the_terms( get_the_ID(), 'listing_type' );
                if ( $categories ) {
                    foreach( $categories as $category ) {
                        $catlink = get_term_link( $category );
                        echo ('<a href="'.esc_url( $catlink ).'">'.esc_html($category->name).'</a> '.'<span class="raquo">' . $divider . '</span> ');
                        break;
                    }
                }
            } else {
                $category = get_the_category();
                if ( $category ) {
                    $catlink = get_category_link( $category[0]->cat_ID );
                    echo ('<a href="'.esc_url($catlink).'">'.esc_html($category[0]->cat_name).'</a> '.'<span class="raquo">' . $divider . '</span> ');
                }
            }
        
            get_the_title( );
        ?>
		<?php } elseif (is_category()) { ?>
            <?php single_cat_title(); ?>
        <?php } elseif (is_tax()) { ?>
            <?php
                $et_taxonomy_links = array();
                $et_term = get_queried_object();
                $et_term_parent_id = $et_term->parent;
                $et_term_taxonomy = $et_term->taxonomy;

                while ( $et_term_parent_id ) {
                    $et_current_term = get_term( $et_term_parent_id, $et_term_taxonomy );
                    $et_taxonomy_links[] = '<a href="' . esc_url( get_term_link( $et_current_term, $et_term_taxonomy ) ) . '" title="' . esc_attr( $et_current_term->name ) . '">' . esc_html( $et_current_term->name ) . '</a>';
                    $et_term_parent_id = $et_current_term->parent;
                }

                if ( !empty( $et_taxonomy_links ) ) echo implode( ' <span class="raquo">' . $divider . '</span> ', array_reverse( $et_taxonomy_links ) ) . ' <span class="raquo">' . $divider . '</span> ';

                echo esc_html( $et_term->name );
            ?>
        <?php } elseif (is_author()) { ?>
            <?php
                global $wp_query;
                $curauth = $wp_query->get_queried_object();
            ?>
            <?php esc_html_e('Posts by '); echo ' ', $curauth->nickname; ?>
        <?php } elseif (is_page()) { 
            echo '<a href="'; the_permalink(); echo '">'; the_title( ); echo '</a>';	
        }; ?>
		<?php } 
		$post_id = get_the_ID();
		if( isset( $_GET['model_number'] ) && $ec_store_page_id == $post_id ){
			
			$db = new ec_db( );
			if( isset( $_GET['model_number'] ) )
				$model_number = $_GET['model_number'];
			else
				$model_number = "";
				
			if( isset( $_GET['menuid'] ) )
				$menuid = $_GET['menuid'];
			else
				$menuid = 0;
				
			if( isset( $_GET['submenuid'] ) )
				$submenuid = $_GET['submenuid'];
			else
				$submenuid = 0;
				
			if( isset( $_GET['subsubmenuid'] ) )
				$subsubmenuid = $_GET['subsubmenuid'];
			else
				$subsubmenuid = 0;
			
			$ec_breadcrumbs = $db->get_breadcrumb_data( $model_number, $menuid, $submenuid, $subsubmenuid );
			
			if( isset( $ec_breadcrumbs->menulevel1_name ) )
				echo "<span class=\"raquo\">$divider</span>" . '<a href="' . $store_page . $permalink_divider . 'menuid=' . $ec_breadcrumbs->menulevel1_id . '&amp;menu=' . htmlentities($ec_breadcrumbs->menulevel1_name ) . '">' . $ec_breadcrumbs->menulevel1_name . '</a>';
				
			
			if( isset( $ec_breadcrumbs->menulevel2_name ) )
				echo "<span class=\"raquo\">$divider</span>" . '<a href="' . $store_page . $permalink_divider . 'submenuid=' . $ec_breadcrumbs->menulevel2_id . '&amp;submenu=' . htmlentities($ec_breadcrumbs->menulevel2_name ) . '">' . $ec_breadcrumbs->menulevel2_name . '</a>';
			
			
			if( isset( $ec_breadcrumbs->menulevel3_name ) )
				echo "<span class=\"raquo\">$divider</span>" . '<a href="' . $store_page . $permalink_divider . 'subsubmenuid=' . $ec_breadcrumbs->menulevel3_id . '&amp;subsubmenu=' . htmlentities($ec_breadcrumbs->menulevel3_name ) . '">' . $ec_breadcrumbs->menulevel3_name . '</a>';
				
			if( isset( $_GET['ec_search'] ) )
				echo "<span class=\"raquo\">$divider</span>" . '<a href="' . $store_page . $permalink_divider . 'ec_search=' . $_GET['ec_search'] . '">' . $_GET['ec_search'] . '</a>';
				
			
			if( isset( $ec_breadcrumbs->title ) )
				echo "<span class=\"raquo\">$divider</span>" . '<a href="' . $store_page . $permalink_divider . 'model_number=' . $ec_breadcrumbs->model_number . '">' . $ec_breadcrumbs->title . '</a>';
		}else if( $ec_filter->menulevel1->menu_name ){
			
			if( isset( $ec_filter->menulevel1->menu_name ) )
				echo "<span class=\"raquo\">$divider</span>" . '<a href="' . $store_page . $permalink_divider . 'menuid=' . $ec_filter->menulevel1->menu_id . '&amp;menu=' . htmlentities($ec_filter->menulevel1->menu_name ) . '">' . $ec_filter->menulevel1->menu_name . '</a>';
				
			
			if( isset( $ec_filter->menulevel2->menu_name ) )
				echo "<span class=\"raquo\">$divider</span>" . '<a href="' . $store_page . $permalink_divider . 'submenuid=' . $ec_filter->menulevel2->menu_id . '&amp;submenu=' . htmlentities($ec_filter->menulevel2->menu_name ) . '">' . $ec_filter->menulevel2->menu_name . '</a>';
			
			
			if( isset( $ec_filter->menulevel3->menu_name ) )
				echo "<span class=\"raquo\">$divider</span>" . '<a href="' . $store_page . $permalink_divider . 'subsubmenuid=' . $ec_filter->menulevel3->menu_id . '&amp;subsubmenu=' . htmlentities($ec_filter->menulevel3->menu_name ) . '">' . $ec_filter->menulevel3->menu_name . '</a>';
					
		}
		
		if( isset( $_GET['model_number'] ) && $ec_store_page_id == $post_id ){
		?>
    
    <div class="ec_product_details_product_pagenation">
    	<?php 
			$storepage = new ec_storepage( );
			$storepage->display_product_previous_category_link( "<" ); 
			echo "&nbsp;";
			$storepage->display_product_number_in_category_list( );
			echo "&nbsp;";
			echo $GLOBALS['language']->get_text( 'product_details', 'product_details_x_of_y' );
			echo "&nbsp;";
			$storepage->display_product_count_in_category_list( );
			echo "&nbsp;";
			$storepage->display_product_next_category_link( ">" ); 
		?>
    </div>
    
    <?php }?>
</div> <!-- #breadcrumbs -->
<div style="clear:both"></div>