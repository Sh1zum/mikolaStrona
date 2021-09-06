<?php
/**
 * Template Name: Homepage
 * Description: A Homepage Template.
 */
$context = Timber::context();
$context['post'] = new Timber\Post();

$args1 = array(
                'post__in' => array(51,49,46,48,45,47),
                'post_type' => 'product',
                'orderby' => 'date',
                'order'   => 'ASC'
	);

$args2 = array(
	'post_type' => 'post',
        'posts_per_page' => 4
	);

$context['posts_products'] = new Timber\PostQuery($args1);
$context['posts_blog'] = new Timber\PostQuery($args2);

Timber::render( 'templates/homepage.twig', $context );