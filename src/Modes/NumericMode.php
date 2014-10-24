<?php namespace Arcanedev\QrCode\Modes;

class NumericMode extends Base\EncodeMode
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    function __construct()
    {
        $this->codewordNumPlus  = $this->getNumericModeArray();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @return array
     */
    protected function encode()
    {
        parent::init(1, 10);

        $i = 0;

        while ($i < $this->getDataLength()) {
            if ( ($i % 3) == 0) {
                $this->setDataValue($this->getOneSegmentFromText($i));
                $this->setDataBits(4);
            }
            else {
                $this->setDataValue(($this->getDataValue() * 10) + $this->getOneSegmentFromText($i));

                if (($i % 3) == 1) {
                    $this->setDataBits(7);
                }
                else {
                    $this->setDataBits(10);
                    $this->incrementDataCounter();
                }
            }

            $i++;
        }
    }
}