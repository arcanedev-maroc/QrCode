<?php

if ( ! function_exists('qr_code_assets_path') ) {
    /**
     * Return the assets path
     *
     * @param  string $path
     *
     * @return string
     */
    function qr_code_assets_path($path = "") {
        return dirname(__DIR__) . '/assets' . ( ! empty($path) ? '/' . $path : $path);
    }
}

if ( ! function_exists('img_tag') ) {
    /**
     * @param string        $url
     * @param string|null   $alt
     * @param array|null    $attributes
     *
     * @return string
     */
    function img_tag($url, $alt = "", $attributes = [])
    {
        $atrs = "";

        if ( count($attributes) ) {
            $atrs = "";
            foreach ($attributes as $name => $value) {
                $atrs .= ' '. $name . '="' . $value . '"';
            }
        }

        return '<img src="' . $url . '" alt="' . $alt . '"' . $atrs . '/>';
    }
}

if ( ! function_exists('validate_hex_color') ) {
    /**
     * Check if HEX is valid
     *
     * @param string $hex
     *
     * @return bool
     */
    function validate_hex_color($hex)
    {
        if ( ! is_string($hex) ) {
            return false;
        }

        return preg_match('/^#(([0-9a-fA-F]{3}){1,2})$/', $hex) !== 0;
    }
}

if ( ! function_exists('hex_to_rgb') ) {
    /**
     * @param  string $hex
     *
     * @return array
     */
    function hex_to_rgb($hex)
    {
        if ( ! validate_hex_color($hex) ) {
            return null;
        }

        $hex = str_replace("#", "", $hex);

        if ( strlen($hex) == 3 ) {
            $red    = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $green  = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $blue   = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        }
        else {
            $red    = hexdec(substr($hex, 0, 2));
            $green  = hexdec(substr($hex, 2, 2));
            $blue   = hexdec(substr($hex, 4, 2));
        }

        return [$red, $green, $blue];
    }
}

if ( ! function_exists('rgb_to_hex') ) {
    /**
     * @param  int $red
     * @param  int $green
     * @param  int $blue
     *
     * @return string
     */
    function rgb_to_hex($red, $green, $blue)
    {
        // TODO: Add or not Add a validation for the rgb values, that's your choice.
        $hex = "#";
        $hex .= str_pad(dechex($red), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($green), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($blue), 2, "0", STR_PAD_LEFT);

        return $hex;
    }
}

if ( ! function_exists('rgb_array_to_hex') ) {
    /**
     * @param  array $rgb
     *
     * @return string
     */
    function rgb_array_to_hex(array $rgb) {
        return rgb_to_hex($rgb['r'], $rgb['g'], $rgb['b']);
    }
}

// You see what we did here (*MEME)
if ( ! function_exists('dd') ) {
    function dd($vars) {
        var_dump($vars); die();
    }
}
