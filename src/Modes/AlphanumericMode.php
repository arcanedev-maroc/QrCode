<?php namespace Arcanedev\QrCode\Modes;

class AlphanumericMode extends Base\EncodeMode
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    protected $alphanumericCharacterHash;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    function __construct()
    {
        $this->codewordNumPlus              = $this->getNumericModeArray();
        $this->alphanumericCharacterHash    = $this->getAlphaNumericModeArray();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @return array
     */
    protected function encode()
    {
        parent::init(2, 9);

        $i = 0;

        while ($i < $this->getDataLength()) {
            if (($i % 2) == 0) {
                $this->setDataValue($this->getHashedCharacter($i));
                $this->setDataBits(6);
            }
            else {
                $this->setDataValue(($this->getDataValue() * 45) + $this->getHashedCharacter($i));
                $this->setDataBits(11);
                $this->incrementDataCounter();
            }

            $i++;
        }
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param int $index
     *
     * @return mixed
     */
    private function getHashedCharacter($index)
    {
        return $this->alphanumericCharacterHash[$this->getOneSegmentFromText($index)];
    }
}