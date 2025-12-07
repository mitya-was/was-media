<?php

if (empty($_GET['action']) || (isset($_GET['action']) && $_GET['action'] == 'edit')) {
	skip_not_active_functions(['get_field', 'pll_register_string', 'pll__']);
} else {
	skip_not_active_functions(['pll_register_string', 'pll__']);
}

function skip_not_active_functions(array $functions_name)
{
	// Безопасная заглушка без eval - функции уже определены в других местах или через плагины
	return;
}

// Безопасные заглушки если функции не существуют
if (!function_exists('get_field')) {
	function get_field($field, $post_id = false) {
		return false;
	}
}

if (!function_exists('pll_register_string')) {
	function pll_register_string($name, $string) {
		return false;
	}
}

if (!function_exists('pll__')) {
	function pll__($string) {
		return $string;
	}
}
