<?php
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'wp_easycart_admin_table' ) ) :

final class wp_easycart_admin_table{
	
	private $wpdb;
	private $table;
	private $table_id;
	private $key;
	private $custom_header;
	private $icon;
	private $add_new = true;
	private $add_new_action = 'add-new';
	private $add_new_label = 'Add New';
	private $add_new_reset;
	private $add_new_reset_var;
	private $add_new_reset_val;
	private $add_new_js = '';
	private $add_new_css = 'ec_page_title_button ec_admin_process_click';
	private $cancel = false;
	private $cancel_link = '';
	private $cancel_label = 'Cancel';
	private $docs_guide;
	private $docs_link;
	private $current_sort_column;
	private $default_sort_column;
	private $current_sort_direction;
	private $default_sort_direction;
	private $list_columns;
	private $search_columns;
	private $current_page;
	private $perpage;
	private $perpage_options;
	private $bulk_actions;
	private $bulk_variables;
	private $get_vars;
	private $actions;
	private $filters;
	private $search_term;
	private $search_disabled = false;
	private $item_label;
	private $item_label_plural;
	private $record_count;
	private $showing;
	private $total_pages;
	private $custom_join;
	private $join;
	private $importer = false;
	private $importer_button;
	private $sortable = false;
	
	private $page_url;
	private $query_params;
	private $results;
	
	private $date_diff;
	
