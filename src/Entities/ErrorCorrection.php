<?php namespace Arcanedev\QrCode\Entities;

class ErrorCorrection
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @const int Error Correction Level Low (7%) */
    const LEVEL_LOW = 1;

    /** @const int Error Correction Level Medium (15%) */
    const LEVEL_MEDIUM = 0;

    /** @const int Error Correction Level Quartile (25%) */
    const LEVEL_QUARTILE = 3;

    /** @const int Error Correction Level High (30%) */
    const LEVEL_HIGH = 2;

    /** @var int */
    protected $errorCorrection = self::LEVEL_MEDIUM;

    /** @var array */
    protected $availableErrorCorrections = [
        self::LEVEL_LOW,
        self::LEVEL_MEDIUM,
        self::LEVEL_QUARTILE,
        self::LEVEL_HIGH
    ];

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Return QR Code error correction level
     *
     * @return int
     */
    public function get()
    {
        return $this->errorCorrection;
    }

    /**
     * Set QR Code error correction level
     *
     * @param  int $errorCorrection
     *
     * @return ErrorCorrection
     */
    public function set($errorCorrection)
    {
        if ( $this->isInErrorCorrections($errorCorrection) ) {
            $this->errorCorrection = $errorCorrection;
        }

        return $this;
    }

    /**
     * Set QR Code error correction level
     *
     * @param  int $errorCorrection
     *
     * @return ErrorCorrection
     */
    public function setErrorCorrection($errorCorrection)
    {
        if ( $this->isInErrorCorrections($errorCorrection) ) {
            $this->errorCorrection = $errorCorrection;
        }

        return $this;
    }

    /**
     * Return QR Code available error correction levels
     *
     * @return array
     */
    private function getAvailableErrorCorrections()
    {
        return $this->availableErrorCorrections;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param $errorCorrection
     *
     * @return bool
     */
    private function isInErrorCorrections($errorCorrection)
    {
        return in_array($errorCorrection, $this->getAvailableErrorCorrections());
    }
}
