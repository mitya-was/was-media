<?php

/**
 * @param string $filePath
 *
 * @return array
 */
function parse_json_file(string $filePath): array {
    $string = file_get_contents($filePath);

    return (array) json_decode($string, true);
}

/**
 * @param $type
 * @param $scope
 *
 * @return null|string
 */
function get_hashed_asset_file($type, $scope) {
    $result = null;
    $theme_was_folder = get_template_directory();
    $hashed_asset_files = parse_json_file($theme_was_folder . "/assets.json");

    if (count($hashed_asset_files) > 0 && isset($hashed_asset_files[$scope][$type])) {
        $result = get_template_directory_uri() . $hashed_asset_files[$scope][$type];
    }

    return $result;
}