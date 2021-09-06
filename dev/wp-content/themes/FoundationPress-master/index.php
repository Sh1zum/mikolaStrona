<?php
global $paged;

if (!isset($paged) || !$paged) $paged = 1;

$context = Timber::context();
$context['post'] = new Timber\Post();

$args = array(
				'post_type' 		=> 'post',
				'posts_per_page'	=> 10,
				'paged' 		=> $paged
			);

$context['posts'] = new Timber\PostQuery($args);
Timber::render( 'templates/blog.twig', $context );
