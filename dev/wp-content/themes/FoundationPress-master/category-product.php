<?php
/**
 * Template Name: Category Product Page
 * Description: A Category Product Template.
 * Template Post Type: post, page, product
 */
$context = Timber::context();
$context['post'] = new Timber\Post();

if($context['post']->post_title == 'Oferta') {
	$args = array(
					'post__in' => array(50,49,46,48,45,47,51,470),
					'post_type' => 'product',
                                        'orderby' => 'date',
					'order'   => 'ASC'
				);
} else {
	$args = array('post_type' => 'product',
					'post_parent' => $context['post']->ID,
					'orderby' => 'date',
					'order'   => 'ASC'
	
	);
}
$context['posts'] = new Timber\PostQuery($args);
Timber::render( 'templates/category-product.twig', $context );