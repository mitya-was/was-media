<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 11/30/2017
 * Time: 13:14
 */

namespace Utils;


use Imgix\UrlBuilder;

class ImgixBuilder {
    private $image;
    private $imageId;
    private $isUrlOnly;
    private $isSnippet;

    private $builder;

    private $options;
    private $textOptions;
    private $markOptions;
    private $snippetOptions;
    private $serviceOptions;
    private $serviceOptionNames;

    private $result;

    public static function makeCDNSiteUrl() {
        return preg_replace('/(.+\/\/)(.*?(\bwas\b).*)/s', '\1\3.imgix.net', get_site_url());
    }

    public static function buildImage($image, $options) {
        return (new self($image, $options))->getImage();
    }

    public static function buildSrcSet($image, $options) {
        return (new self($image, $options))->getSrcSet();
    }

    /**
     * ImgixBuilder constructor.
     *
     * @param       $image   - URL Or ID Or Image Array
     * @param array $options - Array Of Options
     */
    public function __construct($image, $options) {
        $this->imageId = null;
        $this->isUrlOnly = false;
        $this->isSnippet = false;
        $this->serviceOptions = ["crop" => true];
        $this->serviceOptionNames = ["i_thumb_name", "i_sizes", "i_crop", "dont_add_sizes"];
        $this->options = [
            "auto" => "compress,format",
            "fit"  => "crop",
            "crop" => "faces"
        ];
        $this->snippetOptions = [
            "txtpad"    => 150,
            "txtalign"  => "center,top",
            "marky"     => 220,
            "markalign" => "center,middle",
            "exp"       => -4
        ];
        $this->textOptions = [
            "txtsize" => 30,
            "txtfont64" => "HelveticaNeue-Medium",
            "txtclr"    => "fff"
        ];
        $this->markOptions = [
            "w"        => 800,
            "txtsize"  => 42,
            "txtfont"  => "Helvetica Neue Condensed,Bold",
            "txtcolor" => "fff",
            "txtalign" => "center,middle"
        ];

        $this->builder = new UrlBuilder("was.imgix.net");

        $this->getLocalImage($image);
        $this->collectOptions($options);

        $this->builder->setUseHttps(true);
        $this->builder->includeLibraryParam = false;
    }

    private function getImage() {

        if (!IS_CDN_ENABLED || is_string(IS_CDN_ENABLED)) {
            $correctImageUrl = $this->sizeCorrector($this->image);
            $this->result = is_string(IS_CDN_ENABLED) ?
                preg_replace(self::getPattern(), self::getReplacement(), $correctImageUrl) :
                $correctImageUrl;
        } else {

            if (strpos($this->image, ".gif") != 0) {
                return $this->image;
            }

            if (!strpos($this->image, ".png") != 0) {
                unset($this->options["auto"]);

                $this->options["fm"] = "pjpg";
            }

            $imageUrlForCDN = preg_replace(self::getPattern(), '\6', $this->image);

            if ($this->isSnippet) {

                if (isset($this->textOptions["txt"]) && $this->textOptions["txt"] != "") {
                    $this->textOptions["txt64"] = $this->textOptions["txt"];

                    unset($this->textOptions["txt"]);
                }

                $this->options = array_merge(
                    $this->options,
                    $this->snippetOptions,
                    $this->textOptions,
                    [
                        "mark64" => "https://assets.imgix.net/~text?" . urldecode(http_build_query($this->markOptions))
                    ]
                );
            }

            $this->result = $this->builder->createURL($imageUrlForCDN, $this->options);
        }

        return $this->result;
    }

    private function getSrcSet() {
        $srcSet = [];

        if (!(IS_CDN_ENABLED) || is_string(IS_CDN_ENABLED)) {

            if (isset($this->serviceOptions["i_thumb_name"])) {
                $srcSet = wp_get_attachment_image_srcset(
                    $this->imageId,
                    $this->serviceOptions["i_thumb_name"]
                );
            }

            if (isset($this->serviceOptions["i_sizes"]) && is_array($this->serviceOptions["i_sizes"]) &&
                count($this->serviceOptions["i_sizes"]) == 2) {
                $srcSet = wp_get_attachment_image_srcset(
                    $this->imageId,
                    [
                        $this->serviceOptions[0],
                        $this->serviceOptions[1]
                    ]
                );
            }

            if (is_string(IS_CDN_ENABLED)) {
                $srcSetArray = explode(",", $srcSet);

                foreach ($srcSetArray as &$url) {
                    $url = preg_replace(self::getPattern(), self::getReplacement(), $url);
                }

                $srcSet = implode(",", $srcSetArray);
            }

            return $srcSet;
        }

        foreach (get_image_sizes() as $imageSize) {
            $currentImageMeta = false;

            if ($this->imageId) {
                $currentImageMeta = wp_get_attachment_metadata($this->imageId);
            }

            if (($currentImageMeta && $imageSize['width'] < $currentImageMeta['width']) || (isset($this->serviceOptions["i_thumb_name"]) && $this->serviceOptions["i_thumb_name"] === "full")) {
                $this->options["w"] = $imageSize['width'];
                $this->options["h"] = "null";

                array_push($srcSet, $this->getImage() . ' ' . $imageSize['width'] . 'w');
            }
        }

        return implode(",", $srcSet);
    }

