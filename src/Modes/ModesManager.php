<?php namespace Arcanedev\QrCode\Modes;

class ModesManager implements Contracts\ModesManagerInterface
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    protected $data;

    protected $structureAppendN;
    protected $structureAppendM;
    protected $structureAppendParity;
    protected $structureAppendOriginalData;

    /** @var NumericMode | AlphanumericMode | EightBitMode */
    protected $mode;

    const NUMERIC_MODE          = "numeric";
    const ALPHA_NUMERIC_MODE    = "alpha-numeric";
    const EIGHT_BITS_MODE       = "8bits";

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param $data
     *
     * @return ModesManager
     */
    public function setData($data)
    {
        $this->data = $data;

        $this->init();

        return $this;
    }

    /**
     * @param $n
     * @param $m
     * @param $parity
     * @param $original_data
     */
    public function setStructureAppend($n, $m, $parity, $original_data)
    {
        $this->structureAppendN               = $n;
        $this->structureAppendM               = $m;
        $this->structureAppendParity          = $parity;
        $this->structureAppendOriginalData   = $original_data;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->mode->getEncodingResults();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Function
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @return ModesManager
     */
    private function init()
    {
        $this->mode = $this->switchMode();

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
        $n              = $this->structureAppendN;
        $m              = $this->structureAppendM;
        $parity         = $this->structureAppendParity;
        $originalData   = $this->structureAppendOriginalData;

        $dataCounter   = 0;

        if ( $this->checkStructureAppend($n, $m) )
        {
            $dataValue[0]  = 3;
            $dataBits[0]   = 4;

            $dataValue[1]  = $m - 1;
            $dataBits[1]   = 4;

            $dataValue[2]  = $n - 1;
            $dataBits[2]   = 4;

            $originalDataLength = strlen($originalData);

            if ( $originalDataLength > 1 )
            {
                $parity = 0;

                $i      = 0;
                while ($i < $originalDataLength)
                {
                    $parity = ($parity ^ ord(substr($originalData, $i, 1)));
                    $i++;
                }
            }

            $dataValue[3]  = $parity;
            $dataBits[3]   = 8;

            $dataCounter   = 4;
        }

        $dataBits[$dataCounter] = 4;

        return $dataBits;
    }

    /**
     * Get the encoding mode name
     *
     * @return string
     */
    public function getModeName()
    {
        return $this->isNumericData()
            ? self::NUMERIC_MODE
            : ( $this->isAlphaNumericData()
                ? self::ALPHA_NUMERIC_MODE
                : self::EIGHT_BITS_MODE
            );
    }

    /**
     * Switch the Encoding mode
     *
     * @return AlphanumericMode | EightBitMode | NumericMode
     */
    private function switchMode()
    {
        switch ( $this->getModeName() )
        {
            case self::NUMERIC_MODE:
                return new NumericMode;

            case self::ALPHA_NUMERIC_MODE:
                return new AlphanumericMode;

            case self::EIGHT_BITS_MODE:
            default:
                return new EightBitMode;
        }
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