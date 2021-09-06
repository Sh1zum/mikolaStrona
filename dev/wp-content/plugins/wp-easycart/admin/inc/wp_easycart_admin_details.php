<?php
if( !defined( 'ABSPATH' ) ) exit;

class wp_easycart_admin_details{
	
	protected $wpdb;
	protected $id;
	protected $page;
	protected $subpage;
	protected $action;
	protected $form_action;
	protected $docs_link;
	
	public function __construct( ){
		global $wpdb;
		$this->wpdb = $wpdb;
	}
	
	public function print_fields( $field_list ){
		foreach( $field_list as $field ){
			$this->print_field( $field );
		}
	}
	
	protected function print_field( $field ){
		$this->{'print_' . $field['type'] . '_field'}( $field );
	}
	protected function print_heading_field( $column ){
		if ($column['horizontal_rule'] == true) echo '<br><hr>';
		echo '<div id="ec_admin_row_heading_title" class="ec_admin_row_heading_title">';
		echo $column['label'] . '<br>';
		echo '</div>';
		echo '<div id="ec_admin_row_heading_message" class="ec_admin_row_heading_message"><p>';
		echo $column['message'] . '</p>';
		echo '</div>';
	}
	
	protected function print_hidden_field( $column ){
		//echo '<div id="ec_admin_row_' . $column['alt_name'] . '">';
		echo '<input type="hidden" name="' . $column['alt_name'] . '" id="' . $column['alt_name'] . '" value="';
		if( $this->id )
			echo $column['value'];
		echo '"/>';
		//echo '</div>';
	}
	
