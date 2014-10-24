<?php namespace Arcanedev\QrCode\Entities\Contracts;

use Arcanedev\QrCode\Entities\Exceptions\InvalidHexValueException;
use Arcanedev\QrCode\Entities\Exceptions\InvalidRGBArrayException;
use Arcanedev\QrCode\Entities\Exceptions\InvalidRGBValuesException;

interface ColorInterface
{
    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @return int
     */
    public function getRed();

    /**
     * @return int
     */
    public function getGreen();

    /**
     * @return int
     */
    public function getBlue();

    /**
     * @return string
     */
    public function getHex();

    /* ------------------------------------------------------------------------------------------------
     |  Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @return ColorInterface
     *
     * @throws InvalidRGBValuesException
     */
    public function rgb($red, $green, $blue);

    /**
     * @param string $hex
     *
     * @return ColorInterface
     *
     * @throws InvalidHexValueException
     */
    public function hex($hex);

    /**
     * @return ColorInterface
     *
     * @throws InvalidRGBValuesException
     */
    public function black();

    /**
     * @return ColorInterface
     *
     * @throws InvalidRGBValuesException
     */
    public function white();

    /**
     * @return array
     */
    public function toArray();

    /**
     * @param array $rgbArray
     *
     * @throws InvalidRGBArrayException
     * @throws InvalidRGBValuesException
     *
     * @return ColorInterface
     */
    public function setFromArray(array $rgbArray);
}