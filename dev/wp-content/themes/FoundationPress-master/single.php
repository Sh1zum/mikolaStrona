<?php

$context = Timber::context();
$context['post'] = new Timber\Post();
Timber::render( 'templates/single.twig', $context );