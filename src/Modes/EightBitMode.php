<?php namespace Arcanedev\QrCode\Modes;

class EightBitMode extends Base\EncodeMode
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
        $this->codewordNumPlus  = $this->getEightByteModeArray();
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
        parent::init(4, 8);

        $i = 0;

        while ($i < $this->getDataLength()) {
            $this->setDataValue($this->getASCIIFromOneSegment($i));
            $this->setDataBits(8);
            $this->incrementDataCounter();
            $i++;
        }
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Return ASCII value of a char
     *
     * @param int $index
     *
     * @return int
     */
    private function getASCIIFromOneSegment($index)
    {
        return ord($this->getOneSegmentFromText($index));
    }
}