<?php

function product_post_type() {
    $labels = array(
        'name'                => 'Produkty',
        'singular_name'       => 'Produkty',
        'menu_name'           => 'Produkty',
        'parent_item_colon'   => 'Produkty',
        'all_items'           => 'Wszystkie produkty',
        'view_item'           => 'Zobacz produkty',
        'add_new_item'        => 'Dodaj produkt',
        'add_new'             => 'Dodaj nowy',
        'edit_item'           => 'Edytuj produkt',
        'update_item'         => 'Aktualizuj',
        'search_items'        => 'Szukaj produktów',
        'not_found'           => 'Nie znaleziono',
        'not_found_in_trash'  => 'Nie znaleziono'
    ); 
    $args = array(
        'label' => 'product',
        'rewrite' => array(
            'slug' 			=> 'oferta'
        ),
        'description'         => 'Produkty',
        'labels'              => $labels,
        'supports'            => array( 'title', 'thumbnail'),
        'taxonomies'          => array(),
        'public'              => true, 
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 4,
        'menu_icon'           => 'dashicons-cart',
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
		'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt','page-attributes'),
		'hierarchical'        => true,
    );
    register_post_type( 'product', $args );
} 
add_action( 'init', 'product_post_type', 0 );


function material_post_type() {
    $labels = array(
        'name'                => 'Materiały',
        'singular_name'       => 'Materiały',
        'menu_name'           => 'Materiały',
        'parent_item_colon'   => 'Materiały',
        'all_items'           => 'Wszystkie materiały',
        'view_item'           => 'Zobacz materiały',
        'add_new_item'        => 'Dodaj materiał',
        'add_new'             => 'Dodaj nowy',
        'edit_item'           => 'Edytuj materiał',
        'update_item'         => 'Aktualizuj',
        'search_items'        => 'Szukaj materiałów',
        'not_found'           => 'Nie znaleziono',
        'not_found_in_trash'  => 'Nie znaleziono'
    ); 
    $args = array(
        'label' => 'material',
        'rewrite' => array(
            'slug' 			=> false,
        ),
        'description'         => 'Materiały',
        'labels'              => $labels,
        'supports'            => array( 'title', 'thumbnail'),
        'taxonomies'          => array(),
        'public'              => true, 
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-admin-appearance',
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
		'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt','page-attributes'),
		'hierarchical'        => true,
    );
    register_post_type( 'material', $args );
} 
add_action( 'init', 'material_post_type', 0 );
?>