	protected function print_image_upload_field ($column) {
		$button_label = __( 'Upload Image', 'wp-easycart' );
		if( isset( $column['button_label'] ) )
			$button_label = $column['button_label'];
		echo '<div id="ec_admin_row_' . $column['name'] . '"';
		if( $this->id && isset( $column['requires'] ) && isset($this->item) && !isset( $column['requires']['name'] ) ){
			$hide = false;
			for( $i=0; $i<count( $column['requires'] ); $i++ ){
				if( $this->item->{$column['requires'][$i]['name']} != $column['requires'][$i]['value'] )
					$hide = true;
			}
			if( $hide ){
				echo ' class="ec_admin_hidden wpeasycart-admin-image-row"';
            }else{
                echo ' class="wpeasycart-admin-image-row"';
            }
		
		}else if($this->id && isset( $column['requires'] ) && isset($this->item) && ( ( is_array( $column['requires']['value'] ) && !in_array( $this->item->{$column['requires']['name']}, $column['requires']['value'] ) ) || ( !is_array( $column['requires']['value'] ) && $this->item->{$column['requires']['name']} != $column['requires']['value'] ) ) ){
			echo ' class="ec_admin_hidden wpeasycart-admin-image-row"';
		
		}else if( !$this->id && isset( $column['requires'] ) && is_array( $column['requires'] ) && isset( $column['requires'][0] ) && isset( $column['requires'][0]['default_show'] ) && $column['requires'][0]['default_show'] == false ){
			echo ' class="ec_admin_hidden wpeasycart-admin-image-row"';
		
		}else if( !$this->id && isset( $column['requires'] ) && isset( $column['requires']['default_show'] ) && $column['requires']['default_show'] == false ){
			echo ' class="ec_admin_hidden wpeasycart-admin-image-row"';
		
		}else{
            echo ' class="wpeasycart-admin-image-row"';
            
        }
		echo '>' . $column['label'];
		echo '<input type="text" name="' . $column['name'] . '" id="' . $column['name'] . '"  class="wpec-admin-upload-input"  value="';
		if( $this->id )
			echo htmlentities( $column['value'] );
		echo '"';
		if( isset( $column['maxlength'] ) ) echo ' maxlength="'.$column['maxlength'].'"';
		if( isset($column['read-only']) && $column['read-only']) echo ' class="wpec-admin-readonly" readonly ';
		if( isset( $column['required'] ) && $column['required'] )
			echo ' class="wpep-required" wpec-admin-validation-type="' . $column['validation_type'] . '"';
		if( isset( $column['show'] ) )
			echo ' onchange="ec_admin_show_hide_update( \'' . $column['name'] . '\', \'' . $column['show']['value'] . '\', \'ec_admin_row_' . $column['show']['name'] . '\' )"';
		if (isset($column['onchange']) && $column['onchange']) 
			echo ' onchange="' . $column['onchange'] . '( \''. $column['name'] .'\');"';
		
		echo ' />';
		echo '<input type="button" class="wpec-admin-upload-button" value="' . $button_label . '" id="ec_upload_button_' . $column['name'] . '"';
		if( isset( $column['image_action'] ) )
			echo ' onclick="' . $column['image_action'] . '( \''. $column['name'] .'\');"';
		else
			echo ' onclick="ec_admin_image_upload( \''. $column['name'] .'\');"';
		echo ' />';
		if( isset( $column['required'] ) && $column['required'] )
			echo '<span id="' . $column['name'] . '_validation" class="ec_validation_error">' . $column['message'] . '</span>';
		echo '</div>';
		echo '<div id="ec_admin_row_' . $column['name'] . '_preview"';
		if( $this->id && isset( $column['requires'] ) && isset($this->item) && !isset( $column['requires']['name'] ) ){
			$hide = false;
			for( $i=0; $i<count( $column['requires'] ); $i++ ){
				if( $this->item->{$column['requires'][$i]['name']} != $column['requires'][$i]['value'] )
					$hide = true;
			}
			if( $hide ){
				echo ' class="ec_admin_hidden wpeasycart-admin-preview-row"';
            }else{
                echo ' class="wpeasycart-admin-preview-row"';
            }
            
		}else if($this->id && isset( $column['requires'] ) && isset($this->item) && ( ( is_array( $column['requires']['value'] ) && !in_array( $this->item->{$column['requires']['name']}, $column['requires']['value'] ) ) || ( !is_array( $column['requires']['value'] ) && $this->item->{$column['requires']['name']} != $column['requires']['value'] ) ) ){
			echo ' class="ec_admin_hidden wpeasycart-admin-preview-row"';
		
		}else if( !$this->id && isset( $column['requires'] ) && is_array( $column['requires'] ) && isset( $column['requires'][0] ) && isset( $column['requires'][0]['default_show'] ) && $column['requires'][0]['default_show'] == false ){
			echo ' class="ec_admin_hidden wpeasycart-admin-preview-row"';
		
		}else if( !$this->id && isset( $column['requires'] ) && isset( $column['requires']['default_show'] ) && $column['requires']['default_show'] == false ){
			echo ' class="ec_admin_hidden wpeasycart-admin-preview-row"';
		
		}else{
            echo ' class="wpeasycart-admin-preview-row"';
            
        }
		echo '>';
		if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" . $column['value'] ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" . $column['value'] ) ) {
			$img_url = plugins_url( "wp-easycart-data/products/pics1/" . $column['value'] );
		
		} else {
			$img_url = $column['value'];
		}
						
		echo '<img src="' . $img_url . '" id="' . $column['name'] . '_preview" class="wpec-admin-upload-preview">';
		if( !isset( $column['show_delete'] ) || $column['show_delete'] ){
			echo '<button class="ec_page_title_button ec_admin_delete_image';
			if( $column['value'] == '' )
				echo ' ec_admin_hidden';
			echo '" onclick="ec_admin_delete_image( \'' . $column['name'] . '\' ); return false;">';
			if( isset( $column['delete_label'] ) )
				echo $column['delete_label'];
			else
				echo __( 'Delete Image', 'wp-easycart' );
			echo '</button>';
		}
		echo '</div>';
		
	}

	protected function print_password_field( $column ){
		echo '<div id="ec_admin_row_' . $column['name'] . '"';
		if( $this->id && isset( $column['requires'] ) && isset($this->item) && !isset( $column['requires']['name'] ) ){
			$hide = false;
			for( $i=0; $i<count( $column['requires'] ); $i++ ){
				if( $this->item->{$column['requires'][$i]['name']} != $column['requires'][$i]['value'] )
					$hide = true;
			}
			if( $hide )
				echo ' class="ec_admin_hidden"';
		}else if($this->id && isset( $column['requires'] ) && isset($this->item) && ( ( is_array( $column['requires']['value'] ) && !in_array( $this->item->{$column['requires']['name']}, $column['requires']['value'] ) ) || ( !is_array( $column['requires']['value'] ) && $this->item->{$column['requires']['name']} != $column['requires']['value'] ) ) ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && is_array( $column['requires'] ) && isset( $column['requires'][0] ) && isset( $column['requires'][0]['default_show'] ) && $column['requires'][0]['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && isset( $column['requires']['default_show'] ) && $column['requires']['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}
		echo '>' . $column['label'];
		echo '<input type="password" name="' . $column['name'] . '" id="' . $column['name'] . '" value="';
		if( $this->id )
			echo htmlentities( $column['value'] );
		echo '"';
		if( isset($column['read-only']) && $column['read-only']) echo ' class="wpec-admin-readonly" readonly ';
		if( $column['required'] )
			echo ' class="wpep-required" wpec-admin-validation-type="' . $column['validation_type'] . '"';
		if( isset( $column['show'] ) )
			echo ' onchange="ec_admin_show_hide_update( \'' . $column['name'] . '\', \'' . $column['show']['value'] . '\', \'ec_admin_row_' . $column['show']['name'] . '\' )"';
		if (isset($column['onchange']) && $column['onchange']) 
			echo ' onchange="' . $column['onchange'] . '( \''. $column['name'] .'\');"';
		echo ' />';
		if( $column['required'] )
			echo '<span id="' . $column['name'] . '_validation" class="ec_validation_error">' . $column['message'] . '</span>';
		echo '</div>';
	}
	protected function print_text_field( $column ){
		echo '<div id="ec_admin_row_' . $column['name'] . '"';
		if( $this->id && isset( $column['requires'] ) && isset($this->item) && !isset( $column['requires']['name'] ) ){
			$hide = false;
			for( $i=0; $i<count( $column['requires'] ); $i++ ){
				if( $this->item->{$column['requires'][$i]['name']} != $column['requires'][$i]['value'] )
					$hide = true;
			}
			if( $hide )
				echo ' class="ec_admin_hidden"';
		}else if($this->id && isset( $column['requires'] ) && isset($this->item) && ( ( is_array( $column['requires']['value'] ) && !in_array( $this->item->{$column['requires']['name']}, $column['requires']['value'] ) ) || ( !is_array( $column['requires']['value'] ) && $this->item->{$column['requires']['name']} != $column['requires']['value'] ) ) ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && is_array( $column['requires'] ) && isset( $column['requires'][0] ) && isset( $column['requires'][0]['default_show'] ) && $column['requires'][0]['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && isset( $column['requires']['default_show'] ) && $column['requires']['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['visible'] ) && $column['visible'] == false ){
			echo ' style="display:none;"';
		}
		echo '>';
		echo '<div class="wp_easycart_admin_no_padding">';
        echo '<div class="wp-easycart-admin-toggle-group-text">';
        echo '<label>' . $column['label'] . '</label>';
        echo '<fieldset class="wp-easycart-admin-field-container">';
		echo '<input type="text" name="' . $column['name'] . '" id="' . $column['name'] . '" value="';
		if( $this->id )
			echo htmlentities( stripslashes( $column['value'] ) );
		else if( isset( $column['default'] ) )
			echo htmlentities( stripslashes( $column['default'] ) );
		echo '"';
		if(isset($column['maxlength'])) echo ' maxlength="'.$column['maxlength'].'"';
		if( isset($column['read-only']) && $column['read-only']) echo ' readonly ';
		if( isset( $column['placeholder'] ) ){ echo ' placeholder="'.$column['placeholder'].'"'; }
		if( $column['required'] )
			echo ' class="wpep-required wp-easycart-admin-field ' . ( ( isset($column['read-only']) && $column['read-only'] ) ? 'wpec-admin-readonly' : '' ) . '" wpec-admin-validation-type="' . $column['validation_type'] . '"';
		else if( isset( $column['validation_type'] ) && isset( $column['message'] ) )
			echo ' class="wpep-validate-only wp-easycart-admin-field ' . ( ( isset($column['read-only']) && $column['read-only'] ) ? 'wpec-admin-readonly' : '' ) . '" wpec-admin-validation-type="' . $column['validation_type'] . '"';
		else
            echo ' class="wp-easycart-admin-field ' . ( ( isset($column['read-only']) && $column['read-only'] ) ? 'wpec-admin-readonly' : '' ) . '"';
		if( isset( $column['show'] ) )
			echo ' onchange="ec_admin_show_hide_update( \'' . $column['name'] . '\', \'' . $column['show']['value'] . '\', \'ec_admin_row_' . $column['show']['name'] . '\' )"';
		if (isset($column['onchange']) && $column['onchange']) 
			echo ' onchange="' . $column['onchange'] . '( \''. $column['name'] .'\');"';
		if (isset($column['onkeyup']) && $column['onkeyup']) 
			echo ' onkeyup="' . $column['onkeyup'] . '( \''. $column['name'] .'\');"';
		if (isset($column['onclick']) && $column['onclick']) 
			echo 'onclick="return ' . $column['onclick'] . '( \''. $column['name'] .'\');"';
		echo ' />';
		if( $column['required'] )
			echo '<span id="' . $column['name'] . '_validation" class="ec_validation_error">' . $column['message'] . '</span>';
		else if( isset( $column['validation_type'] ) && isset( $column['message'] ) )
			echo '<span id="' . $column['name'] . '_validation" class="ec_validation_error">' . $column['message'] . '</span>';
		echo '</fieldset></div></div></div>';
	}
	protected function print_color_field( $column ){
		echo '<div id="ec_admin_row_' . $column['name'] . '"';
		if( $this->id && isset( $column['requires'] ) && isset($this->item) && !isset( $column['requires']['name'] ) ){
			$hide = false;
			for( $i=0; $i<count( $column['requires'] ); $i++ ){
				if( $this->item->{$column['requires'][$i]['name']} != $column['requires'][$i]['value'] )
					$hide = true;
			}
			if( $hide )
				echo ' class="ec_admin_hidden"';
		}else if($this->id && isset( $column['requires'] ) && isset($this->item) && ( ( is_array( $column['requires']['value'] ) && !in_array( $this->item->{$column['requires']['name']}, $column['requires']['value'] ) ) || ( !is_array( $column['requires']['value'] ) && $this->item->{$column['requires']['name']} != $column['requires']['value'] ) ) ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && is_array( $column['requires'] ) && isset( $column['requires'][0] ) && isset( $column['requires'][0]['default_show'] ) && $column['requires'][0]['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && isset( $column['requires']['default_show'] ) && $column['requires']['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}
		echo '>' . $column['label'];
		echo '<div class="ec_admin_color_holder"><input type="color" name="' . $column['name'] . '" id="' . $column['name'] . '" value="';
		if( $this->id )
			echo $column['value'];
		else if( isset( $column['default'] ) )
			echo $column['default'];
		echo '"';
		if(isset($column['maxlength'])) echo ' maxlength="'.$column['maxlength'].'"';
		if( isset($column['read-only']) && $column['read-only']) echo ' class="wpec-admin-readonly" readonly ';
		if( $column['required'] )
			echo ' class="ec_color_block_input wpep-required" wpec-admin-validation-type="' . $column['validation_type'] . '"';
		else if( isset( $column['validation_type'] ) && isset( $column['message'] ) )
			echo ' class="ec_color_block_input wpep-validate-only" wpec-admin-validation-type="' . $column['validation_type'] . '"';
		else
			echo ' class="ec_color_block_input"';
		if( isset( $column['show'] ) )
			echo ' onchange="ec_admin_show_hide_update( \'' . $column['name'] . '\', \'' . $column['show']['value'] . '\', \'ec_admin_row_' . $column['show']['name'] . '\' )"';
		if (isset($column['onchange']) && $column['onchange']) 
			echo ' onchange="' . $column['onchange'] . '( \''. $column['name'] .'\');"';
		if (isset($column['onkeyup']) && $column['onkeyup']) 
			echo ' onkeyup="' . $column['onkeyup'] . '( \''. $column['name'] .'\');"';
		if (isset($column['onclick']) && $column['onclick']) 
			echo 'onclick="return ' . $column['onclick'] . '( \''. $column['name'] .'\');"';
		echo ' /></div>';
		if( $column['required'] )
			echo '<span id="' . $column['name'] . '_validation" class="ec_validation_error">' . $column['message'] . '</span>';
		else if( isset( $column['validation_type'] ) && isset( $column['message'] ) )
			echo '<span id="' . $column['name'] . '_validation" class="ec_validation_error">' . $column['message'] . '</span>';
		echo '</div>';
	}
	protected function print_select_field( $column ){
		echo '<div id="ec_admin_row_' . $column['name'] . '"';
		if( $this->id && isset( $column['requires'] ) && isset($this->item) && !isset( $column['requires']['name'] ) ){
			$hide = false;
			for( $i=0; $i<count( $column['requires'] ); $i++ ){
				if( $this->item->{$column['requires'][$i]['name']} != $column['requires'][$i]['value'] )
					$hide = true;
			}
			if( $hide )
				echo ' class="ec_admin_hidden"';
		}else if($this->id && isset( $column['requires'] ) && isset($this->item) && ( ( is_array( $column['requires']['value'] ) && !in_array( $this->item->{$column['requires']['name']}, $column['requires']['value'] ) ) || ( !is_array( $column['requires']['value'] ) && $this->item->{$column['requires']['name']} != $column['requires']['value'] ) ) ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && is_array( $column['requires'] ) && isset( $column['requires'][0] ) && isset( $column['requires'][0]['default_show'] ) && $column['requires'][0]['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && isset( $column['requires']['default_show'] ) && $column['requires']['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}
		echo '>';
		echo '<div class="wp_easycart_admin_no_padding">';
        echo '<div class="wp-easycart-admin-toggle-group-text">';
        echo '<label>' . $column['label'] . '</label>';
        echo '<fieldset class="wp-easycart-admin-field-container">';
		echo '<select name="' . $column['name'] . '" id="' . $column['name'] . '"';
		$select_classes = array( );
		if( isset( $column['select2'] ) )
			$select_classes[] = 'select2-' . $column['select2'];
		if( isset($column['read-only']) && $column['read-only'] ){
			$select_classes[] = 'wpec-admin-readonly';
			echo 'disabled="true" ';
		}
		if( isset( $column['multiple']) && $column['multiple'] ){
			echo 'multiple="multiple" ';
		}
		if( isset( $column['show'] ) )
			echo ' onchange="ec_admin_show_hide_update( \'' . $column['name'] . '\', \'' . $column['show']['value'] . '\', \'ec_admin_row_' . $column['show']['name'] . '\' )"';
		if( $column['required'] ){
			$select_classes[] = 'wpep-required';
			echo ' wpec-admin-validation-type="' . $column['validation_type'] . '"';
		}
		if (isset($column['onchange']) && $column['onchange']) 
			echo ' onchange="' . $column['onchange'] . '( \''. $column['name'] .'\');"';
		echo ' class="';
		foreach( $select_classes as $class ){
			echo $class . ' ';
		}
		echo '"';
		echo '>';
		echo '<option value="0">' . $column['data_label'] . '</option>';
		foreach( $column['data'] as $data_item ){
		echo '<option value="' . htmlentities( $data_item->id ) . '"';
		if( $this->id ){
			if( isset( $column['dependent'] ) && $column['dependent'] == 'custom' && isset( $data_item->selected ) ){
				$selected = true;
				foreach( $data_item->selected as $name => $value ){
					if( ( $value === true && $this->item->{$name} ) || ( $value === false && !$this->item->{$name} ) || ( !is_bool( $value ) && $this->item->{$name} != $value ) )
						$selected = false;
				}
				if( $selected )
					echo ' selected="selected"';
			}else if( $data_item->id == $column['value'] ){
				echo ' selected="selected"';
			}else if( isset( $column['multiple']) && $column['multiple'] && is_array( $column['value'] ) && in_array( $data_item->id, $column['value'] ) ){
				echo ' selected="selected"';
			}
		}
		echo '>' . $data_item->value . '</option>';
		}
		echo '</select>';
		if( $column['required'] )
			echo '<span id="' . $column['name'] . '_validation" class="ec_validation_error">' . $column['message'] . '</span>';
		echo '</fieldset></div></div></div>';
	}
	protected function print_number_field( $column ){
		echo '<div id="ec_admin_row_' . $column['name'] . '"';
		if( $this->id && isset( $column['requires'] ) && isset($this->item) && !isset( $column['requires']['name'] ) ){
			$hide = false;
			for( $i=0; $i<count( $column['requires'] ); $i++ ){
				if( $this->item->{$column['requires'][$i]['name']} != $column['requires'][$i]['value'] )
					$hide = true;
			}
			if( $hide )
				echo ' class="ec_admin_hidden"';
		}else if($this->id && isset( $column['requires'] ) && isset($this->item) && ( ( is_array( $column['requires']['value'] ) && !in_array( $this->item->{$column['requires']['name']}, $column['requires']['value'] ) ) || ( !is_array( $column['requires']['value'] ) && $this->item->{$column['requires']['name']} != $column['requires']['value'] ) ) ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && is_array( $column['requires'] ) && isset( $column['requires'][0] ) && isset( $column['requires'][0]['default_show'] ) && $column['requires'][0]['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && isset( $column['requires']['default_show'] ) && $column['requires']['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}
		echo '>';
		echo '<div class="wp_easycart_admin_no_padding">';
        echo '<div class="wp-easycart-admin-toggle-group-text">';
        echo '<label>' . $column['label'] . '</label>';
        echo '<fieldset class="wp-easycart-admin-field-container">';
		if( isset( $column['step'] ) )
			echo '<input type="number" step="' . $column['step'] . '" name="' . $column['name'] . '" id="' . $column['name'] . '" value="';
		else
			echo '<input type="number" step=".01" name="' . $column['name'] . '" id="' . $column['name'] . '" value="';
		if( $this->id )
			echo $column['value'];
		else if( isset( $column['default'] ) )
			echo $column['default'];
		echo '"';
		if(isset($column['max'])) echo ' max="'.$column['max'].'"';
		if(isset($column['min'])) echo ' min="'.$column['min'].'"';
		
		if( isset($column['read-only']) && $column['read-only']) echo ' class="wpec-admin-readonly" readonly ';
		if( isset( $column['show'] ) )
			echo ' onchange="ec_admin_show_hide_update( \'' . $column['name'] . '\', \'' . $column['show']['value'] . '\', \'ec_admin_row_' . $column['show']['name'] . '\' )"';
		if (isset($column['onchange']) && $column['onchange']) 
			echo ' onchange="' . $column['onchange'] . '( \''. $column['name'] .'\');"';
		if( $column['required'] )
			echo ' class="wpep-required" wpec-admin-validation-type="' . $column['validation_type'] . '"';
		if( isset( $column['styles'] ) ){
			echo ' style="';
			foreach( $column['styles'] as $style ){
				echo $style[0] . ':' . $style[1] . ';';
			}
			echo '"';
		}
		echo ' />';
		if( $column['required'] )
			echo '<span id="' . $column['name'] . '_validation" class="ec_validation_error">' . $column['message'] . '</span>';
		echo '</fieldset></div></div></div>';
	}
	protected function print_currency_field( $column ){
		echo '<div id="ec_admin_row_' . $column['name'] . '"';
		if( $this->id && isset( $column['requires'] ) && isset($this->item) && !isset( $column['requires']['name'] ) ){
			$hide = false;
			for( $i=0; $i<count( $column['requires'] ); $i++ ){
				if( $this->item->{$column['requires'][$i]['name']} != $column['requires'][$i]['value'] )
					$hide = true;
			}
			if( $hide )
				echo ' class="ec_admin_hidden"';
		}else if($this->id && isset( $column['requires'] ) && isset($this->item) && ( ( is_array( $column['requires']['value'] ) && !in_array( $this->item->{$column['requires']['name']}, $column['requires']['value'] ) ) || ( !is_array( $column['requires']['value'] ) && $this->item->{$column['requires']['name']} != $column['requires']['value'] ) ) ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && is_array( $column['requires'] ) && isset( $column['requires'][0] ) && isset( $column['requires'][0]['default_show'] ) && $column['requires'][0]['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && isset( $column['requires']['default_show'] ) && $column['requires']['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}
		echo '>';
		echo '<div class="wp_easycart_admin_no_padding">';
        echo '<div class="wp-easycart-admin-toggle-group-text">';
        echo '<label>' . $column['label'] . '</label>';
        echo '<fieldset class="wp-easycart-admin-field-container">';
		$step = 1;
		for( $i=0; $i<$GLOBALS['currency']->get_decimal_length( ); $i++ ){
			$step = $step / 10;
		}
		echo '<input type="number" name="' . $column['name'] . '" step="' . $step . '" id="' . $column['name'] . '" value="';
		if( $this->id )
			echo number_format( $column['value'], 2, '.', '' );
		else if( isset( $column['default'] ) )
			echo number_format( $column['default'], 2, '.', '' );
		echo '"';
		if( isset($column['read-only']) && $column['read-only']) echo ' class="wpec-admin-readonly" readonly ';
		if( isset( $column['show'] ) )
			echo ' onchange="ec_admin_show_hide_update( \'' . $column['name'] . '\', \'' . $column['show']['value'] . '\', \'ec_admin_row_' . $column['show']['name'] . '\' )"';
		if (isset($column['onchange']) && $column['onchange']) 
			echo ' onchange="' . $column['onchange'] . '( \''. $column['name'] .'\');"';
		if( $column['required'] )
			echo ' class="wpep-required" wpec-admin-validation-type="' . $column['validation_type'] . '"';
		echo ' />';
		if( $column['required'] )
			echo '<span id="' . $column['name'] . '_validation" class="ec_validation_error">' . $column['message'] . '</span>';
		echo '</fieldset></div></div></div>';
	}
	protected function print_textarea_field( $column ){
		echo '<div id="ec_admin_row_' . $column['name'] . '"';
		if( $this->id && isset( $column['requires'] ) && isset($this->item) && !isset( $column['requires']['name'] ) ){
			$hide = false;
			for( $i=0; $i<count( $column['requires'] ); $i++ ){
				if( $this->item->{$column['requires'][$i]['name']} != $column['requires'][$i]['value'] )
					$hide = true;
			}
			if( $hide )
				echo ' class="ec_admin_hidden"';
		}else if($this->id && isset( $column['requires'] ) && isset($this->item) && ( ( is_array( $column['requires']['value'] ) && !in_array( $this->item->{$column['requires']['name']}, $column['requires']['value'] ) ) || ( !is_array( $column['requires']['value'] ) && $this->item->{$column['requires']['name']} != $column['requires']['value'] ) ) ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && is_array( $column['requires'] ) && isset( $column['requires'][0] ) && isset( $column['requires'][0]['default_show'] ) && $column['requires'][0]['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && isset( $column['requires']['default_show'] ) && $column['requires']['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}else if( isset( $column['visible'] ) && $column['visible'] == false ){ 
			echo ' style="display:none;"';
		}
		echo '>' . $column['label'];
		//echo '<div>';
		echo '<textarea name="' . $column['name'] . '" id="' . $column['name'] . '"';
		if( isset($column['read-only']) && $column['read-only']) echo ' class="wpec-admin-readonly" readonly ';
		if( isset( $column['show'] ) )
			echo ' onchange="ec_admin_show_hide_update( \'' . $column['name'] . '\', \'' . $column['show']['value'] . '\', \'ec_admin_row_' . $column['show']['name'] . '\' )"';
		if (isset($column['onchange']) && $column['onchange']) 
			echo ' onchange="' . $column['onchange'] . '( \''. $column['name'] .'\');"';
		if( $column['required'] )
			echo ' class="wpep-required" wpec-admin-validation-type="' . $column['validation_type'] . '"';
		if (isset($column['height']) && $column['height']) 
			echo ' style=" height: ' . $column['height'] . 'px;" ';
		echo '>';
		if( $this->id )
			echo htmlentities( $column['value'] );
		echo '</textarea>';
		if( $column['required'] )
			echo '<span id="' . $column['name'] . '_validation" class="ec_validation_error">' . $column['message'] . '</span>';
		echo '</div>';
	}
	protected function print_wp_textarea_field( $column ){
		echo '<div id="ec_admin_row_' . $column['name'] . '"';
		if( $this->id && isset( $column['requires'] ) && isset($this->item) && !isset( $column['requires']['name'] ) ){
			$hide = false;
			for( $i=0; $i<count( $column['requires'] ); $i++ ){
				if( $this->item->{$column['requires'][$i]['name']} != $column['requires'][$i]['value'] )
					$hide = true;
			}
			if( $hide )
				echo ' class="ec_admin_hidden"';
		}else if($this->id && isset( $column['requires'] ) && isset($this->item) && ( ( is_array( $column['requires']['value'] ) && !in_array( $this->item->{$column['requires']['name']}, $column['requires']['value'] ) ) || ( !is_array( $column['requires']['value'] ) && $this->item->{$column['requires']['name']} != $column['requires']['value'] ) ) ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && is_array( $column['requires'] ) && isset( $column['requires'][0] ) && isset( $column['requires'][0]['default_show'] ) && $column['requires'][0]['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && isset( $column['requires']['default_show'] ) && $column['requires']['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}
		echo '>' . $column['label'] . '</div>';
		$editor_settings = array( 'wpautop' => true, 'default_editor' => 'TinyMCE' );
		if( $column['required'] ){
			$editor_settings['editor_class'] = 'wpep-wp-editor-required tinymce_enabled';
		}
		$editor_value = '';
		if( $this->id )
			$editor_value = nl2br( stripslashes( $column['value'] ) );
		wp_editor( $editor_value, $column['name'], $editor_settings  );
		if (isset($column['validation'])) echo '<span id="' . $column['name'] . '_validation" class="ec_validation_error">' . $column['message'] . '</span>';
	}
	protected function print_toggle_field( $column ){
		wp_easycart_admin( )->load_toggle_group( $column['name'], $column['onclick'], $column['value'], $column['label'], $column['description'] );
	}
	protected function print_checkbox_field( $column ){
		echo '<div id="ec_admin_row_' . $column['name'] . '"';
		if( $this->id && isset( $column['requires'] ) && isset($this->item) && !isset( $column['requires']['name'] ) ){
			$hide = false;
			for( $i=0; $i<count( $column['requires'] ); $i++ ){
				if( $this->item->{$column['requires'][$i]['name']} != $column['requires'][$i]['value'] )
					$hide = true;
			}
			if( $hide )
				echo ' class="ec_admin_hidden"';
		}else if($this->id && isset( $column['requires'] ) && isset($this->item) && ( ( is_array( $column['requires']['value'] ) && !in_array( $this->item->{$column['requires']['name']}, $column['requires']['value'] ) ) || ( !is_array( $column['requires']['value'] ) && $this->item->{$column['requires']['name']} != $column['requires']['value'] ) ) ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && is_array( $column['requires'] ) && isset( $column['requires'][0] ) && isset( $column['requires'][0]['default_show'] ) && $column['requires'][0]['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && isset( $column['requires']['default_show'] ) && $column['requires']['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}
		echo '>';
		echo '<div class="wp_easycart_admin_no_padding">';
        echo '<div class="wp-easycart-admin-toggle-group">';
        echo '<label for="' . $column['name'] . '">';
        if( isset( $column['onclick'] ) && $column['onclick'] == 'show_pro_required' )
			echo '<span class="dashicons dashicons-lock" style="color:#FC0; margin-top:-3px;"></span>';
        echo $column['label'] . '</label>';
        echo '<fieldset class="wp-easycart-admin-field-container">';
		echo '<input type="checkbox" name="' . $column['name'] . '" id="' . $column['name'] . '" value="1"';
		if( isset($column['read-only']) && $column['read-only']) echo ' class="wpec-admin-readonly" readonly ';
		if( isset( $column['show'] ) )
			echo ' onchange="ec_admin_show_hide_update( \'' . $column['name'] . '\', \'' . $column['show']['value'] . '\', \'ec_admin_row_' . $column['show']['name'] . '\' )"';
		if( ($this->id && $column['value']) ) {
			echo ' checked="checked"';
		} else if( !$this->id && isset($column['selected']) && $column['selected'] == true) {
			echo ' checked="checked"';
		}
		if( $column['required'] )
			echo ' class="wpep-required" wpec-admin-validation-type="' . $column['validation_type'] . '"';
		if (isset($column['onclick']) && $column['onclick']) 
			echo 'onclick="return ' . $column['onclick'] . '( \''. $column['name'] .'\');"';
		echo  ' /> ';
		if( $column['required'] )
			echo '<span id="' . $column['name'] . '_validation" class="ec_validation_error">' . $column['message'] . '</span>';
		echo '<div class="wp-easycart-admin-onoffswitch wp-easycart-admin-pull-right" aria-hidden="true">';
            echo '<div class="wp-easycart-admin-onoffswitch-label">';
                echo '<div class="wp-easycart-admin-onoffswitch-inner"></div>';
                echo '<div class="wp-easycart-admin-onoffswitch-switch">';
                    echo '<div class="wp-easycart-admin-dual-ring wp_easycart_toggle_saving" style="display: none;"></div>';
                    echo '<div class="dashicons-before dashicons-yes-alt wp_easycart_toggle_saved" style="display: none;"></div>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
		echo '</fieldset></div></div></div>';
	}
	protected function print_popup_field( $column ){
		echo '<div id="ec_admin_row_' . $column['name'] . '"';
		if( $this->id && isset( $column['requires'] ) && isset($this->item) && !isset( $column['requires']['name'] ) ){
			$hide = false;
			for( $i=0; $i<count( $column['requires'] ); $i++ ){
				if( $this->item->{$column['requires'][$i]['name']} != $column['requires'][$i]['value'] )
					$hide = true;
			}
			if( $hide )
				echo ' class="ec_admin_hidden"';
		}else if($this->id && isset( $column['requires'] ) && isset($this->item) && ( ( is_array( $column['requires']['value'] ) && !in_array( $this->item->{$column['requires']['name']}, $column['requires']['value'] ) ) || ( !is_array( $column['requires']['value'] ) && $this->item->{$column['requires']['name']} != $column['requires']['value'] ) ) ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && is_array( $column['requires'] ) && isset( $column['requires'][0] ) && isset( $column['requires'][0]['default_show'] ) && $column['requires'][0]['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && isset( $column['requires']['default_show'] ) && $column['requires']['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}
		echo '>' . $column['label'] . '<input type="submit" value="' . $column['button'] . '" class="ec_admin_settings_simple_button" onclick="return ' . $column['click'] . '( );"></div>';
		if( $column['required'] )
			echo '<span id="' . $column['name'] . '_validation" class="ec_validation_error">' . $column['message'] . '</span>';
	}
	
	protected function print_date_field( $column ){
		if ($column['value'] == '') $print_date = '';
		else if ( is_numeric($column['value']) && (int)$column['value'] == $column['value'] ) $print_date = date( 'Y-m-d', $column['value']);
		else if ( !is_numeric($column['value']) || (int)$column['value'] != $column['value'] ) $print_date = date( 'Y-m-d', strtotime( $column['value'] ) );
		
		echo '<script>jQuery(document).ready(function(){jQuery(".wp-ec-datepicker").datepicker();	});</script>';
		echo '<div id="ec_admin_row_' . $column['name'] . '"';
		if( isset( $column['requires'] ) && isset($this->item) && ( ( is_array( $column['requires']['value'] ) && !in_array( $this->item->{$column['requires']['name']}, $column['requires']['value'] ) ) || ( !is_array( $column['requires']['value'] ) && $this->item->{$column['requires']['name']} != $column['requires']['value'] ) ) )
			echo ' class="ec_admin_hidden"';
		else if (!$this->id && isset( $column['requires']) && $column['requires']['default_show'] == false )
			echo ' class="ec_admin_hidden"';
		echo '>';
        echo '<div class="wp_easycart_admin_no_padding">';
        echo '<div class="wp-easycart-admin-toggle-group-text">';
        echo '<label>' . $column['label'] . '</label>';
        echo '<fieldset class="wp-easycart-admin-field-container">';
		echo '<input type="text" autocomplete="off" name="' . $column['name'] . '" id="' . $column['name'] . '" value="';
		if( $this->id )
			echo $print_date;
		echo '"';
		
		if(isset($column['min'])) echo ' min="'.$column['min'].'" ';
		if(isset($column['max'])) echo ' max="'.$column['max'].'" ';
		
		if( isset($column['read-only']) && $column['read-only']) echo ' class="wpec-admin-readonly" readonly ';
		if( $column['required'] )
			echo ' class="wpep-required wp-ec-datepicker" wpec-admin-validation-type="' . $column['validation_type'] . '"';
		else
			echo ' class="wp-ec-datepicker" ';
		if( isset( $column['show'] ) )
			echo ' onchange="ec_admin_show_hide_update( \'' . $column['name'] . '\', \'' . $column['show']['value'] . '\', \'ec_admin_row_' . $column['show']['name'] . '\' )"';
		echo ' />';
		if( $column['required'] )
			echo '<span id="' . $column['name'] . '_validation" class="ec_validation_error">' . $column['message'] . '</span>';
		echo '</fieldset></div></div></div>';
			
	}
	
	protected function print_star_rating_field( $column ){
		echo '<div id="ec_admin_row_' . $column['name'] . '"';
		if( $this->id && isset( $column['requires'] ) && isset($this->item) && !isset( $column['requires']['name'] ) ){
			$hide = false;
			for( $i=0; $i<count( $column['requires'] ); $i++ ){
				if( $this->item->{$column['requires'][$i]['name']} != $column['requires'][$i]['value'] )
					$hide = true;
			}
			if( $hide )
				echo ' class="ec_admin_hidden"';
		}else if($this->id && isset( $column['requires'] ) && isset($this->item) && ( ( is_array( $column['requires']['value'] ) && !in_array( $this->item->{$column['requires']['name']}, $column['requires']['value'] ) ) || ( !is_array( $column['requires']['value'] ) && $this->item->{$column['requires']['name']} != $column['requires']['value'] ) ) ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && is_array( $column['requires'] ) && isset( $column['requires'][0] ) && isset( $column['requires'][0]['default_show'] ) && $column['requires'][0]['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}else if( !$this->id && isset( $column['requires'] ) && isset( $column['requires']['default_show'] ) && $column['requires']['default_show'] == false ){
			echo ' class="ec_admin_hidden"';
		
		}
		echo '>' . $column['label'];
		
		echo '<div class="ec_admin_rating_bar">' . $this->display_review_stars($column['value']) . '</div>';
		
		echo '<input type="number" step="' . $column['step'] . '" name="' . $column['name'] . '" id="' . $column['name'] . '" value="';
		if( $this->id )
			echo $column['value'];
		echo '"';
		if($column['min']) echo ' min="'.$column['min'].'" ';
		if($column['max']) echo ' max="'.$column['max'].'" ';
		
		if( isset($column['read-only']) && $column['read-only']) echo ' class="wpec-admin-readonly" readonly ';
		if( isset( $column['show'] ) )
			echo ' onchange="ec_admin_show_hide_update( \'' . $column['name'] . '\', \'' . $column['show']['value'] . '\', \'ec_admin_row_' . $column['show']['name'] . '\' )"';
		if (isset($column['onchange']) && $column['onchange']) 
			echo ' onchange="' . $column['onchange'] . '( \''. $column['name'] .'\');"';
		if( $column['required'] )
			echo ' class="wpep-required" wpec-admin-validation-type="' . $column['validation_type'] . '"';
		echo ' />';
		if( $column['required'] )
			echo '<span id="' . $column['name'] . '_validation" class="ec_validation_error">' . $column['message'] . '</span>';
		echo '</div>';
	}
	
	protected function print_custom_field( ){ }
	
	protected function get_item( ){
			$sql = "SELECT ";
			$first = true;
			foreach( $this->columns as $column ){
				if($column['name'] != '') {
					if( !isset( $column['dependent'] ) ){
						if( !$first )
							$sql .= ", ";
						 $sql .= $column['name'];
						$first = false;
					}
				}
			}
			$sql .= " FROM " . $this->table . " WHERE " . $this->wpdb->prepare( $this->table . '.' . $this->table_key . " = %s", $this->id );
			$this->item = $this->wpdb->get_row( $sql );
	}
	
	protected function seoUrl($text) { 
		$text = strtolower(htmlentities($text)); 
		$text = str_replace(" ", "-", $text);
		return $text;
	}
	
	/* Helpers */
	protected function get_url( $param = false, $value = false, $reset_params = true, $alt_param = NULL, $alt_value = NULL ){
		$uri_parts = explode( '?', $_SERVER['REQUEST_URI'], 2 );
		$this->page_url = $uri_parts[0];
		$url = $this->page_url;
		if( !$reset_params ){
			$url .= '?';
			foreach( $this->query_params as $query_param ){
				if( $param == 'orderby' && $query_param[0] == 'pagenum' ){
					// Igrore pagenum only when resorting products.
				}else if( isset( $query_param[0] ) && isset( $query_param[1] ) && $query_param[0] != $param && ( !$alt_param || $query_param[0] != $alt_param ) ){
					$url .= '&'.$query_param[0].'='.$query_param[1];
				}
			}
			$url .= '&'.$param.'='.$value;
			if( $alt_param ){
				$url .= '&'.$alt_param.'='.$alt_value;
			}
				
		}else{
			$url .= '?page='.htmlspecialchars( $_GET['page'], ENT_QUOTES );
			if( isset( $_GET['subpage'] ) )
				$url .= '&subpage='.htmlspecialchars( $_GET['subpage'], ENT_QUOTES );
			if( $param )
				$url .= '&'.$param.'='.$value;
			if( $alt_param )
				$url .= '&'.$alt_param.'='.$alt_value;
		}
		return $url;
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