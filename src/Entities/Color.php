<?php namespace Arcanedev\QrCode\Entities;

class Color
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    protected $red;

    protected $green;

    protected $blue;

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
     * @return mixed
     */
    public function getRed()
    {
        return $this->red;
    }

    /**
     * @param int $red
     */
    protected function setRed($red)
    {
        $this->red = $red;
    }

    /**
     * @return mixed
     */
    public function getGreen()
    {
        return $this->green;
    }

    /**
     * @param int $green
     */
    protected function setGreen($green)
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
    protected function setBlue($blue)
    {
        $this->blue = $blue;
    }

    /**
     * Get RGB Values
     *
     * @return array
     */
    protected function getValues()
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
     * @throws Exceptions\IncorrectRGBValues
     */
    protected function setValues($red, $green, $blue)
    {
        if ( ! $this->checkRgbValues($red, $green, $blue) ) {
            throw new Exceptions\IncorrectRGBValues("Incorrect RGB Values [red = {$red}, green = {$green}, blue = {$blue}]");
        }

        $this->setRed($red);
        $this->setGreen($green);
        $this->setBlue($blue);
    }

    /**
     * @param array $color
     *
     * @return Color
     */
    public function setFromArray($color)
    {
        $this->setValues($color['r'], $color['g'], $color['b']);

        return $this;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Function
     | ------------------------------------------------------------------------------------------------
     */

    /**
     * @param string $hex
     *
     * @return Color
     *
     * @throws Exceptions\InvalidHexValueException
     */
    public function hex($hex)
    {
        $this->hexToRGB($hex);

        return $this;
    }

    /**
     * @return Color
     *
     * @throws Exceptions\IncorrectRGBValues
     */
    public function black()
    {
        return $this->rgb(0, 0, 0);
    }

    /**
     * @return Color
     *
     * @throws Exceptions\IncorrectRGBValues
     */
    public function white()
    {
        return $this->rgb(255, 255, 255);
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @return Color
     *
     * @throws Exceptions\IncorrectRGBValues
     */
    public function rgb($red, $green, $blue)
    {
        $this->setValues($red, $green, $blue);

        return $this;
    }

    public function toArray()
    {
        return [
            'r' => $this->red,
            'g' => $this->green,
            'b' => $this->blue
        ];
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param string $hex
     *
     * @return Color
     *
     * @throws Exceptions\IncorrectRGBValues
     * @throws Exceptions\InvalidHexValueException
     */
    private function hexToRGB($hex)
    {
        if ( ! $this->isHexValid($hex) )
            throw new Exceptions\InvalidHexValueException;

        $hex = str_replace("#", "", $hex);

        if ( strlen($hex) == 3 )
        {
            $red    = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $green  = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $blue   = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        }
        else {
            $red    = hexdec(substr($hex, 0, 2));
            $green  = hexdec(substr($hex, 2, 2));
            $blue   = hexdec(substr($hex, 4, 2));
        }

        $this->setValues($red, $green, $blue);

        return $this;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param string $hex
     *
     * @return bool
     */
    public function isHexValid($hex)
    {
        $hex = str_replace("#", "", $hex);

        if ( strlen($hex) == 3 || strlen($hex) == 6)
        {
            return true;
        }

        return false;
    }

    /**
     * @param int $red - Red Hex Value
     * @param int $green - Green Hex Value
     * @param int $blue - Blue Hex Value
     *
     * @return bool
     */
    public function checkRgbValues($red, $green, $blue)
    {
        if ( $this->checkValue($red) && $this->checkValue($green) && $this->checkValue($blue) )
            return true;

        return false;
    }

    /**
     * @param int $value - HEX Value
     *
     * @return bool
     */
    private function checkValue($value)
    {
        return (bool) ($value >= 0 && $value <= 255);
    }
}