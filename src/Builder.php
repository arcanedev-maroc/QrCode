<?php namespace Arcanedev\QrCode;

use Arcanedev\QrCode\Entities\Color;
use Arcanedev\QrCode\Entities\ErrorCorrection;
use Arcanedev\QrCode\Entities\ImageType;

use Arcanedev\QrCode\Entities\Mask;
use Arcanedev\QrCode\Exceptions\DataDoesNotExistsException;
use Arcanedev\QrCode\Exceptions\ImageSizeTooLargeException;
use Arcanedev\QrCode\Exceptions\VersionTooLargeException;
use OverflowException;

class Builder implements Contracts\BuilderInterface
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var string */
    protected $text     = '';

    /** @var int */
    protected $size     = 0;

    /** @var int */
    protected $moduleSize;

    /** @var int */
    protected $padding  = 16;

    protected $version;

    /** @var ImageType */
    protected $imageType;

    /** @var ErrorCorrection */
    protected $errorCorrection;

    protected $encodeManager;

    /** @var resource */
    protected $image    = null;

    protected $frontDefaultColor;
    protected $backDefaultColor;

    protected $frontColor;
    protected $backColor;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    function __construct()
    {
        $this->encodeManager        = new Modes\ModesManager;
        $this->imageType            = new ImageType;
        $this->initColors();
        $this->errorCorrection      = new ErrorCorrection;
    }

    public function initColors()
    {
        $this->frontDefaultColor    = (new Color)->black();
        $this->backDefaultColor     = (new Color)->white();
        $this->frontColor           = (new Color)->black();
        $this->backColor            = (new Color)->white();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Return text that will be hid in QR Code
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set text to hide in QR Code
     *
     * @param string $text Text to hide
     *
     * @return Builder
     */
    public function setText($text)
    {
        $this->text = $text;
        $this->encodeManager->setData($text);

        return $this;
    }

    /**
     * Get the generated image resource
     *
     * @return resource
     */
    public function getImage()
    {
        if ( ! $this->hasImage() ) {
            $this->create();
        }

        return $this->image;
    }

    /**
     * Return path to the data directory
     *
     * @return string
     */
    private function getDataPath()
    {
        return qr_code_assets_path('data');
    }

    /**
     * Return path to the images directory
     *
     * @return string
     */
    private function getImagePath()
    {
        return qr_code_assets_path('image');
    }

    /**
     * Return QR Code version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set QR Code version
     *
     * @param int $version
     *
     * @return Builder
     */
    public function setVersion($version)
    {
        if ( $this->checkVersion($version) ) {
            $this->version = $version;
        }

        return $this;
    }

    /**
     * Return foreground color of the QR Code
     *
     * @return Color
     */
    public function getForegroundColor()
    {
        return $this->frontColor;
    }

    /**
     * Set foreground color of the QR Code
     *
     * @param array $foregroundColor
     *
     * @return Builder
     */
    public function setForegroundColor($foregroundColor)
    {
        if ( is_null($this->frontColor) ) {
            $this->frontColor = new Color;
        }

        $this->frontColor->setFromArray($foregroundColor);

        return $this;
    }

    /**
     * @param string $foregroundHexColor
     *
     * @return Builder
     */
    public function setForegroundHexColor($foregroundHexColor)
    {
        if ( is_null($this->frontColor) ) {
            $this->frontColor = new Color;
        }

        $this->frontColor->hex($foregroundHexColor);

        return $this;
    }

    /**
     * Return background color of the QR Code
     *
     * @return Color
     */
    public function getBackgroundColor()
    {
        return $this->backColor;
    }

    /**
     * Set background color of the QR Code
     *
     * @param array $backgroundColor
     *
     * @return Builder
     */
    public function setBackgroundColor($backgroundColor)
    {
        if ( is_null($this->backColor) ) {
            $this->backColor = new Color;
        }

        $this->backColor->setFromArray($backgroundColor);

        return $this;
    }

    /**
     * @param string $backgroundHexColor
     *
     * @return Builder
     */
    public function setBackgroundHexColor($backgroundHexColor)
    {
        if ( is_null($this->frontColor) ) {
            $this->backColor = new Color;
        }

        $this->backColor->hex($backgroundHexColor);

        return $this;
    }

    /**
     * Return image type for rendering
     *
     * @return string
     */
    public function getImageFormat()
    {
        return $this->imageType->get();
    }

    /**
     * Set image type for rendering
     *
     * @param  string $type - Image type
     */
    public function setImageFormat($type)
    {
        $this->imageType->set($type);
    }

    public function getFunctionName()
    {
        return $this->imageType->getFunctionName();
    }

    public function setFilename($filename)
    {
        $this->imageType->setTypeFromFilename($filename);
    }

    public function getAvailableImageTypes()
    {
        return $this->imageType->getAllAvailable();
    }

    /**
     * Set structure append
     *
     * @param  int    $n
     * @param  int    $m
     * @param  int    $parity
     * @param  string $originalData
     *
     * @return Builder
     */
    public function setStructureAppend($n, $m, $parity, $originalData)
    {
        $this->encodeManager->setStructureAppend($n, $m, $parity, $originalData);

        return $this;
    }

    /**
     * Return QR Code error correction level
     *
     * @return int
     */
    public function getErrorCorrection()
    {
        return $this->errorCorrection->get();
    }

    /**
     * Set QR Code error correction level
     *
     * @param  int $errorCorrection
     *
     * @return Builder
     */
    public function setErrorCorrection($errorCorrection)
    {
        $this->errorCorrection->set($errorCorrection);

        return $this;
    }


    /**
     * Return QR Code module size
     *
     * @return int
     */
    public function getModuleSize()
    {
        if ( is_null($this->moduleSize) )
        {
            $this->moduleSize = $this->getImageFormat() == "jpeg" ? 8 : 4;
        }

        return $this->moduleSize;
    }

    /**
     * Set QR Code module size
     *
     * @param int $moduleSize
     *
     * @return Builder
     */
    public function setModuleSize($moduleSize)
    {
        $this->moduleSize = $moduleSize;
    }

    /**
     * Return QR Code size (width)
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set QR Code size (width)
     *
     * @param int $size
     *
     * @return Builder
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Return padding around the QR Code
     *
     * @return int
     */
    public function getPadding()
    {
        return $this->padding;
    }

    /**
     * Set padding around the QR Code
     *
     * @param int $padding
     *
     * @return Builder
     */
    public function setPadding($padding)
    {
        $this->padding = $padding;

        return $this;
    }


    /**
     * Get the Text/Data Length
     *
     * @return int
     */
    private function getDataLength()
    {
        return strlen( $this->getText() );
    }

    public function getHeaderAttributes()
    {
        return $this->imageType->getHeaderAttributes();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Create the image.
     *
     * @throws DataDoesNotExistsException
     * @throws VersionTooLargeException
     * @throws ImageSizeTooLargeException
     * @throws OverflowException
     */
    public function create()
    {
        if ( ! $this->hasData() ) {
            throw new DataDoesNotExistsException('QRCode: Data does not exists.');
        }

        // Determine encode mode
        $encodeResults =  $this->encodeManager->getResults();
        list($codewordNumPlus, $dataValue, $dataCounter, $dataBits, $codewordNumCounterValue) = $encodeResults;

        if ( array_key_exists($dataCounter, $dataBits) && $dataBits[$dataCounter] > 0) {
            $dataCounter++;
        }

        $totalDataBits    = $this->getDataBitsTotal($dataBits, $dataCounter);

        $maxDataBits      = $this->getMaxDataBits($codewordNumPlus, $totalDataBits);

        if ( $this->isVersionTooLarge() ) {
            throw new VersionTooLargeException('QRCode : version too large');
        }

        $totalDataBits                      += $codewordNumPlus[$this->getVersion()];
        $dataBits[$codewordNumCounterValue] += $codewordNumPlus[$this->getVersion()];

        // Read version ECC data file
        $fp1            = fopen($this->getEccDatFilePath(), "rb");
        $matX           = fread($fp1, $this->getByteNumber());
        $matY           = fread($fp1, $this->getByteNumber());
        $masks          = fread($fp1, $this->getByteNumber());
        $fiX            = fread($fp1, 15);
        $fiY            = fread($fp1, 15);
        $rsEccCodewords = ord(fread($fp1, 1));
        $rso            = fread($fp1, 128);
        fclose($fp1);

        // Set terminator
        list($dataValue, $dataBits) = $this->setTerminator($dataValue, $dataCounter, $dataBits, $totalDataBits, $maxDataBits);

        $maxDataCodewords   = ($maxDataBits >> 3);

        $codewords          = $this->getCodewords($dataValue, $dataCounter, $dataBits, $maxDataCodewords);

        // RS-ECC prepare
        $codewords          = $this->prepareRsEcc($maxDataCodewords, $codewords, $rso, $rsEccCodewords);

        $matrixContent      = $this->getMatrixContent($codewords, $masks, $matX, $matY);

        // Format information
        $matrixContent      = $this->formatInformation($matrixContent, $fiX, $fiY);

        $this->createImage($matrixContent);

        return $this->image;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @return mixed
     */
    private function getByteNumber()
    {
        return $this->getOneMatrixRemainBit() + ($this->getMaxCodewords() << 3);
    }

    private function getDataBitsTotal($dataBits, $dataCounter)
    {
        $i              = 0;
        $totalDataBits  = 0;

        while ($i < $dataCounter) {
            $totalDataBits += $dataBits[$i];
            $i++;
        }

        return $totalDataBits;
    }

    private function getMaxDataBits($codewordNumPlus, $dataBitsTotal)
    {
        if ( $this->hasVersion() && $this->isVersionNumeric() ) {
            $index = $this->getVersion() + 40 * $this->getEccCharacter();

            return $this->getMaxDataBitsByIndexFormArray($index);
        }

        return $this->autoVersionAndGetMaxDataBits($codewordNumPlus, $dataBitsTotal);
    }

    /**
     * Auto version select and Get Max Data Bits
     *
     * @param $codewordNumPlus
     * @param $dataBitsTotal
     *
     * @return int
     */
    private function autoVersionAndGetMaxDataBits($codewordNumPlus, $dataBitsTotal)
    {
        $i          = 1 + 40 * $this->getEccCharacter();
        $j          = $i + 39;
        $version    = 1;

        while ($i <= $j) {
            $maxDataBits = $this->getMaxDataBitsByIndexFormArray($i);

            if ( ($maxDataBits) >= ($dataBitsTotal + $codewordNumPlus[$version]) ) {
                $this->setVersion($version);

                return $maxDataBits;
            }

            $i++; $version++;
        }

        return 0;
    }

    private function getMaxDataBitsByIndexFormArray($index)
    {
        $maxDataBitsArray = [
            0, 128, 224, 352, 512, 688, 864, 992, 1232, 1456, 1728,
            2032,2320,2672,2920,3320,3624,4056,4504,5016,5352,
            5712,6256,6880,7312,8000,8496,9024,9544,10136,10984,
            11640,12328,13048,13800,14496,15312,15936,16816,17728,18672,

            152,272,440,640,864,1088,1248,1552,1856,2192,
            2592,2960,3424,3688,4184,4712,5176,5768,6360,6888,
            7456,8048,8752,9392,10208,10960,11744,12248,13048,13880,
            14744,15640,16568,17528,18448,19472,20528,21616,22496,23648,

            72,128,208,288,368,480,528,688,800,976,
            1120,1264,1440,1576,1784,2024,2264,2504,2728,3080,
            3248,3536,3712,4112,4304,4768,5024,5288,5608,5960,
            6344,6760,7208,7688,7888,8432,8768,9136,9776,10208,

            104,176,272,384,496,608,704,880,1056,1232,
            1440,1648,1952,2088,2360,2600,2936,3176,3560,3880,
            4096,4544,4912,5312,5744,6032,6464,6968,7288,7880,
            8264,8920,9368,9848,10288,10832,11408,12016,12656,13328
        ];

        return $maxDataBitsArray[$index];
    }

    /**
     * @param $matrixContent
     *
     * @return int
     */
    private function getMaskNumber($matrixContent)
    {
        $mask   = new Mask($matrixContent, $this->getMaxModulesOneSide(), $this->getByteNumber());

        return $mask->getNumber();
    }

    /**
     * @return mixed
     */
    private function getOneMatrixRemainBit()
    {
        $matrixRemainBitArray = [
            0, 0, 7, 7, 7, 7, 7, 0, 0, 0, 0, 0, 0, 0, 3, 3, 3, 3, 3, 3, 3,
            4, 4, 4, 4, 4, 4, 4, 3, 3, 3, 3, 3, 3, 3, 0, 0, 0, 0, 0, 0
        ];

        return $matrixRemainBitArray[$this->getVersion()];
    }

    /**
     * @return int
     */
    private function getMaxCodewords()
    {
        $maxCodeWordsArray = [
            0,26,44,70,100,134,172,196,242,
            292,346,404,466,532,581,655,733,815,901,991,1085,1156,
            1258,1364,1474,1588,1706,1828,1921,2051,2185,2323,2465,
            2611,2761,2876,3034,3196,3362,3532,3706
        ];

        return $maxCodeWordsArray[$this->getVersion()];
    }

    private function getEccCharacter()
    {
        $ec = $this->isErrorCorrectionNumeric()
            ? $this->getErrorCorrection()
            : $this->getEccCharacterHash($this->getErrorCorrection());

        if ( ! $ec ) {
            $ec = 0;
        }

        return $ec;
    }

    /**
     * @param $filename
     *
     * @return string
     */
    private function getDatFilePath($filename)
    {
        return "{$this->getDataPath()}/{$filename}.dat";
    }

    /**
     * @return string
     */
    private function getEccDatFilePath()
    {
        return $this->getDatFilePath("qrv{$this->getVersion()}_{$this->getEccCharacter()}");
    }

    /**
     * @param $rs_ecc_codewords
     *
     * @return string
     */
    private function getRSCDatFilePath($rs_ecc_codewords)
    {
        return $this->getDatFilePath("rsc{$rs_ecc_codewords}");
    }

    /**
     * @param $index
     *
     * @return mixed
     */
    private function getEccCharacterHash($index)
    {
        $eccCharacterHash = [
            "L" => "1", "l" => "1", "M" => "0", "m" => "0", "Q" => "3", "q" => "3", "H" => "2", "h" => "2"
        ];

        return @$eccCharacterHash[$index];
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function hasImage()
    {
        return ! empty($this->image);
    }

    public function isInAvailableImageTypes($type)
    {
        return $this->imageType->isAvailable($type);
    }

    /**
     * Check if the version is a correct one
     *
     * @param int $version
     * @return bool
     */
    protected function checkVersion($version)
    {
        return $version <= 40 && $version >= 0;
    }

    /**
     * Check if has text data
     *
     * @return bool
     */
    protected function hasData()
    {
        return $this->getDataLength() > 0;
    }

    private function isVersionNumeric()
    {
        return is_numeric($this->getVersion());
    }

    private function hasVersion()
    {
        return ! is_null($this->getVersion()) && $this->isVersionNumeric();
    }

    private function isVersionTooLarge()
    {
        return $this->getVersion() > 40;
    }

    private function isErrorCorrectionNumeric()
    {
        return is_numeric($this->getErrorCorrection());
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param $rsEccCodewords
     *
     * @return array
     */
    private function getRSCalTableArray($rsEccCodewords)
    {
        $rsCalTableArray = [];

        $fp0    = fopen($this->getRSCDatFilePath($rsEccCodewords), "rb");

        $i      = 0;
        while ($i < 256) {
            $rsCalTableArray[$i] = fread($fp0, $rsEccCodewords);
            $i++;
        }

        fclose($fp0);

        return $rsCalTableArray;
    }

    /**
     * @param $codewords
     * @param $masks
     * @param $matX
     * @param $matY
     *
     * @return array
     */
    private function getMatrixContent($codewords, $masks, $matX, $matY)
    {
        // Flash matrix
        $max_modules_1side  = $this->getMaxModulesOneSide();
        $matrix_content     = [];

        $i = 0;

        while ($i < $max_modules_1side) {
            $j = 0;
            while ($j < $max_modules_1side) {
                $matrix_content[$j][$i] = 0;
                $j++;
            }

            $i++;
        }

        // Attach data
        $mask_array         = unpack("C*", $masks);
        $matrix_x_array     = unpack("C*", $matX);
        $matrix_y_array     = unpack("C*", $matY);

        $i = 0;
        while ($i < $this->getMaxCodewords()) {
            $codeword_i = $codewords[$i];
            $j = 8;

            while ($j >= 1) {
                $codeword_bits_number = ($i << 3) + $j;
                $matrix_content[$matrix_x_array[$codeword_bits_number]][$matrix_y_array[$codeword_bits_number]] = ((255 * ($codeword_i & 1)) ^ $mask_array[$codeword_bits_number]);
                $codeword_i = $codeword_i >> 1;
                $j--;
            }

            $i++;
        }

        $matrix_remain  = $this->getOneMatrixRemainBit();

        while ($matrix_remain)
        {
            $remain_bit_temp = $matrix_remain + ( $this->getMaxCodewords() << 3);
            $matrix_content[ $matrix_x_array[$remain_bit_temp] ][ $matrix_y_array[$remain_bit_temp] ] = ( 255 ^ $mask_array[$remain_bit_temp] );
            $matrix_remain--;
        }

        return $matrix_content;
    }

    /**
     * @param $matrix_content
     * @param $fi_x
     * @param $fi_y
     *
     * @return array
     */
    private function formatInformation($matrix_content, $fi_x, $fi_y)
    {
        $mask_number    = $this->getMaskNumber($matrix_content);

        $format_information_value = (($this->getEccCharacter() << 3) | $mask_number);
        $format_information_array = [
            "101010000010010", "101000100100101", "101111001111100", "101101101001011",
            "100010111111001", "100000011001110", "100111110010111", "100101010100000",
            "111011111000100", "111001011110011", "111110110101010", "111100010011101",
            "110011000101111", "110001100011000", "110110001000001", "110100101110110",
            "001011010001001", "001001110111110", "001110011100111", "001100111010000",
            "000011101100010", "000001001010101", "000110100001100", "000100000111011",
            "011010101011111", "011000001101000", "011111100110001", "011101000000110",
            "010010010110100", "010000110000011", "010111011011010", "010101111101101"
        ];

        $format_information_x1  = [0, 1, 2, 3, 4, 5, 7, 8, 8, 8, 8, 8, 8, 8, 8];
        $format_information_y1  = [8, 8, 8, 8, 8, 8, 8, 8, 7, 5, 4, 3, 2, 1, 0];
        $format_information_x2  = unpack("C*", $fi_x);
        $format_information_y2  = unpack("C*", $fi_y);

        $i = 0;

        while ($i < 15) {
            $content = substr($format_information_array[$format_information_value], $i, 1);

            $matrix_content[$format_information_x1[$i]][$format_information_y1[$i]] = $content * 255;
            $matrix_content[$format_information_x2[$i + 1]][$format_information_y2[$i + 1]] = $content * 255;
            $i++;
        }

        return $matrix_content;
    }

    /**
     * @param $totalDataBits
     * @param $maxDataBits
     * @param $dataValue
     * @param $dataCounter
     * @param $dataBits
     *
     * @return array
     */
    private function setTerminator($dataValue, $dataCounter, $dataBits, $totalDataBits, $maxDataBits)
    {
        if ( $totalDataBits <= ($maxDataBits - 4) ) {
            $dataValue[$dataCounter]  = 0;
            $dataBits[$dataCounter]   = 4;
        }
        else {
            if ( $totalDataBits < $maxDataBits ) {
                $dataValue[$dataCounter]  = 0;
                $dataBits[$dataCounter]   = $maxDataBits - $totalDataBits;
            }
            else {
                if ( $totalDataBits > $maxDataBits ) {
                    throw new OverflowException('QRCode : Overflow error');
                }
            }
        }
        return array($dataValue, $dataBits);
    }

    /**
     * @param $maxDataCodewords
     * @param $codewords
     * @param $rso
     * @param $rsEccCodewords
     *
     * @return array
     */
    private function prepareRsEcc($maxDataCodewords, $codewords, $rso, $rsEccCodewords)
    {
        $rsCalTableArray    = $this->getRSCalTableArray($rsEccCodewords);

        $rsBlockOrder       = unpack("C*", $rso);

        $i                  = 0;
        $j                  = 0;
        $rsBlockNumber      = 0;
        $rsTempOne[0]       = "";

        while ($i < $maxDataCodewords) {
            $rsTempOne[$rsBlockNumber] .= chr($codewords[$i]);
            $j++;

            if ($j >= ($rsBlockOrder[$rsBlockNumber + 1] - $rsEccCodewords)) {
                $j = 0;
                $rsBlockNumber++;
                $rsTempOne[$rsBlockNumber] = "";
            }

            $i++;
        }

        // RS-ECC main
        $rsBlockNumber      = 0;
        $rsBlockOrderNum    = count($rsBlockOrder);

        while ($rsBlockNumber < $rsBlockOrderNum) {
            $rsCodewords        = $rsBlockOrder[$rsBlockNumber + 1];
            $rsCodewordsData    = $rsCodewords - $rsEccCodewords;

            $rsTempTwo          = $rsTempOne[$rsBlockNumber] . str_repeat(chr(0), $rsEccCodewords);
            $paddingData        = str_repeat(chr(0), $rsCodewordsData);

            $j = $rsCodewordsData;
            while ($j > 0) {
                $first = ord(substr($rsTempTwo, 0, 1));

                if ($first) {
                    $leftChr    = substr($rsTempTwo, 1);
                    $cal        = $rsCalTableArray[$first] . $paddingData;
                    $rsTempTwo  = $leftChr ^ $cal;
                }
                else {
                    $rsTempTwo  = substr($rsTempTwo, 1);
                }

                $j--;
            }

            $codewords = array_merge($codewords, unpack("C*", $rsTempTwo));

            $rsBlockNumber++;
        }

        return $codewords;
    }

    /**
     * @return int
     */
    private function getMaxModulesOneSide()
    {
        return 17 + ($this->getVersion() << 2);
    }

    /**
     * @param $dataValue
     * @param $dataCounter
     * @param $dataBits
     * @param $maxDataCodewords
     *
     * @return mixed
     */
    private function getCodewords($dataValue, $dataCounter, $dataBits, $maxDataCodewords)
    {
        // Divide data by 8bit
        $codewordsCounter   = 0;
        $codewords[0]       = 0;
        $remainingBits      = 8;

        $i = 0;
        while ($i <= $dataCounter) {
            $buffer         = @$dataValue[$i];
            $bufferBits     = @$dataBits[$i];

            $flag = 1;
            while ($flag) {
                if ($remainingBits > $bufferBits) {
                    $codewords[$codewordsCounter] = ((@$codewords[$codewordsCounter] << $bufferBits) | $buffer);
                    $remainingBits -= $bufferBits;
                    $flag = 0;
                } else {
                    $bufferBits -= $remainingBits;
                    $codewords[$codewordsCounter] = (($codewords[$codewordsCounter] << $remainingBits) | ($buffer >> $bufferBits));

                    if ($bufferBits == 0) {
                        $flag = 0;
                    } else {
                        $buffer = ($buffer & ((1 << $bufferBits) - 1));
                        $flag = 1;
                    }

                    $codewordsCounter++;

                    if ($codewordsCounter < ($maxDataCodewords - 1)) {
                        $codewords[$codewordsCounter] = 0;
                    }

                    $remainingBits = 8;
                }
            }
            $i++;
        }

        // $codewords, $codewords_counter
        if ( $remainingBits != 8 ) {
            $codewords[$codewordsCounter] = $codewords[$codewordsCounter] << $remainingBits;
        }
        else {
            $codewordsCounter--;
        }

        // Set padding character
        if ($codewordsCounter < ($maxDataCodewords - 1)) {
            $flag = 1;

            while ($codewordsCounter < ($maxDataCodewords - 1)) {
                $codewordsCounter++;

                $codewords[$codewordsCounter] = ($flag == 1) ? 236 : 17;

                $flag = $flag * (-1);
            }
        }

        // Divide data by 8bit
        $codewordsCounter   = 0;
        $codewords[0]       = 0;
        $remainingBits      = 8;

        $i = 0;
        while ($i <= $dataCounter) {
            $buffer     = @$dataValue[$i];
            $bufferBits = @$dataBits[$i];

            $flag = 1;
            while ($flag) {
                if ($remainingBits > $bufferBits) {
                    $codewords[$codewordsCounter] = ((@$codewords[$codewordsCounter] << $bufferBits) | $buffer);
                    $remainingBits -= $bufferBits;
                    $flag = 0;
                }
                else {
                    $bufferBits -= $remainingBits;
                    $codewords[$codewordsCounter] = (($codewords[$codewordsCounter] << $remainingBits) | ($buffer >> $bufferBits));

                    if ($bufferBits == 0) {
                        $flag = 0;
                    } else {
                        $buffer = ($buffer & ((1 << $bufferBits) - 1));
                        $flag = 1;
                    }

                    $codewordsCounter++;

                    if ($codewordsCounter < ($maxDataCodewords - 1)) {
                        $codewords[$codewordsCounter] = 0;
                    }

                    $remainingBits = 8;
                }
            }
            $i++;
        }

        if ($remainingBits != 8) {
            $codewords[$codewordsCounter] = $codewords[$codewordsCounter] << $remainingBits;
        }
        else {
            $codewordsCounter--;
        }

        // Set padding character
        if ($codewordsCounter < ($maxDataCodewords - 1)) {
            $flag = 1;

            while ($codewordsCounter < ($maxDataCodewords - 1)) {
                $codewordsCounter++;

                $codewords[$codewordsCounter] = ($flag == 1) ? 236 : 17;

                $flag = $flag * (-1);
            }

            return $codewords;
        }

        return $codewords;
    }

    /**
     * @param $matrixContent
     *
     * @throws ImageSizeTooLargeException
     */
    private function createImage($matrixContent)
    {
        $maskNumber     = $this->getMaskNumber($matrixContent);
        $maskContent    = 1 << $maskNumber;

        $mib = $this->getMaxModulesOneSide() + 8;

        if ( $this->getSize() == 0 ) {
            $this->setSize($mib * $this->getModuleSize());

            if ( $this->getSize() > 1480 ) {
                throw new ImageSizeTooLargeException('QRCode : Image size too large');
            }
        }

        $this->image    = imagecreate($this->size + $this->padding * 2, $this->size + $this->padding * 2);
        imagecolorallocate($this->image, 255, 255, 255);

        $baseImage      = imagecreatefrompng($this->getImagePath() . "/qrv" . $this->getVersion() . ".png");

        // Foreground Color
        $col[1]         = imagecolorallocate($baseImage, $this->frontDefaultColor->getRed(), $this->frontDefaultColor->getGreen(), $this->frontDefaultColor->getBlue());
        // Background Color
        $col[0]         = imagecolorallocate($baseImage, $this->backDefaultColor->getRed(), $this->backDefaultColor->getGreen(), $this->backDefaultColor->getBlue());

        $mxe    = 4 + $this->getMaxModulesOneSide();
        $i      = 4;
        $ii     = 0;

        while ($i < $mxe) {
            $j  = 4; $jj = 0;

            while ($j < $mxe) {
                if ($matrixContent[$ii][$jj] & $maskContent) {
                    imagesetpixel($baseImage, $i, $j, $col[1]);
                }

                $j++; $jj++;
            }

            $i++; $ii++;
        }

        imagecopyresampled($this->image, $baseImage, $this->padding, $this->padding, 4, 4, $this->size, $this->size, $mib - 8, $mib - 8);

        if ( ! is_null($this->backColor) ) {
            $this->colorifyImage($this->backDefaultColor, $this->backColor);
        }

        if ( ! is_null($this->frontColor) ) {
            $this->colorifyImage($this->frontDefaultColor, $this->frontColor);
        }
    }

    /**
     * @param Color $defaultColor
     * @param Color $replaceColor
     */
    private function colorifyImage($defaultColor, $replaceColor)
    {
        $index = imagecolorclosest($this->image, $defaultColor->getRed(), $defaultColor->getGreen(), $defaultColor->getBlue());
        imagecolorset($this->image, $index, $replaceColor->getRed(), $replaceColor->getGreen(), $replaceColor->getBlue());
    }

    /**
     * Return ASCII value of a char
     *
     * @param int $i - Iterator
     *
     * @return int
     */
    public function getASCIIFromOneSegment($i)
    {
        return ord($this->getOneSegmentFromText($i));
    }

    /**
     * Return a part from text
     *
     * @param $i
     *
     * @return mixed
     */
    protected function getOneSegmentFromText($i)
    {
        return substr($this->getText(), $i, 1);
    }
}