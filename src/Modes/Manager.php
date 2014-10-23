<?php namespace Arcanedev\QrCode\Modes;

class Manager
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    protected $data;

    protected $structure_append_n;
    protected $structure_append_m;
    protected $structure_append_parity;
    protected $structure_append_original_data;

    /** @var NumericMode|AlphanumericMode|EightBitMode */
    protected $mode;

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    public function setData($data)
    {
        $this->data = $data;

        $this->init();

        return $this;
    }

    public function setStructureAppend($n, $m, $parity, $original_data)
    {
        $this->structure_append_n               = $n;
        $this->structure_append_m               = $m;
        $this->structure_append_parity          = $parity;
        $this->structure_append_original_data   = $original_data;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->mode->getEncodingResults();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Function
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @return Manager
     */
    private function init()
    {
        $this->mode = $this->isNumericData()
            ? new NumericMode
            : ( $this->isAlphaNumericData() ? new AlphanumericMode : new EightBitMode );

        $this->mode->setDatas($this->data, $this->prepareDataBits());

        return $this;
    }

    /**
     * Get Data Bits Array
     *
     * @return array
     */
    private function prepareDataBits()
    {
        $n              = $this->structure_append_n;
        $m              = $this->structure_append_m;
        $parity         = $this->structure_append_parity;
        $originalData   = $this->structure_append_original_data;

        $data_counter   = 0;

        if ( $this->checkStructureAppend($n, $m) )
        {
            $data_value[0]  = 3;
            $data_bits[0]   = 4;

            $data_value[1]  = $m - 1;
            $data_bits[1]   = 4;

            $data_value[2]  = $n - 1;
            $data_bits[2]   = 4;

            $originalDataLength = strlen($originalData);

            if ( $originalDataLength > 1 )
            {
                $parity = 0;
                $i = 0;

                while ($i < $originalDataLength)
                {
                    $parity = ($parity ^ ord(substr($originalData, $i, 1)));
                    $i++;
                }
            }

            $data_value[3]  = $parity;
            $data_bits[3]   = 8;

            $data_counter   = 4;
        }

        $data_bits[$data_counter] = 4;

        return $data_bits;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Function
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Check if data is numeric
     *
     * @return bool
     */
    private function isNumericData()
    {
        return $this->pregMatchData('/[^0-9]/');
    }

    /**
     * @return bool
     */
    private function isAlphaNumericData()
    {
        return $this->pregMatchData('/[^0-9A-Z \$\%\*\+\-\.\/\:]/');
    }

    /**
     * @param string $pattern
     *
     * @return bool
     */
    private function pregMatchData($pattern)
    {
        return preg_match($pattern, $this->data) === 0;
    }

    /**
     * @param $n
     * @param $m
     *
     * @return bool
     */
    private function checkStructureAppend($n, $m)
    {
        return ($n > 1 && $n <= 16) && ($m > 0 && $m <= 16);
    }
}