<?php namespace Arcanedev\QrCode\Contracts;

use Arcanedev\QrCode\QrCode;

interface QrCodeInterface {

    /**
     * @param string $text
     *
     * @return QrCode
     */
    public function setText($text);

    /**
     * @param int $size
     *
     * @return QrCode
     */
    public function setSize($size);

    /**
     * @param int $padding
     *
     * @return QrCode
     */
    public function setPadding($padding);

    /**
     * @param $foregroundColor
     *
     * @return QrCode
     */
    public function setForegroundColor($foregroundColor);

    /**
     * @param string $foregroundHexColor
     *
     * @return QrCode
     */
    public function setForegroundHexColor($foregroundHexColor);

    /**
     * @param array $backgroundColor
     *
     * @return QrCode
     */
    public function setBackgroundColor($backgroundColor);

    /**
     * @param string $backgroundHexColor
     *
     * @return QrCode
     */
    public function setBackgroundHexColor($backgroundHexColor);

    public function setStructureAppend($n, $m, $parity, $original_data);


}