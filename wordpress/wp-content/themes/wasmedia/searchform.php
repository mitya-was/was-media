<?php

use Timber\Timber;

$search_form_id = esc_attr(uniqid('search-form-'));

$context = Timber::get_context();

$context["search_form_id"] = $search_form_id;
$context["search_form_action"] = esc_url(home_url('/'));
$context["search_form_value"] = get_search_query();

Timber::render("template-parts/was-search-form.twig", $context, TWIG_CACHE_TIME);