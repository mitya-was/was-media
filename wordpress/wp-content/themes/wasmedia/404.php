<?php

use \Timber\Timber;

$context = Timber::get_context();

Timber::render("was-404.twig", $context, TWIG_CACHE_TIME);