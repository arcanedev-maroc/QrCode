<?php namespace Arcanedev\QrCode\Modes\Base;

use Arcanedev\QrCode\Modes\Contracts\EncodeModeInterface;

abstract class EncodeMode implements EncodeModeInterface
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    protected $data;

    protected $dataBits;

    protected $dataCounter;

    protected $dataValue;

    protected $codewordNumPlus;

    protected $codewordsNumCounter;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @return mixed
     */
    protected function getData()
    {
        return $this->data;
    }

    protected function getDataBits()
    {
        return $this->dataBits;
    }

    public function setDatas($data, $dataBits)
    {
        $this->data     = $data;
        $this->dataBits = $dataBits;

        return $this;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @return array
     */
    abstract protected function encode();

    /**
     * @return array
     */
    public function getEncodingResults()
    {
        $this->encode();

        return $this->getResults();
    }

    /**
     * @param int $dataValue
     * @param int $dataBits
     */
    protected function init($dataValue, $dataBits)
    {
        $this->initDataCounter();

        $this->setDataValue($dataValue);
        $this->incrementDataCounter();

        $this->setDataValue($this->getDataLength());
        $this->setDataBits($dataBits);   /* #version 1-9 */
        $this->saveCodewordsNumCounter();
        $this->incrementDataCounter();
    }

    protected function getDataValue()
    {
        return $this->dataValue[$this->dataCounter];
    }

    protected function setDataValue($value)
    {
        $this->dataValue[$this->dataCounter]    = $value;
    }

    protected function setDataBits($value)
    {
        $this->dataBits[$this->dataCounter]     = $value;
    }

    protected function saveCodewordsNumCounter()
    {
        $this->codewordsNumCounter              = $this->dataCounter;
    }

    protected function initDataCounter()
    {
        $this->dataCounter                      = count($this->dataBits) - 1;
    }

    protected function incrementDataCounter()
    {
        $this->dataCounter++;
    }

    /**
     * @return array
     */
    protected function getResults()
    {
        return [
            $this->codewordNumPlus,
            $this->dataValue,
            $this->dataCounter,
            $this->dataBits,
            $this->codewordsNumCounter
        ];
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get Text Length
     *
     * @return int
     */
    protected function getDataLength()
    {
        return strlen($this->data);
    }

    /**
     * Return a part from text
     *
     * @param $index
     *
     * @return mixed
     */
    protected function getOneSegmentFromText($index)
    {
        return substr($this->data, $index, 1);
    }

    /**
     * @return array
     */
    protected function getNumericModeArray()
    {
        return [
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
            2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2,
            4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4
        ];
    }

    /**
     * @return array
     */
    protected function getAlphaNumericModeArray()
    {
        return [
            "0" => 0,  "1" => 1,  "2" => 2,  "3" => 3,  "4" => 4,
            "5" => 5,  "6" => 6,  "7" => 7,  "8" => 8,  "9" => 9,
            "A" => 10, "B" => 11, "C" => 12, "D" => 13, "E" => 14,
            "F" => 15, "G" => 16, "H" => 17, "I" => 18, "J" => 19,
            "K" => 20, "L" => 21, "M" => 22, "N" => 23, "O" => 24,
            "P" => 25, "Q" => 26, "R" => 27, "S" => 28, "T" => 29,
            "U" => 30, "V" => 31, "W" => 32, "X" => 33, "Y" => 34,
            "Z" => 35, " " => 36, "$" => 37, "%" => 38, "*" => 39,
            "+" => 40, "-" => 41, "." => 42, "/" => 43, ":" => 44
        ];
    }

    /**
     * @return array
     */
    protected function getEightByteModeArray()
    {
        return [
            0,0,0,0,0,0,0,0,0,0,
            8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,
            8,8,8,8,8,8,8,8,8,8,8,8,8,8
        ];
    }
}