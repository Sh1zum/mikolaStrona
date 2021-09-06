<?php
/**
 * The template for displaying archive pages
 */
 
$context = Timber::context();
$context['post'] = new Timber\Post();

$args = array(
	'post_type' => 'post',
	'posts_per_page' => 2
	);

$context['posts'] = new Timber\PostQuery($args);
Timber::render( 'templates/blog.twig', $context );