	public function __construct(){ 
		global $wpdb;
		$this->wpdb = $wpdb;
		
        $this->add_new_label = __( 'Add New', 'wp-easycart' );
        $this->cancel_label = __( 'Cancel', 'wp-easycart' );
        
		$now_server = $this->wpdb->get_var( "SELECT NOW( ) AS the_time" );
		$now_timestamp = strtotime( $now_server );
		$now_gmt_timestampt = time( );
		$storage_offset = $now_timestamp - $now_gmt_timestampt;
		$local_offset = get_option('gmt_offset') * 60 * 60;
		$this->date_diff = $local_offset - $storage_offset;
		
		if( isset( $_GET['orderby'] ) && $_GET['orderby'] != '' )
			$this->current_sort_column = htmlspecialchars( $_GET['orderby'], ENT_QUOTES );
		if( isset( $_GET['order'] ) && $_GET['order'] != '' )
			$this->current_sort_direction = htmlspecialchars( $_GET['order'], ENT_QUOTES );
		if( isset( $_GET['pagenum'] ) && $_GET['pagenum'] != '' )
			$this->current_page = htmlspecialchars( $_GET['pagenum'], ENT_QUOTES );
		else
			$this->current_page = 1;
		if( isset( $_GET['perpage'] ) ){
			$this->perpage = htmlspecialchars( $_GET['perpage'], ENT_QUOTES );
			setcookie( "wpeasycart_admin_perpage", "", time( ) - 3600 );
			setcookie( "wpeasycart_admin_perpage", "", time( ) - 3600, "/" ); 
			setcookie( 'wpeasycart_admin_perpage', $this->perpage, time( ) + ( 3600 * 24 * 1 ), "/" );
		
		}else if( isset( $_COOKIE['wpeasycart_admin_perpage'] ) ){
			$this->perpage = $_COOKIE['wpeasycart_admin_perpage'];
		
		}else{
			$this->perpage = 25;
		}
		$this->bulk_actions = array(
			array(
				'name'	=> 'delete',
				'label'	=> __( 'Delete', 'wp-easycart' )
			),
			array(
				'name'	=> 'export',
				'label'	=> __( 'Export', 'wp-easycart' )
			)
		);
		$this->filters = array( );
		$this->record_count = 0;
		$this->showing = 0;
		$this->join = '';
		$this->get_vars = array( );
		$uri_parts = explode( '?', $_SERVER['REQUEST_URI'], 2 );
		$this->page_url = $uri_parts[0];
		$params = explode( '&', $uri_parts[1] );
		foreach( $params as $param ){
			$this->query_params[] = explode( '=', $param );
		}
		$this->perpage_options = array( 10, 25, 50, 100, 250, 500 );
		$this->custom_where = '';
	}
	public function __clone(){ _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-easycart' ), '1.0' ); }
	public function __wakeup(){ _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-easycart' ), '1.0' ); }
	public function set_table( $table, $key ){ $this->table = $table; $this->key = $key; }
	public function set_table_id( $table_id ){ $this->table_id = $table_id; }
	public function set_default_sort( $default_sort_column, $default_sort_direction ){ $this->default_sort_column = $default_sort_column; $this->default_sort_direction = $default_sort_direction; }
	public function set_header( $header ){ $this->custom_header = $header; }
	public function set_icon( $icon ){ $this->icon = $icon; }
	public function set_add_new( $add_new, $add_new_action = '', $add_new_label = '', $add_new_reset = false, $add_new_reset_var = '', $add_new_reset_val = '' ){ $this->add_new = $add_new; $this->add_new_action = $add_new_action; $this->add_new_label = $add_new_label; $this->add_new_reset = $add_new_reset; $this->add_new_reset_var = $add_new_reset_var; $this->add_new_reset_val = $add_new_reset_val; }
	public function set_add_new_js( $add_new_js ){ $this->add_new_js = $add_new_js; }
	public function set_add_new_css( $add_new_css ){ $this->add_new_css = $add_new_css; }
	public function set_cancel( $cancel, $cancel_link, $cancel_label ){ $this->cancel = $cancel; $this->cancel_link = $cancel_link; $this->cancel_label = $cancel_label; }
	public function set_list_columns( $list_columns ){ $this->list_columns = $list_columns; }
	public function set_search_columns( $search_columns ){ $this->search_columns = $search_columns; }
	public function set_search_disabled( $search_disabled) { $this->search_disabled = $search_disabled; }
	public function goto_page( $current_page ){ $this->current_page = $current_page; }
	public function set_per_page( $per_page ){ $this->perpage = $per_page; }
	public function set_bulk_actions( $bulk_actions ){ $this->bulk_actions = $bulk_actions; }
	public function set_bulk_action_hidden_variables( $bulk_variables) {$this->bulk_variables = $bulk_variables;}
	public function set_actions( $actions ){ $this->actions = $actions; }
	public function set_filters( $filters ){ $this->filters = $filters; }
	public function set_label( $single, $plural ){ $this->item_label = $single; $this->item_label_plural = $plural; }
	public function set_join( $join ){ $this->join = $join; }
	public function set_custom_where( $custom_where ){ $this->custom_where = $custom_where; }
	public function set_docs_link( $guide, $docs_link ) { $this->docs_guide = $guide; $this->docs_link = $docs_link; }
	public function set_importer( $importer, $importer_button) { $this->importer = $importer; $this->importer_button = $importer_button; }
	public function set_get_vars( $get_vars ){ $this->get_vars = $get_vars; }
	public function set_sortable( $sortable ){ $this->sortable = $sortable; }
	public function print_table( ){
		$this->get_data( ); // Stores to class results
		
		echo '<div class="easycart-wrap">';
		echo '<h1 class="easycart-wp-heading-inline"> ';
		if( isset( $this->icon ) )
			echo '<div class="dashicons-before dashicons-' . $this->icon . '"></div>';
		if( isset( $this->custom_header ) )
			echo $this->custom_header;
		else
			echo $this->item_label_plural;
		
		//echo docs	
		echo '<a href=' . wp_easycart_admin( )->helpsystem->print_docs_url($this->docs_guide, htmlspecialchars( $this->docs_link, ENT_QUOTES ), "master-record" ) . ' target="_blank" class="ec_help_icon_link"><div class="dashicons-before ec_help_icon dashicons-info"></div> ' . __( 'Help', 'wp-easycart' ) . '</a>';
		//echo vids
		echo wp_easycart_admin( )->helpsystem->print_vids_url($this->docs_guide, htmlspecialchars( $this->docs_link, ENT_QUOTES ), "master-record" );
		
		if($this->add_new)echo '<a href="' . $this->get_url( 'ec_admin_form_action', $this->add_new_action, $this->add_new_reset, $this->add_new_reset_var, $this->add_new_reset_val ) . '" class="' . $this->add_new_css . '"' . ( ( $this->add_new_js != '' ) ? ' onclick="' . $this->add_new_js . '"' : '' ) . '>'.$this->add_new_label.'</a>';
		if($this->cancel) echo '<a href="'.$this->cancel_link.'" class="ec_page_title_button">'.$this->cancel_label.'</a>';
		if($this->importer) {
			echo '<a onclick="ec_admin_importer_open_close(\'' . htmlspecialchars( ( ( isset( $_GET['subpage'] ) ) ? $_GET['subpage'] : 'products' ), ENT_QUOTES ) . '_importer\');" class="ec_page_title_button">'.$this->importer_button.'</a>';
			//import div
			echo '<div id="' . htmlspecialchars( ( ( isset( $_GET['subpage'] ) ) ? $_GET['subpage'] : 'products' ), ENT_QUOTES ) . '_importer" class="ec_importer_form">';
			//textbox for file url
			echo '<a href="' . wp_easycart_admin( )->helpsystem->print_docs_url($this->docs_guide, htmlspecialchars( $this->docs_link, ENT_QUOTES ), "importer" ) . '" target="_blank" class="ec_admin_importer_help_link">' . __( 'Need Help?', 'wp-easycart' ) . '</a> <input type="text" name="' . htmlspecialchars( ( ( isset( $_GET['subpage'] ) ) ? $_GET['subpage'] : 'products' ), ENT_QUOTES ) . '_import_file" id="' . htmlspecialchars( ( ( isset( $_GET['subpage'] ) ) ? $_GET['subpage'] : 'products' ), ENT_QUOTES ) . '_import_file"  class="wpec-admin-upload-input" />';
			//browse button to open wp media manager
			echo '<input type="button" class="ec_page_title_button" value="' . __( 'Browse', 'wp-easycart' ) . '" id="' . htmlspecialchars( ( ( isset( $_GET['subpage'] ) ) ? $_GET['subpage'] : 'products' ), ENT_QUOTES ) . '_browse_button" onclick="ec_admin_import_file_upload( \'' . htmlspecialchars( ( ( isset( $_GET['subpage'] ) ) ? $_GET['subpage'] : 'products' ), ENT_QUOTES ) . '_import_file\', \'' . htmlspecialchars( ( ( isset( $_GET['subpage'] ) ) ? $_GET['subpage'] : 'products' ), ENT_QUOTES ) . '_import_button\', \'' . htmlspecialchars( ( ( isset( $_GET['subpage'] ) ) ? $_GET['subpage'] : 'products' ), ENT_QUOTES ) . '_importer_status\');" />';
			//import button
			echo '<input type="button" class="ec_page_title_button ec_import_button" value="' . __( 'Import File', 'wp-easycart' ) . '" id="' . htmlspecialchars( ( ( isset( $_GET['subpage'] ) ) ? $_GET['subpage'] : 'products' ), ENT_QUOTES ) . '_import_button" onclick="ec_admin_start_importer( \'' . htmlspecialchars( ( ( isset( $_GET['subpage'] ) ) ? $_GET['subpage'] : 'products' ), ENT_QUOTES ) . '_import_file\', \'' . htmlspecialchars( ( ( isset( $_GET['subpage'] ) ) ? $_GET['subpage'] : 'products' ), ENT_QUOTES ) . '_importer_status\');" />';
			echo '</div>';
			//status window
			echo '<div id="' . htmlspecialchars( ( ( isset( $_GET['subpage'] ) ) ? $_GET['subpage'] : 'products' ), ENT_QUOTES ) . '_importer_status" class="ec_importer_status">';
			echo '</div>';
			
		}
		echo '</h1><hr>';

		echo '<form id="posts-filter" method="get">';
		wp_easycart_admin( )->preloader->print_preloader( "ec_admin_table_display_loader" );
		echo '<input type="hidden" name="page" value="' . htmlspecialchars( $_GET['page'], ENT_QUOTES ) . '" />';
		if( isset( $_GET['subpage'] ) )
		echo '<input type="hidden" name="subpage" value="' . htmlspecialchars( $_GET['subpage'], ENT_QUOTES ) . '" />';
		if( count( $this->get_vars ) ){
			for( $i=0; $i<count( $this->get_vars ); $i++ ){
				if( isset( $_GET[$this->get_vars[$i]] ) )
					echo '<input type="hidden" name="' . $this->get_vars[$i] . '" id="' . $this->get_vars[$i] . '" value="' . htmlspecialchars( $_GET[$this->get_vars[$i]], ENT_QUOTES ) . '" />';
			}
		}
		echo '<input type="hidden" name="orderby" value="' . $this->current_sort_column . '" />';
		echo '<input type="hidden" name="order" value="' . $this->current_sort_direction . '" />';
		$this->print_sort( );
		$this->print_table_header( );
		$this->print_table_data( );
		$this->print_table_footer( );
		$this->print_paging_only( );
		echo '</form>';
		echo '</div>';
	}
	
	/* Private Table Function */
	private function print_sort( ){
		echo '<div class="alignleft actions filteractions">';
		$this->print_filter( );
		$this->print_filter_apply( );
		echo '</div>';
		if(!$this->search_disabled) {
			echo '<p class="search-box">';
			$this->print_search_box( );
			$this->print_search_submit( );
			echo '</p>';
		}
		echo '<div class="tablenav top">';
		$this->print_left_sort( );
		$this->print_right_sort( );
		echo '<div style="clear:both;"></div></div>';
	}
	private function print_left_sort( ){
		echo '<div class="alignleft actions bulkactions">';
		$this->print_bulk_actions( );
		$this->print_bulk_actions_apply( );
		$this->print_bulk_action_variables( );
		echo '</div>';
	}
	private function print_right_sort( ){
		echo '<div class="tablenav-pages">';
		$this->print_items_info( );
		$this->print_paging( );
		echo '</div>';
	}
	private function print_bulk_actions( ){
		$alt = '';
		echo '<select id="ec_form_action" name="ec_admin_form_action">';
		echo '<option value="">' . __( 'Bulk Actions', 'wp-easycart' ) . '</option>';
		foreach( $this->bulk_actions as $bulk_action ){
			echo '<option value="' . $bulk_action['name'] . '">' . $bulk_action['label'] . '</option>';
			if( isset( $bulk_action['alt'] ) ){
				$alt .= '<select id="' . $bulk_action['alt']['id'] . '" name="' . $bulk_action['alt']['id'] . '" style="display:none;">';
				foreach( $bulk_action['alt']['options'] as $option ){
					$alt .= '<option value="' . $option->value . '">' . $option->label . '</option>';
				}
				$alt .= '</select>';
			}
		}
		echo '</select>';
		echo $alt;
	}
	private function print_bulk_action_variables( ){
		if(isset($this->bulk_variables)) {
			foreach( $this->bulk_variables as $bulk_variables ){
				echo '<input type="hidden" name="' . $bulk_variables['name'] . '" value="' . $bulk_variables['label'] . '" />';
			}
		}
	}
	private function print_bulk_actions_apply( ){
		echo '<input type="submit" id="doaction" value="' . __( 'Apply', 'wp-easycart' ) . '" class="ec_admin_list_submit" ';
		echo ' onclick="ec_bulk_disable();  this.form.submit();"';
		echo '/>';
	}
	private function print_filter( ){
		if( count( $this->filters ) ){
			for( $i=0; $i<count( $this->filters ); $i++ ){
				if( count( $this->filters[$i]['data'] ) >= 500 ){
					echo '<input type="text" name="filter_' . $i . '" style="max-width:200px;" value="' . ( ( isset( $_GET['filter_'.$i] ) ) ? $_GET['filter_'.$i] : '' ) . '" placeholder="' . $this->filters[$i]['label'] . '" />';
					
				}else{
					echo '<select name="filter_' . $i . '" style="max-width:200px;">';
					echo '<option value="">' . $this->filters[$i]['label'] . '</option>';
					foreach( $this->filters[$i]['data'] as $filter_option ){
						echo '<option value="' . $filter_option->value . '"';
						if( isset( $_GET['filter_'.$i] ) && $_GET['filter_'.$i]  ==  $filter_option->value )
							echo ' selected="selected"';
						echo '>' . $filter_option->label . '</option>';
					}
					echo '</select>';
				}
			}
		}
	}
	private function print_filter_apply( ){
		if( count( $this->filters ) ){
			echo '<input type="submit" id="dofilter" value="' .__( 'Filter', 'wp-easycart' ) . '" class="ec_admin_list_submit" />';
		}
	}
	private function print_paging_only( ){
		echo '<div class="tablenav top">';
		echo '<div class="alignleft actions pagingactions">';
		$this->print_perpage_actions( );
		$this->print_perpage_actions_apply( );
		echo '</div>';
		echo '<div class="tablenav-pages">';
		$this->print_items_info( );
		$this->print_paging( false );
		echo '</div>';
		echo '<div style="clear:both;"></div></div>';
	}
	private function print_perpage_actions( ){
		echo '<select name="perpage" id="perpage">';
		for( $i=0; $i<count( $this->perpage_options ); $i++ ){
			echo '<option value="' . $this->perpage_options[$i] . '"';
			if( $this->perpage_options[$i] == $this->perpage ){
				echo ' selected="selected"';
			}
			echo '>' . $this->perpage_options[$i] . ' ' . __( 'Per Page', 'wp-easycart' ) . '</option>';
		}
		echo '</select>';
	}
	private function print_perpage_actions_apply( ){
		echo '<input type="submit" id="doperpage" value="' . __( 'Apply', 'wp-easycart' ) . '" class="ec_admin_list_submit" />';
	}
	private function print_items_info( ){
		echo '<span class="displaying-num';
		if( $this->record_count <= $this->showing )
			echo ' showing-all';
		echo '">';
		if( $this->record_count > $this->showing && $this->record_count != ((($this->current_page-1) * $this->perpage) + $this->showing) ){
			echo ((($this->current_page-1) * $this->perpage) + 1) . '-' . ((($this->current_page-1) * $this->perpage) + $this->showing) . ' of ' . $this->record_count . ' ' . $this->item_label_plural;
		
		}else if( $this->record_count > $this->showing ){
			echo ((($this->current_page-1) * $this->perpage) + $this->showing) . ' of ' . $this->record_count . ' ' . $this->item_label_plural;
		
		}else if( $this->showing > 1 ){
			echo $this->showing . ' ' . $this->item_label_plural;
		
		}else{
			echo $this->showing . ' ' . $this->item_label;
		
		}
		echo '</span>';
	}
	private function print_paging( $show_pagenum_box = true ){
		if( $this->record_count > $this->showing ){
			echo '<span class="pagination-links">';
			
			if( $this->current_page == 1 ){
				echo '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>';
				echo '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>';
			}else{
				echo '<a class="first-page button" href="' . $this->get_url( 'pagenum', '1', false ) . '">';
				echo '<span class="screen-reader-text">First page</span>';
				echo '<span aria-hidden="true">«</span></a>';
				echo '<a class="prev-page button" href="' . $this->get_url( 'pagenum', $this->current_page-1, false ) . '">';
				echo '<span class="screen-reader-text">Previous page</span>';
				echo '<span aria-hidden="true">‹</span></a>';
			}
			
			
			echo '<span class="paging-input">';
			echo '<label for="current-page-selector" class="screen-reader-text">Current ' . $this->item_label . '</label>';
			if( $show_pagenum_box ){
				echo '<input class="current-page" type="text" name="pagenum" id="pagenum" value="' . $this->current_page . '" size="1">';
			}else{
				echo $this->current_page;
			}
			echo '<span class="tablenav-paging-text"> of <span class="total-pages">' . $this->total_pages . '</span></span>';
			echo '</span>';
			
			if( $this->current_page == $this->total_pages ){
				echo '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>';
				echo '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>';
			}else{
				echo '<a class="next-page button" href="' . $this->get_url( 'pagenum', $this->current_page+1, false ) . '">';
				echo '<span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>';
				echo '<a class="last-page button" href="' . $this->get_url( 'pagenum', $this->total_pages, false ) . '">';
				echo '<span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a>';
				
			}
			echo '</span>';
		}
	}
	private function print_search_box( ){
		echo '<input type="search" id="search-input" name="s" value="';
		if( isset( $_GET['s'] ) && $_GET['s'] != '' )
			echo htmlspecialchars( stripslashes( $_GET['s'] ) );
		echo '" />';
	}
	private function print_search_submit( ){
		echo '<input type="submit" id="search-submit" class="button" value="' . __( 'Search', 'wp-easycart' ) . ' ' . $this->item_label_plural . '" class="ec_admin_list_submit" />';
	}
	private function print_table_header( ){
		echo '<table class="wp-list-table widefat fixed striped pages" id="' . $this->table_id . '">';
		echo '<thead>';
		echo '<td id="cb" class="manage-column column-cb check-column">';
		echo '<label class="screen-reader-text" for="cb-select-all-1">' . __( 'Select All', 'wp-easycart' ) . '</label><input id="cb-select-all-1" type="checkbox">';
		echo '</td>';
		foreach( $this->list_columns as $header ){
			if( $header['format'] != 'hidden' ){
				$sort = 'asc';
				if( $this->current_sort_column == $header['name'] && $this->current_sort_direction == 'asc' )
					$sort = 'desc';
				$is_sort_selected = 'sortable';
				if( $this->current_sort_column == $header['name'] )
					$is_sort_selected = 'sorted';
				echo '<th scope="col" id="' . $header['name'] . '" class="manage-column column-primary ' . $is_sort_selected . ' ' . $sort . '"';
				if( isset( $header['width'] ) )
					echo ' width="' . $header['width'] . '"';
				echo '>';
				echo '<a href="' . $this->get_url( 'orderby', $header['name'], false, 'order', $sort ) . '"><span>' . $header['label'] . '</span><span class="sorting-indicator"></span></a>';
				echo '</th>';
			}
		}
		$actions_width = array( 45, 90, 120, 140, 175, 225 );
		echo '<th width="' . ( $actions_width[count( $this->actions )] ) . '">';
		if( $this->sortable )
			echo '<input type="button" value="' . __( 'Save Sort', 'wp-easycart' ) . '" style="float:right;" onclick="save_sort_order( \'' . $this->table_id . '\' );" class="button ec_page_title_button" />';
		echo '</th>';
		echo '</thead>';
	}
	private function print_table_data( ){
		echo '<tbody>';
		foreach( $this->results as $result ){
			echo '<tr data-id="' . $result->{$this->key} . '">';
			echo '<th scope="row" class="check-column">';
			echo '<label class="screen-reader-text" for="cb-select-' . $result->{$this->key} . '">' . __( 'Select', 'wp-easycart' ) . ' ' . $result->{$this->key} . '</label>';
			echo '<input id="cb-select-' . $result->{$this->key} . '" type="checkbox" name="bulk[]" value="' . $result->{$this->key} . '">';
			echo '</th>';
			for( $i=0; $i<count( $this->list_columns ); $i++ ){
				
				if( $this->list_columns[$i]['format'] != 'hidden' ){
					echo '<td>';
					if( isset( $this->list_columns[$i]['linked'] ) && $this->list_columns[$i]['linked'] ){
                        echo '<a href="' . $this->get_url( $this->key, $result->{$this->key}, false, 'ec_admin_form_action', 'edit' ) . '">';
                    }
					switch( $this->list_columns[$i]['format'] ){
						case 'int':
							echo (integer) $result->{$this->list_columns[$i]['name']};
							break;
						case 'stock':
							if( $result->show_stock_quantity || $result->use_optionitem_quantity_tracking )
								echo (integer) $result->{$this->list_columns[$i]['name']};
							else
								echo '&#8734;';
							break;
						case 'string':
							echo htmlentities( stripslashes( $result->{$this->list_columns[$i]['name']} ), ENT_NOQUOTES );
							break;
						case 'yes_no':
							echo ($result->{$this->list_columns[$i]['name']}) ? __( 'Yes', 'wp-easycart' ) : __( 'No', 'wp-easycart' );
							break;
						case 'date':
							echo date('F d, Y', strtotime($result->{$this->list_columns[$i]['name']}));
							break;
						case 'datetime':
							$date = $result->{$this->list_columns[$i]['name']};
							$date_timestamp = strtotime( $date );
							$date_timestamp = $date_timestamp + $this->date_diff;
							echo date( get_option('date_format') . ' ' . get_option('time_format'), $date_timestamp );
							break;
						case 'bool':
							echo ($result->{$this->list_columns[$i]['name']} ? 'Yes' : 'No' );
							break;
						case 'currency':
							echo $GLOBALS['currency']->get_currency_display( $result->{$this->list_columns[$i]['name']} );
							break;
						case 'checkbox':
							echo '<input type="checkbox"  onclick="return false;" ' . ($result->{$this->list_columns[$i]['name']} == 1 ? 'checked' : '')  . '>';
							break;
						case 'order_viewed':
							echo '<span class="ec_admin_new_order" title="' . __( 'New Order', 'wp-easycart' ) . '">'.($result->{$this->list_columns[$i]['name']} == 0 ? '!' : '').'</span>';
							break;
						case 'image_swatch':
							if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/swatches/" . $result->{$this->list_columns[$i]['name']} ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/swatches/" . $result->{$this->list_columns[$i]['name']} ) ) {
								$img_url = plugins_url( "wp-easycart-data/products/swatches/" . $result->{$this->list_columns[$i]['name']} );
								echo '<img src="' . $img_url  . '" style="height:25px;width:25px;">';
							
							}else if( substr( $result->{$this->list_columns[$i]['name']}, 0, 7 ) == 'http://' || substr( $result->{$this->list_columns[$i]['name']}, 0, 8 ) == 'https://' ) {
								echo '<img src="' . $result->{$this->list_columns[$i]['name']}  . '" style="height:25px;width:25px;">';
							
							}else{
								echo '<div class="wp-easycart-admin-swatch">' . $result->{$this->list_columns[$i]['alt']} . '</div>';
							}
							
							break;
						case 'image_upload':
							if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/swatches/" . $result->{$this->list_columns[$i]['name']} ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/swatches/" . $result->{$this->list_columns[$i]['name']} ) ) {
								$img_url = plugins_url( "wp-easycart-data/products/swatches/" . $result->{$this->list_columns[$i]['name']} );
								echo '<img src="' . $img_url  . '" style="height:25px;width:25px;">';
							
							}else if( substr( $result->{$this->list_columns[$i]['name']}, 0, 7 ) == 'http://' || substr( $result->{$this->list_columns[$i]['name']}, 0, 8 ) == 'https://' ) {
								echo '<img src="' . $result->{$this->list_columns[$i]['name']}  . '" style="height:25px;width:25px;">';
							
							}else{
								echo $result->{$this->list_columns[$i]['name']};
							}
							
							break;
						case 'star_rating':
						
							echo $this->display_review_stars($result->{$this->list_columns[$i]['name']});
							break;
						case 'optiontype':
							
							$option_type = $result->{$this->list_columns[$i]['name']};
							$option_types = array(
											(object) array(
													'value'	=> 'basic-combo',
													'label'	=> __( 'Basic: Combo', 'wp-easycart' )
											),
											(object) array(
													'value'	=> 'basic-swatch',
													'label'	=> __( 'Basic: Swatch', 'wp-easycart' )
											),
											(object) array(
													'value'	=> 'combo',
													'label'	=> __( 'Advanced: Combo Box', 'wp-easycart' )
											),
											(object) array(
													'value'	=> 'swatch',
													'label'	=> __( 'Advanced: Image Swatches', 'wp-easycart' )
											),
											(object) array(
													'value'	=> 'text',
													'label'	=> __( 'Advanced: Text Input', 'wp-easycart' )
											),
											(object) array(
													'value'	=> 'textarea',
													'label'	=> __( 'Advanced: Text Area', 'wp-easycart' )
											),
											(object) array(
													'value'	=> 'number',
													'label'	=> __( 'Advanced: Number Field', 'wp-easycart' )
											),
											(object) array(
													'value'	=> 'file',
													'label'	=> __( 'Advanced: File Upload', 'wp-easycart' )
											),
											(object) array(
													'value'	=> 'radio',
													'label'	=> __( 'Advanced: Radio Group', 'wp-easycart' )
											),
											(object) array(
													'value'	=> 'checkbox',
													'label'	=> __( 'Advanced: Checkbox Group', 'wp-easycart' )
											),
											(object) array(
													'value'	=> 'grid',
													'label'	=> __( 'Advanced: Quantity Grid', 'wp-easycart' )
											),
											(object) array(
													'value'	=> 'date',
													'label'	=> __( 'Advanced: Date', 'wp-easycart' )
											),
											(object) array(
													'value'	=> 'dimensions1',
													'label'	=> __( 'Advanced: Dimensions (Whole Inch)', 'wp-easycart' )
											),
											(object) array(
													'value'	=> 'dimensions2',
													'label'	=> __( 'Advanced: Dimensions (Sub-Inch)', 'wp-easycart' )
											)
										);
									foreach( $option_types as $op_type ){
										if( $op_type->value == $option_type){
											$option_type = $op_type->label;
											break;
										} 
									}
									echo $option_type;
							break;
						default:
							echo $result->{$this->list_columns[$i]['name']};
							break;
					}
                    if( isset( $this->list_columns[$i]['linked'] ) && $this->list_columns[$i]['linked'] ){
                        echo '</a>';
                        if( isset( $this->list_columns[$i]['subactions'] ) ){
                            echo '<div class="ec_admin_list_subactions">';
                            $first_subaction = true;
                            foreach( $this->list_columns[$i]['subactions'] as $subaction ){
                                if( !$first_subaction ){
                                    echo '<span> | </span>';
                                }
                                echo '<a href="';
                                echo ( ( isset( $subaction['url'] ) ) ? str_replace( '{key}', $result->{$this->key}, $subaction['url'] ) : $this->get_url( $this->key, $result->{$this->key}, false, 'ec_admin_form_action', $subaction['action'] ) );
                                echo '" aria-label="' . $subaction['name'] . '" title="' . $subaction['name'] . '"';
                                if( $subaction['action_type'] == 'delete') {
                                    echo ' onclick="return confirm(\'' . __( 'Are you sure you want to delete this item?', 'wp-easycart' ) . '\');"';

                                }else if( $subaction['action_type'] == 'quick-edit' ){
                                    echo ' onclick="wp_easycart_open_quick_edit( \'' . $subaction['type'] . '\', \'' . $result->{$this->key} . '\' ); return false;"';

                                }
                                echo '>' . $subaction['name'] . '</a>';
                                $first_subaction = false;
                            }
                            echo '</div>';
                        }
                    }
					echo '</td>';
				}
			}
				echo '<td>';
				
				
				