    private static function getPattern() {
        return '/(.+\/\/)(.*?(\bwas\b).*)(.*?(\bmedia\b).*)(\/wp-content.+)/s';
    }

    private static function getReplacement() {
        $cdnDomain = defined('CDN_DOMAIN') ? CDN_DOMAIN : '\3.\5';

        return '\1' . $cdnDomain . '\6';
    }

    private function getLocalImage($image) {

        if (is_array($image) && isset($image["ID"])) {
            $this->imageId = $image["ID"];

        } elseif (is_string($image) && strpos($image, "http") !== false) {
            $this->image = $image;

            $this->isUrlOnly = true;

        } else {
            $this->imageId = $image;
        }

        if (!$this->isUrlOnly || !is_null($this->imageId)) {
            $this->image = wp_get_attachment_image_url($this->imageId, "full");
        }
    }

    /**
     * @param array $options
     */
    private function collectOptions($options) {

        if (is_array($options)) {

            if (isset($options["i_thumb_name"])) {
                $this->options = array_merge(
                    $this->options,
                    [
                        "w" => get_image_width($options["i_thumb_name"]),
                        "h" => get_image_height($options["i_thumb_name"])
                    ]
                );
            }

            if (isset($options["i_sizes"]) && is_array($options["i_sizes"]) && count($options["i_sizes"]) == 2) {
                $this->options = array_replace(
                    $this->options,
                    [
                        "w" => $options["i_sizes"][0],
                        "h" => $options["i_sizes"][1]
                    ]
                );
            }

            if (isset($options["i_crop"]) && !$options["i_crop"]) {
                $this->serviceOptions["crop"] = false;
            }

            if ($this->serviceOptions["crop"] && $this->imageId && isset($options["i_thumb_name"])) {
                $thumbName = $options["i_thumb_name"];
                $imageMeta = wp_get_attachment_metadata($this->imageId);
                $cropRectangle = isset($imageMeta["micSelectedArea"][$thumbName]) ?
                    $imageMeta["micSelectedArea"][$thumbName] :
                    [];

                if (count($cropRectangle) > 0) {
                    list($rx, $ry, $rw, $rh, $rscale) = array_values($cropRectangle);

                    $this->options = array_merge(
                        $this->options,
                        [
                            "rect" => intval(floatval($rx) * floatval($rscale)) . "," . intval(floatval($ry) * floatval($rscale)) . "," . intval(floatval($rw) * floatval($rscale)) . "," . intval(floatval($rh) * floatval($rscale))
                        ]
                    );

                    unset($this->options["crop"]);
                }
            }

            if (isset($options["i_game"])) {
                $this->isSnippet = true;

                if (isset($options["i_game"]["i_txt"])) {
                    $this->textOptions = array_replace($this->textOptions, $options["i_game"]["i_txt"]);

                    unset($options["i_game"]["i_txt"]);
                }

                if (isset($options["i_game"]["i_mark"])) {
                    $this->markOptions = array_replace($this->markOptions, $options["i_game"]["i_mark"]);

                    unset($options["i_game"]["i_mark"]);
                }

                $this->snippetOptions = array_replace($this->snippetOptions, $options["i_game"]);

                unset($options["i_game"]);
            }

            foreach ($this->serviceOptionNames as $serviceOptionName) {

                if (isset($options[$serviceOptionName])) {
                    $this->serviceOptions = array_merge($this->serviceOptions, [$serviceOptionName => $options[$serviceOptionName]]);

                    unset($options[$serviceOptionName]);
                }
            }

            $this->options = array_replace($this->options, $options);
        }
    }

    private function sizeCorrector($image) {
        $resultImageUrl = $image;

        if ($this->imageId && isset($this->serviceOptions["i_thumb_name"])) {
            $resultImageUrl = wp_get_attachment_image_url($this->imageId, $this->serviceOptions["i_thumb_name"]);
        }

        if ($this->isUrlOnly && isset($this->serviceOptions["i_thumb_name"])) {
            $replacement = (!isset($this->serviceOptions["dont_add_sizes"])) ? '-' . get_image_width($this->serviceOptions["i_thumb_name"]) . 'x' . get_image_height($this->serviceOptions["i_thumb_name"]) : "";
            $resultImageUrl = substr_replace(
                $image,
                $replacement,
                strrpos($image, '.'),
                0
            );
        }

        if ($this->isUrlOnly && isset($this->serviceOptions["i_sizes"]) && count($this->serviceOptions["i_sizes"]) == 2) {
            $replacement = (!isset($this->serviceOptions["dont_add_sizes"])) ? '-' . $this->serviceOptions["i_sizes"][0] . 'x' . $this->serviceOptions["i_sizes"][1] : "";
            $resultImageUrl = substr_replace(
                $image,
                $replacement,
                strrpos($image, '.'),
                0
            );
        }

        return $resultImageUrl;
    }
}