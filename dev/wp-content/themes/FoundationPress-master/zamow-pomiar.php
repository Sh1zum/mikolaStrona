<?php
/**
 * Template Name: Zamów pomiar
 * Description: A Zamow pomiar Template.
 */
$context = Timber::context();
$context['post'] = new Timber\Post();
if(isset($_POST['imie'])) {
    $context['imie'] = $_POST['imie'];
}

Timber::render( 'templates/zamow-pomiar.twig', $context );