				for( $j=0; $j<count( $this->actions ); $j++ ){

					/*if(($option_type == 'dimensions1' || $option_type == 'dimensions2' || $option_type == 'date' || $option_type == 'file' || $option_type == 'number' || $option_type == 'text' || $option_type == 'textarea') && $this->actions[$j]['name'] == 'edit-optionitem') {
						//don't display anything if we have option sets that do not allow editing of the option item
						echo '<div class="dashicons-before dashicons-" style="width:30px;height:10px;"></div>';
					} else {*/
						
						$label = $this->actions[$j]['label'];
						$icon = $this->actions[$j]['icon'];
						if( $this->actions[$j]['icon'] == 'hidden' && !$result->is_visible ){
							$label = __( 'Activate', 'wp-easycart' );
							$icon = 'visibility';
						}
						echo '<span class="' . $this->actions[$j]['name'] . '"><a href="';
						if( isset( $this->actions[$j]['custom'] ) )
							echo $this->get_url( $this->key, $result->{$this->key}, true, $this->actions[$j]['custom'], $this->actions[$j]['name'] );
						else
							echo $this->get_url( $this->key, $result->{$this->key}, false, 'ec_admin_form_action', $this->actions[$j]['name'] );
						echo '" aria-label="' . $label . '" title="' . $label . '"';
						if($label == 'Delete') {
							echo ' onclick="return confirm(\'' . __( 'Are you sure you want to delete this item?', 'wp-easycart' ) . '\');"';
						
						}else if( $label == 'Quick Edit' ){
							echo ' onclick="wp_easycart_open_quick_edit( \'' . $this->actions[$j]['type'] . '\', \'' . $result->{$this->key} . '\' ); return false;"';
						
						}else if( isset( $this->actions[$j]['customhtml'] ) ){
							echo $this->actions[$j]['customhtml'];
						
						}
						if( $label == 'Stats' ){
							echo ' data-views="' . $result->{'views'} . '"';
						}
						echo '>';
						echo '<div class="dashicons-before dashicons-' . $icon . '"></div>';
						echo '</a>';
						echo '</span>';
					//}
				}
				echo '</td>';
			echo '</tr>';
		}
		echo '</tbody>';
	}
	private function print_table_footer( ){
		echo '<tfoot>';
		echo '<td id="cb" class="manage-column column-cb check-column">';
		echo '<label class="screen-reader-text" for="cb-select-all-2">' . __( 'Select All', 'wp-easycart' ) . '</label><input id="cb-select-all-2" type="checkbox">';
		echo '</td>';
		foreach( $this->list_columns as $header ){
			if( $header['format'] != 'hidden' ){
				$sort = 'asc';
				if( $this->current_sort_column == $header['name'] && $this->current_sort_direction == 'asc' )
					$sort = 'desc';
				$is_sort_selected = 'sortable';
				if( $this->current_sort_column == $header['name'] )
					$is_sort_selected = 'sorted';
				echo '<th scope="col" id="' . $header['name'] . '" class="manage-column column-primary ' . $is_sort_selected . ' ' . $sort . '">';
				echo '<a href="' . $this->get_url( 'orderby', $header['name'], true, 'order', $sort ) . '"><span>' . $header['label'] . '</span><span class="sorting-indicator"></span></a>';
				echo '</th>';
			}
		}
		echo '<th></th>';
		echo '</tfoot>';
		echo '</table>';
	}
	
	/* Private Helpers */
	private function get_url( $param, $value, $reset_params, $alt_param = NULL, $alt_value = NULL ){
		$url = $this->page_url;
		if( !$reset_params ){
			$url .= '?';
			foreach( $this->query_params as $query_param ){
				if( $param == 'orderby' && $query_param[0] == 'pagenum' ){
					// Igrore pagenum only when resorting products.
				}else if( $alt_param == 'subpage' && $query_param[0] == 'subpage' ){
					// Ignore subpage when alt_param is subpage.
				}else if( $query_param[0] == 'success' ){
					// Ignore success param.
				}else if( isset( $query_param[0] ) && isset( $query_param[1] ) && $query_param[0] != $param && ( !$alt_param || $query_param[0] != $alt_param ) ){
					$url .= '&'.$query_param[0].'='.$query_param[1];
				}
			}
			$url .= '&'.$param.'='.str_replace( '%', '%25', $value );
			if( $alt_param && $alt_param != 'subpage' ){
				$url .= '&'.$alt_param.'='.str_replace( '%', '%25', $alt_value );
			}
				
		}else{
			$url .= '?page='.htmlspecialchars( $_GET['page'], ENT_QUOTES );
			if( $alt_param == 'subpage' )
				$url .= '&subpage='.htmlspecialchars( $alt_value, ENT_QUOTES );
			else if( isset( $_GET['subpage'] ) )
				$url .= '&subpage='.htmlspecialchars( $_GET['subpage'], ENT_QUOTES );
			if( $param )
				$url .= '&'.$param.'='.str_replace( '%', '%25', $value );
			if( $alt_param && $alt_param != 'subpage' )
				$url .= '&'.$alt_param.'='.str_replace( '%', '%25', $alt_value );
		}
		return $url;
	}
	
	/* Private Query Functions */
	private function get_data( ){
		$sql = $this->get_query( );
		$this->results = $this->wpdb->get_results( $sql );
		$this->showing = count( $this->results );
		$this->record_count = $this->wpdb->get_var( "SELECT FOUND_ROWS()" );
		$this->total_pages = ceil( $this->record_count / $this->perpage );
		if( $this->current_page > $this->total_pages && $this->record_count == 0 ){
			$this->current_page = 1;
			
		}else if( $this->current_page > $this->total_pages ){
			$this->current_page = $this->total_pages;
			$this->get_data( );
		}
	}
	private function get_query( ){
		if( isset( $this->current_sort_column ) ){
			$sort_column = $this->current_sort_column;
			$sort_direction = $this->current_sort_direction;
		
		// Custom multi-sort order by default.
		}else if( is_array( $this->default_sort_column ) ){
			$sort_column = '';
			for( $i=0; $i<count( $this->default_sort_column ); $i++ ){
				if( $i > 0 )
					$sort_column .= ', ';
				$sort_column .= $this->default_sort_column[$i] . " " . $this->default_sort_direction[$i];
			}
			$sort_direction = '';
		
		}else{
			$sort_column = $this->current_sort_column = $this->default_sort_column;
			$sort_direction = $this->current_sort_direction = $this->default_sort_direction;
		}
		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT " . $this->table . '.' . $this->key;
		foreach( $this->list_columns as $list_column ){
			$sql .= ", ";
			if( isset( $list_column['select'] ) )
				$sql .= $list_column['select'];
			else
				$sql .= $this->table . '.' . $list_column['name'];
		}
		$sql .= $this->get_filter_select( ) . " FROM " . $this->table . ' ' . $this->join . $this->get_filter( ) . " ORDER BY " . $sort_column . " " . $sort_direction . " LIMIT " . ($this->current_page-1) * $this->perpage . ", " . $this->perpage;
		
		return $sql;
	}
	private function get_filter( ){
		$join = '';
		$where = ' WHERE 1=1' . $this->custom_where;
		$having = '';
		for( $i=0; $i<count( $this->filters ); $i++ ){
			if( isset( $_GET['filter_'.$i] ) && $_GET['filter_'.$i] != '' ){
				if( isset( $this->filters[$i]['join'] ) && $this->filters[$i]['join'] != '' )
					$join .= ' ' . $this->filters[$i]['join'];
				
				if( isset( $this->filters[$i]['where'] ) && $this->filters[$i]['where'] != '' )
					$where .= ' AND ( ' . $this->wpdb->prepare( $this->filters[$i]['where'], $_GET['filter_'.$i] );
				
				if( isset( $this->filters[$i]['where2'] ) && $this->filters[$i]['where2'] != '' )
					$where .= ' OR ' . $this->wpdb->prepare( $this->filters[$i]['where2'], $_GET['filter_'.$i] );
					
				if( isset( $this->filters[$i]['where'] ) && $this->filters[$i]['where'] != '' )
					$where .= ' )';
				
				if( isset( $this->filters[$i]['having'] ) && $having == '' && $this->filters[$i]['having'] != '' )
					$having .= ' HAVING ' . $this->wpdb->prepare( $this->filters[$i]['having'], $_GET['filter_'.$i] );
				
				else if( isset( $this->filters[$i]['having'] ) && $this->filters[$i]['having'] != '' )
					$having .= ' AND ' . $this->wpdb->prepare( $this->filters[$i]['having'], $_GET['filter_'.$i] );
					
				else if( isset( $this->filters[$i]['group'] ) && $this->filters[$i]['group'] != '' )
					$having .= ' ' . $this->filters[$i]['group'];
			}
		}
		if( isset( $_GET['s'] ) && $_GET['s'] != '' ){
			/* Generate a search string */
			$search = trim( $_GET['s'] );
			$search_terms = explode( ' ', $search );
			
			/* Build the where */
			$where .= ' AND (';
			for( $i=0; $i<count( $this->search_columns ); $i++ ){
				//for( $j=0; $j<count( $search_terms ); $j++ ){
				//	if( $i > 0 || $j > 0 )
				//		$where .= ' OR ';
				//	$where .= ' ' . $this->search_columns[$i] . ' LIKE ' . $this->wpdb->prepare( '%s', '%' . $search_terms[$j] . '%' );
				//}
				if( $i > 0 )
					$where .= ' OR ';
				$where .= ' ' . $this->search_columns[$i] . ' LIKE ' . $this->wpdb->prepare( '%s', '%' . $search . '%' );
			}
			$where .= ')';
		}
		return $join . $where . $having;
	}
	private function get_filter_select( ){
		$filter_select = '';
		for( $i=0; $i<count( $this->filters ); $i++ ){
			if( isset( $_GET['filter_'.$i] ) && $_GET['filter_'.$i] != '' && isset( $this->filters[$i]['select'] ) ){
				$filter_select .= ', '.$this->filters[$i]['select'];
			}
		}
		return $filter_select;
	}
	private function get_filter_options( $filter ){
		$sql = "SELECT " . $filter['filterkey'] . " AS option_id, " . $filter['orderby'] . " AS option_label FROM " . $filter['table'] . " ORDER BY " . $filter['orderby'] . " " . $filter['order'];
		return $this->wpdb->get_results( $sql );
	}
	
	public function display_review_stars( $rating ){
		$ret_string = "";
		for($i=0; $i<$rating; $i++)						$ret_string .= $this->display_star_on();
		for($i=$rating; $i<5; $i++)						$ret_string .= $this->display_star_off();					
		return $ret_string;	
	}
	
	private function display_star_on( ){
		return "<div class=\"ec_admin_review_star_on\"></div>";
	}
	
	private function display_star_off( ){
		return "<div class=\"ec_admin_review_star_off\"></div>";
	}
}
endif; // End if class_exists check