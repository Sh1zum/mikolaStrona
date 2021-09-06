<?php
/**
 * Template Name: Contact
 * Description: A Contact Template.
 */
$context = Timber::context();
$context['post'] = new Timber\Post();
Timber::render( 'templates/contact.twig', $context );