<?php namespace Arcanedev\QrCode\Entities;

use SebastianBergmann\Exporter\Exception;

class Color implements Contracts\ColorInterface
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    protected $red;

    protected $green;

    protected $blue;

    private static $required   = ['r', 'g', 'b'];

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    function __construct($red = 0, $green = 0, $blue = 0)
    {
        $this->setValues($red, $green, $blue);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @return int
     */
    public function getRed()
    {
        return $this->red;
    }

    /**
     * @param int $red
     */
    private function setRed($red)
    {
        $this->red = $red;
    }

    /**
     * @return int
     */
    public function getGreen()
    {
        return $this->green;
    }

    /**
     * @param int $green
     */
    private function setGreen($green)
    {
        $this->green = $green;
    }

    /**
     * @return mixed
     */
    public function getBlue()
    {
        return $this->blue;
    }

    /**
     * @param int $blue
     */
    private function setBlue($blue)
    {
        $this->blue = $blue;
    }

    /**
     * Get RGB Values
     *
     * @return array
     */
    private function getValues()
    {
        return [
            'r' => $this->red,
            'g' => $this->green,
            'b' => $this->blue
        ];
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @throws Exceptions\InvalidRGBValuesException
     */
    private function setValues($red, $green, $blue)
    {
        if ( ! $this->checkRgbValues($red, $green, $blue) ) {
            throw new Exceptions\InvalidRGBValuesException("Incorrect RGB Values (red = {$red}, green = {$green}, blue = {$blue}).");
        }

        $this->setRed($red);
        $this->setGreen($green);
        $this->setBlue($blue);
    }

    /**
     * @param array $rgbArray
     *
     * @throws Exceptions\InvalidRGBArrayException
     * @throws Exceptions\InvalidRGBValuesException
     *
     * @return Color
     */
    public function setFromArray(array $rgbArray)
    {
        if ( ! $this->checkRgbArray($rgbArray) )
            throw new Exceptions\InvalidRGBArrayException("The RGB Array must have the required keys : 'r', 'g', 'b'.");

        $this->setValues($rgbArray['r'], $rgbArray['g'], $rgbArray['b']);

        return $this;
    }

    /**
     * @return string
     */
    public function getHex()
    {
        return $this->rgbToHex();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Function
     | ------------------------------------------------------------------------------------------------
     */

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @return Color
     *
     * @throws Exceptions\InvalidRGBValuesException
     */
    public function rgb($red, $green, $blue)
    {
        $this->setValues($red, $green, $blue);

        return $this;
    }

    /**
     * @param string $hex
     *
     * @return Color
     *
     * @throws Exceptions\InvalidHexValueException
     */
    public function hex($hex)
    {
        list($red, $green, $blue) = $this->hexToRGB($hex);

        $this->rgb($red, $green, $blue);

        return $this;
    }

    /**
     * @return Color
     *
     * @throws Exceptions\InvalidRGBValuesException
     */
    public function black()
    {
        $this->rgb(0, 0, 0);

        return $this;
    }

    /**
     * @return Color
     *
     * @throws Exceptions\InvalidRGBValuesException
     */
    public function white()
    {
        $this->rgb(255, 255, 255);

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getValues();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Conversion Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     *
     * @param string $hex
     *
     * @return Color
     *
     * @throws Exceptions\InvalidRGBValuesException
     * @throws Exceptions\InvalidHexValueException
     */
    private function hexToRGB($hex)
    {
        if ( ! $this->isHexValid($hex) )
            throw new Exceptions\InvalidHexValueException;

        return hex_to_rgb($hex);
    }

    /**
     * Convert RGB To HEX
     *
     * @return string
     */
    private function rgbToHex()
    {
        return rgb_to_hex($this->red, $this->green, $this->blue);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Check if HEX is valid
     *
     * @param string $hex
     *
     * @return bool
     */
    private function isHexValid($hex)
    {
        return validate_hex_color($hex);
    }

    /**
     * @param int $red - Red Hex Value
     * @param int $green - Green Hex Value
     * @param int $blue - Blue Hex Value
     *
     * @return bool
     */
    private function checkRgbValues($red, $green, $blue)
    {
        return $this->checkValue($red) && $this->checkValue($green) && $this->checkValue($blue);
    }

    /**
     * Check Color Value Value
     *
     * @param int $value
     *
     * @return bool
     */
    private function checkValue($value)
    {
        return ( is_int($value) && ($value >= 0 && $value <= 255) );
    }

    /**
     * @param array $rgbArray
     *
     * @return bool
     */
    private function checkRgbArray($rgbArray)
    {
        if ( is_array($rgbArray) ) {
            $intersectCount = count(array_intersect_key(array_flip(self::$required), $rgbArray));

            if ( $intersectCount === count(self::$required) ) {
                return true;
            }
        }

        return false;
    }
}