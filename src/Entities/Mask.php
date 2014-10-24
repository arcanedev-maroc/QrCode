<?php namespace Arcanedev\QrCode\Entities;

class Mask
{
    /* ------------------------------------------------------------------------------------------------
     |  properties
     | ------------------------------------------------------------------------------------------------
     */
    private $matrixContent;

    private $maxModulesOneSide;

    private $byteNumber;

    public $horizontalMaster    = "";

    public $verticalMaster      = "";

    public $maskNumber          = 0;

    /* ------------------------------------------------------------------------------------------------
     |  constructor
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param array $matrixContent
     * @param int $maxModulesOneSide
     * @param int $byteNumber
     */
    function __construct($matrixContent, $maxModulesOneSide, $byteNumber)
    {
        $this->setMatrixContent($matrixContent);
        $this->setMaxModulesOneSide($maxModulesOneSide);
        $this->setByteNumber($byteNumber);

        $this->init();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param int $indexOne
     * @param int $indexTwo
     *
     * @return string
     */
    private function getMatrixContentByIndex($indexOne, $indexTwo)
    {
        $value = $this->matrixContent[$indexOne][$indexTwo];

        // Returns a one-character string containing the character specified by ascii.
        return chr($value);
    }

    /**
     * @param mixed $matrixContent
     */
    private function setMatrixContent($matrixContent)
    {
        $this->matrixContent = $matrixContent;
    }

    /**
     * @param mixed $maxModulesOneSide
     *
     * @return Mask
     */
    private function setMaxModulesOneSide($maxModulesOneSide)
    {
        $this->maxModulesOneSide = $maxModulesOneSide;

        return $this;
    }

    /**
     * @param int $byteNumber
     */
    private function setByteNumber($byteNumber)
    {
        $this->byteNumber = $byteNumber;
    }

    public function getNumber()
    {
        return $this->maskNumber;
    }

    /**
     * @return array
     */
    public function getSelect()
    {
        return [
            $this->horizontalMaster,
            $this->verticalMaster
        ];
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @return int
     */
    public function init()
    {
        $this->initSelect();

        $i                  = 0;
        $this->maskNumber   = 0;
        $min_demerit_score  = 0;

        while ($i < 8)
        {
            $demerit_n1     = 0;
            $ptn_temp       = [];
            $bit            = 1<< $i;
            $bit_r          = (~$bit) & 255;
            $bit_mask       = str_repeat(chr($bit), pow($this->maxModulesOneSide, 2));
            $hor            = $this->horizontalMaster & $bit_mask;
            $ver            = $this->verticalMaster   & $bit_mask;

            $ver_shift1     = $ver . str_repeat(chr(170), $this->maxModulesOneSide);
            $ver_shift2     = str_repeat(chr(170), $this->maxModulesOneSide) . $ver;
            $ver_shift1_0   = $ver.str_repeat(chr(0), $this->maxModulesOneSide);
            $ver_shift2_0   = str_repeat(chr(0), $this->maxModulesOneSide) . $ver;
            $ver_or         = chunk_split(~($ver_shift1 | $ver_shift2), $this->maxModulesOneSide, chr(170));
            $ver_and        = chunk_split(~($ver_shift1_0 & $ver_shift2_0), $this->maxModulesOneSide, chr(170));

            $hor            = chunk_split(~$hor, $this->maxModulesOneSide, chr(170));
            $ver            = chunk_split(~$ver, $this->maxModulesOneSide, chr(170));
            $hor            = $hor . chr(170) . $ver;

            $n1_search      = "/" . str_repeat(chr(255), 5) . "+|" . str_repeat(chr($bit_r), 5). "+/";
            $n3_search      = chr($bit_r) . chr(255) . chr($bit_r) . chr($bit_r) . chr($bit_r) . chr(255) . chr($bit_r);

            $demerit_n3     = substr_count($hor, $n3_search) * 40;
            $demerit_n4     = floor(abs(( (100 * (substr_count($ver, chr($bit_r)) / $this->byteNumber) ) - 50) / 5)) * 10;

            $n2_search1     = "/" . chr($bit_r) . chr($bit_r) . "+/";
            $n2_search2     = "/" . chr(255) . chr(255) . "+/";
            $demerit_n2     = 0;
            preg_match_all($n2_search1, $ver_and, $ptn_temp);
            foreach ($ptn_temp[0] as $str_temp)
            {
                $demerit_n2 += (strlen($str_temp) - 1);
            }

            $ptn_temp = [];
            preg_match_all($n2_search2, $ver_or, $ptn_temp);
            foreach ($ptn_temp[0] as $str_temp)
            {
                $demerit_n2 += (strlen($str_temp) - 1);
            }

            $demerit_n2 *= 3;

            $ptn_temp = [];
            preg_match_all($n1_search, $hor, $ptn_temp);
            foreach ($ptn_temp[0] as $str_temp)
            {
                $demerit_n1 += (strlen($str_temp) - 2);
            }

            $demerit_score = $demerit_n1+$demerit_n2+$demerit_n3+$demerit_n4;

            if ($demerit_score <= $min_demerit_score || $i==0)
            {
                $this->maskNumber   = $i;
                $min_demerit_score  = $demerit_score;
            }

            $i++;
        }
    }

    private function initSelect()
    {
        $k = 0;
        while ($k < $this->maxModulesOneSide) {

            $l = 0;
            while ($l < $this->maxModulesOneSide) {
                $this->horizontalMaster .= $this->getMatrixContentByIndex($l, $k);
                $this->verticalMaster   .= $this->getMatrixContentByIndex($k, $l);

                $l++;
            }

            $k++;
        }
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
}
