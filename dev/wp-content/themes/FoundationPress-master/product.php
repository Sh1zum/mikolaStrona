<?php
/**
 * Template Name: Product Page
 * Description: A Product Template.
 * Template Post Type: post, page, product
 */
$context = Timber::context();
$context['post'] = new Timber\Post();
Timber::render( 'templates/product.twig', $context );