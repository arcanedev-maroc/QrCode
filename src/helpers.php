<?php

if ( ! function_exists('qr_code_assets_path') )
{
    /**
     * Return the assets path
     *
     * @param string $path
     *
     * @return string
     */
    function qr_code_assets_path($path = "") {
        $assetsFolder = dirname(__DIR__) . '/assets';

        return $assetsFolder . ($path ? '/' . $path : $path);
    }
}

if ( ! function_exists('qr_code_img') )
{
    function qr_code_img(\Arcanedev\QrCode\QrCode $qrCode, $alt = "", $attributes = [])
    {
        $atrs = "";

        if ( count($attributes) ) {
            foreach ($attributes as $name => $value) {
                $atrs .= ' '. $name . '="' . $value . '"';
            }
        }

        return '<img src="' . $qrCode->getDataUri() . '" alt="' . $alt . '"' . $atrs . '/>';
    }
}