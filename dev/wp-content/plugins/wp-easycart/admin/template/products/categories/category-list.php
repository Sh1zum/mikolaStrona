<?php
$table = new wp_easycart_admin_table( );
$table->set_table( 'ec_category', 'category_id' );
$table->set_table_id( 'ec_admin_category_list' );
$table->set_sortable( true );
$table->set_default_sort( array( 'priority', 'category_name' ), array( 'DESC', 'ASC' ) );
$table->set_header( __( 'Manage Product Categories', 'wp-easycart' ) );
$table->set_add_new( true, 'add-new-category', __( 'Add New', 'wp-easycart' ) );
$table->set_icon( 'menu' );
$table->set_docs_link ('products','categories');
$table->set_list_columns( 
	array(
		array( 
			'name' 	=> 'category_name', 
			'label'	=> __( 'Category Name', 'wp-easycart' ),
			'format'=> 'text',
            'linked'=> true,
            'subactions'=> array(
                array(
                    'url'           => 'admin.php?page=wp-easycart-products&subpage=category-products&category_id={key}',
                    'name'          => __( 'Edit Category\'s Products', 'wp-easycart' ),
                    'action_type'   => 'subpage',
                    'action'        => 'category-products'
                ),
                array(
                    'click'         => 'return false',
                    'name'          => __( 'Duplicate', 'wp-easycart' ),
                    'action_type'   => 'duplicate',
                    'action'        => 'duplicate-category'
                ),
                array(
                    'click'         => 'return false',
                    'name'          => __( 'Delete', 'wp-easycart' ),
                    'action_type'   => 'delete',
                    'action'        => 'delete-category'
                )
            )
		),
		array( 
			'name' 	=> 'category_id', 
			'label'	=> __( 'Category ID', 'wp-easycart' ),
			'format'=> 'int'
		),
		array( 
			'select'=> '(SELECT COUNT(ec_categoryitem.category_id) FROM ec_categoryitem WHERE ec_categoryitem.category_id = `ec_category`.category_id) AS total_products',
			'name' 	=> 'total_products',
			'label'	=> __( 'Products in Category', 'wp-easycart' ),
			'format'=> 'int'
		),
		array( 
			'name' 	=> 'featured_category',
			'label'	=> __( 'Featured Category', 'wp-easycart' ),
			'format'=> 'checkbox'
		)
	)
);
$table->set_search_columns(
	array( 'ec_category.category_name' )
);
$table->set_bulk_actions(
	array(
		array(
			'name'	=> 'delete-category',
			'label'	=> __( 'Delete', 'wp-easycart' )
		)
	)
);
$table->set_actions(
	array(
		array(
			'custom'=> 'subpage',
			'name'	=> 'category-products',
			'label'	=> __( 'Edit Products', 'wp-easycart' ),
			'icon'	=> 'external'
		),
		array(
			'name'	=> 'edit',
			'label'	=> __( 'Edit', 'wp-easycart' ),
			'icon'	=> 'edit'
		),
		array(
			'name'	=> 'duplicate-category',
			'label'	=> __( 'Duplicate', 'wp-easycart' ),
			'icon'	=> 'admin-page'
		),	
		array(
			'name'	=> 'delete-category',
			'label'	=> __( 'Delete', 'wp-easycart' ),
			'icon'	=> 'trash'
		)
	)
);
$table->set_filters(
	array( )
);
$table->set_label( __( 'Category', 'wp-easycart' ), __( 'Categories', 'wp-easycart' ) );
$table->print_table( );
?>