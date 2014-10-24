<?php namespace Arcanedev\QrCode\Modes\Contracts;

interface ModesManagerInterface
{
    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param $data
     *
     * @return ModesManagerInterface
     */
    public function setData($data);

    /**
     * @param $n
     * @param $m
     * @param $parity
     * @param $original_data
     */
    public function setStructureAppend($n, $m, $parity, $original_data);

    /**
     * @return array
     */
    public function getResults();

    /**
     * Get the encoding mode name
     *
     * @return string
     */
    public function getModeName();
}