<?php namespace Arcanedev\QrCode\Modes\Contracts;

interface EncodeModeInterface
{
    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @return mixed
     */
    public function setDatas($data, $dataBits);

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @return array
     */
    public function getEncodingResults();
}