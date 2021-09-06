<?php
/**
 * Template Name: O nas
 * Description: O nas Template.
 */
$context = Timber::context();
$context['post'] = new Timber\Post();

Timber::render( 'templates/o-nas.twig', $context );