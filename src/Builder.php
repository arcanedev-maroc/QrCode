<?php namespace Arcanedev\QrCode;

use Arcanedev\QrCode\Entities\Color;
use Arcanedev\QrCode\Entities\ImageType;

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
    protected $path;

    /** @var string */
    protected $image_path;

    /** @var string */
    protected $text     = '';

    /** @var int */
    protected $size     = 0;
    /** @var int */
    protected $moduleSize;

    /** @var int */
    protected $padding  = 16;

    protected $version;

    protected $imageType;

    /** @const string Image type png */
    const IMAGE_TYPE_PNG = 'png';
    /** @const string Image type gif */
    const IMAGE_TYPE_GIF = 'gif';
    /** @const string Image type jpeg */
    const IMAGE_TYPE_JPEG = 'jpeg';
    /** @const string Image type wbmp */
    const IMAGE_TYPE_WBMP = 'wbmp';
    /** @var string */
    protected $image_type = self::IMAGE_TYPE_PNG;
    /** @var array */
    protected $image_types_available = [
        self::IMAGE_TYPE_GIF,
        self::IMAGE_TYPE_PNG,
        self::IMAGE_TYPE_JPEG,
        self::IMAGE_TYPE_WBMP
    ];

    /** @const int Error Correction Level Low (7%) */
    const LEVEL_LOW = 1;

    /** @const int Error Correction Level Medium (15%) */
    const LEVEL_MEDIUM = 0;

    /** @const int Error Correction Level Quartile (25%) */
    const LEVEL_QUARTILE = 3;

    /** @const int Error Correction Level High (30%) */
    const LEVEL_HIGH = 2;

    /** @var int */
    protected $errorCorrection = self::LEVEL_MEDIUM;

    /** @var array */
    protected $availableErrorCorrections = [
        self::LEVEL_LOW,
        self::LEVEL_MEDIUM,
        self::LEVEL_QUARTILE,
        self::LEVEL_HIGH
    ];

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
        $this->frontDefaultColor    = (new Color)->black();
        $this->backDefaultColor     = (new Color)->white();
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
        return $this->errorCorrection;
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
        if ( $this->isInErrorCorrections($errorCorrection) ) {
            $this->errorCorrection = $errorCorrection;
        }

        return $this;
    }

    /**
     * Return QR Code available error correction levels
     *
     * @return array
     */
    private function getAvailableErrorCorrections()
    {
        return $this->availableErrorCorrections;
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
        list($codeword_num_plus, $data_value, $data_counter, $data_bits, $codeword_num_counter_value) = $encodeResults;

        if ( array_key_exists($data_counter, $data_bits) && $data_bits[$data_counter] > 0) {
            $data_counter++;
        }

        $total_data_bits    = $this->getDataBitsTotal($data_bits, $data_counter);

        $max_data_bits      = $this->getMaxDataBits($codeword_num_plus, $total_data_bits);

        if ( $this->isVersionTooLarge() ) {
            throw new VersionTooLargeException('QRCode : version too large');
        }

        $total_data_bits                        += $codeword_num_plus[$this->getVersion()];
        $data_bits[$codeword_num_counter_value] += $codeword_num_plus[$this->getVersion()];

        // Read version ECC data file
        $fp1                    = fopen ($this->getEccDatFilePath(), "rb");
        $matX                   = fread($fp1, $this->getByteNumber());
        $matY                   = fread($fp1, $this->getByteNumber());
        $masks                  = fread($fp1, $this->getByteNumber());
        $fi_x                   = fread($fp1, 15);
        $fi_y                   = fread($fp1, 15);
        $rs_ecc_codewords       = ord(fread($fp1, 1));
        $rso                    = fread($fp1, 128);
        fclose($fp1);

        $max_data_codewords     = ($max_data_bits >> 3);

        $rs_cal_table_array     = $this->getRSCalTableArray($rs_ecc_codewords);

        // Set terminator
        list($data_value, $data_bits) = $this->setTerminator($total_data_bits, $max_data_bits, $data_value, $data_counter, $data_bits);

        $codewords      = $this->getCodewords($data_counter, $data_value, $data_bits, $max_data_codewords);

        // RS-ECC prepare
        $codewords      = $this->prepareRsEcc($max_data_codewords, $codewords, $rso, $rs_ecc_codewords, $rs_cal_table_array);

        $matrixContent  = $this->getMatrixContent($codewords, $masks, $matX, $matY);

        // Format information
        $matrixContent  = $this->formatInformation($matrixContent, $fi_x, $fi_y);

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

    private function getDataBitsTotal($data_bits, $data_counter)
    {
        $i                  = 0;
        $total_data_bits    = 0;

        while ($i < $data_counter)
        {
            $total_data_bits += $data_bits[$i];
            $i++;
        }

        return $total_data_bits;
    }

    private function getMaxDataBits($codeword_num_plus, $total_data_bits)
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

        if ( $this->isVersionNumeric() && $this->getVersion() ) {
            return $maxDataBitsArray[$this->getVersion() + 40 * $this->getEccCharacter()];
        }

        // Auto version select
        $i       = 1 + 40 * $this->getEccCharacter();
        $j       = $i + 39;
        $version = 1;

        while ($i <= $j)
        {
            if ( ($maxDataBitsArray[$i]) >= ($total_data_bits + $codeword_num_plus[$version]) ) {
                $this->setVersion($version);

                return $maxDataBitsArray[$i];
            }

            $i++;
            $version++;
        }

        return 0;
    }

    /**
     * @param $matrix_content
     *
     * @return int
     */
    private function getMaskNumber($matrix_content)
    {
        $max_modules_1side              = $this->getMaxModulesOneSide();

        list($hor_master, $ver_master)  = $this->getMaskSelect($matrix_content);

        $i                  = 0;
        $all_matrix         = $max_modules_1side * $max_modules_1side;
        $mask_number        = 0;
        $min_demerit_score  = 0;

        while ($i < 8)
        {
            $demerit_n1     = 0;
            $ptn_temp       = [];
            $bit            = 1<< $i;
            $bit_r          = (~$bit) & 255;
            $bit_mask       = str_repeat(chr($bit), $all_matrix);
            $hor            = $hor_master & $bit_mask;
            $ver            = $ver_master & $bit_mask;

            $ver_shift1     = $ver . str_repeat(chr(170), $max_modules_1side);
            $ver_shift2     = str_repeat(chr(170), $max_modules_1side) . $ver;
            $ver_shift1_0   = $ver.str_repeat(chr(0), $max_modules_1side);
            $ver_shift2_0   = str_repeat(chr(0), $max_modules_1side) . $ver;
            $ver_or         = chunk_split(~($ver_shift1 | $ver_shift2), $max_modules_1side, chr(170));
            $ver_and        = chunk_split(~($ver_shift1_0 & $ver_shift2_0), $max_modules_1side, chr(170));

            $hor            = chunk_split(~$hor, $max_modules_1side, chr(170));
            $ver            = chunk_split(~$ver, $max_modules_1side, chr(170));
            $hor            = $hor . chr(170) . $ver;

            $n1_search      = "/" . str_repeat(chr(255), 5) . "+|" . str_repeat(chr($bit_r), 5). "+/";
            $n3_search      = chr($bit_r) . chr(255) . chr($bit_r) . chr($bit_r) . chr($bit_r) . chr(255) . chr($bit_r);

            $demerit_n3     = substr_count($hor, $n3_search) * 40;
            $demerit_n4     = floor(abs(( (100 * (substr_count($ver, chr($bit_r)) / $this->getByteNumber()) ) - 50) / 5)) * 10;

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
                $mask_number        = $i;
                $min_demerit_score  = $demerit_score;
            }

            $i++;
        }

        return $mask_number;
    }

    /**
     * @param $matrix_content
     *
     * @return array
     */
    private function getMaskSelect($matrix_content)
    {
        $max_modules_1side = $this->getMaxModulesOneSide();

        $hor_master = "";
        $ver_master = "";
        $k = 0;

        while ($k < $max_modules_1side) {
            $l = 0;
            while ($l < $max_modules_1side) {
                $hor_master = $hor_master . chr($matrix_content[$l][$k]);
                $ver_master = $ver_master . chr($matrix_content[$k][$l]);
                $l++;
            }
            $k++;
        }

        return [$hor_master, $ver_master];
    }

    /**
     * @return mixed
     */
    private function getOneMatrixRemainBit()
    {
        $matrixRemainBitArray = [
            0,0,7,7,7,7,7,0,0,0,0,0,0,0,3,3,3,3,3,3,3,
            4,4,4,4,4,4,4,3,3,3,3,3,3,3,0,0,0,0,0,0
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
     * @param $filename
     *
     * @return string
     */
    private function getDatFilePath($filename)
    {
        return "{$this->getDataPath()}/{$filename}.dat";
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
     * @param $error_correction
     *
     * @return bool
     */
    private function isInErrorCorrections($error_correction)
    {
        return in_array($error_correction, $this->getAvailableErrorCorrections());
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
     * @param $rs_ecc_codewords
     *
     * @return array
     */
    private function getRSCalTableArray($rs_ecc_codewords)
    {
        $rs_cal_table_array = [];

        $fp0    = fopen($this->getRSCDatFilePath($rs_ecc_codewords), "rb");

        $i      = 0;
        while ($i < 256) {
            $rs_cal_table_array[$i] = fread($fp0, $rs_ecc_codewords);
            $i++;
        }

        fclose($fp0);

        return $rs_cal_table_array;
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

        $format_information_x1 = [0, 1, 2, 3, 4, 5, 7, 8, 8, 8, 8, 8, 8, 8, 8];
        $format_information_y1 = [8, 8, 8, 8, 8, 8, 8, 8, 7, 5, 4, 3, 2, 1, 0];
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
     * @param $total_data_bits
     * @param $max_data_bits
     * @param $data_value
     * @param $data_counter
     * @param $data_bits
     *
     * @return array
     */
    private function setTerminator($total_data_bits, $max_data_bits, $data_value, $data_counter, $data_bits)
    {
        if ( $total_data_bits <= ($max_data_bits - 4) ) {
            $data_value[$data_counter]  = 0;
            $data_bits[$data_counter]   = 4;
        }
        else {
            if ( $total_data_bits < $max_data_bits ) {
                $data_value[$data_counter]  = 0;
                $data_bits[$data_counter]   = $max_data_bits - $total_data_bits;
            }
            else {
                if ( $total_data_bits > $max_data_bits ) {
                    throw new OverflowException('QRCode : Overflow error');
                }
            }
        }
        return array($data_value, $data_bits);
    }

    /**
     * @param $max_data_codewords
     * @param $codewords
     * @param $rso
     * @param $rs_ecc_codewords
     * @param $rs_cal_table_array
     *
     * @return array
     */
    private function prepareRsEcc($max_data_codewords, $codewords, $rso, $rs_ecc_codewords, $rs_cal_table_array)
    {
        $rs_block_order     = unpack("C*", $rso);

        $i                  = 0;
        $j                  = 0;
        $rs_block_number    = 0;
        $rs_temp[0]         = "";

        while ($i < $max_data_codewords) {
            $rs_temp[$rs_block_number] .= chr($codewords[$i]);
            $j++;

            if ($j >= ($rs_block_order[$rs_block_number + 1] - $rs_ecc_codewords)) {
                $j = 0;
                $rs_block_number++;
                $rs_temp[$rs_block_number] = "";
            }

            $i++;
        }

        // RS-ECC main
        $rs_block_number    = 0;
        $rs_block_order_num = count($rs_block_order);

        while ($rs_block_number < $rs_block_order_num) {
            $rs_codewords       = $rs_block_order[$rs_block_number + 1];
            $rs_data_codewords  = $rs_codewords - $rs_ecc_codewords;

            $rstemp             = $rs_temp[$rs_block_number] . str_repeat(chr(0), $rs_ecc_codewords);
            $padding_data       = str_repeat(chr(0), $rs_data_codewords);

            $j = $rs_data_codewords;
            while ($j > 0) {
                $first = ord(substr($rstemp, 0, 1));

                if ($first) {
                    $left_chr   = substr($rstemp, 1);
                    $cal        = $rs_cal_table_array[$first] . $padding_data;
                    $rstemp     = $left_chr ^ $cal;
                }
                else {
                    $rstemp     = substr($rstemp, 1);
                }

                $j--;
            }

            $codewords = array_merge($codewords, unpack("C*", $rstemp));

            $rs_block_number++;
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
     * @param $data_counter
     * @param $data_value
     * @param $data_bits
     * @param $max_data_codewords
     *
     * @return mixed
     */
    private function getCodewords($data_counter, $data_value, $data_bits, $max_data_codewords)
    {
        // Divide data by 8bit
        $codewords_counter  = 0;
        $codewords[0]       = 0;
        $remaining_bits     = 8;

        $i = 0;
        while ($i <= $data_counter) {
            $buffer         = @$data_value[$i];
            $buffer_bits    = @$data_bits[$i];

            $flag = 1;
            while ($flag) {
                if ($remaining_bits > $buffer_bits) {
                    $codewords[$codewords_counter] = ((@$codewords[$codewords_counter] << $buffer_bits) | $buffer);
                    $remaining_bits -= $buffer_bits;
                    $flag = 0;
                } else {
                    $buffer_bits -= $remaining_bits;
                    $codewords[$codewords_counter] = (($codewords[$codewords_counter] << $remaining_bits) | ($buffer >> $buffer_bits));

                    if ($buffer_bits == 0) {
                        $flag = 0;
                    } else {
                        $buffer = ($buffer & ((1 << $buffer_bits) - 1));
                        $flag = 1;
                    }

                    $codewords_counter++;

                    if ($codewords_counter < ($max_data_codewords - 1)) {
                        $codewords[$codewords_counter] = 0;
                    }

                    $remaining_bits = 8;
                }
            }
            $i++;
        }

        // $codewords, $codewords_counter
        if ($remaining_bits != 8) {
            $codewords[$codewords_counter] = $codewords[$codewords_counter] << $remaining_bits;
        } else {
            $codewords_counter--;
        }

        // Set padding character
        if ($codewords_counter < ($max_data_codewords - 1)) {
            $flag = 1;

            while ($codewords_counter < ($max_data_codewords - 1)) {
                $codewords_counter++;

                $codewords[$codewords_counter] = ($flag == 1) ? 236 : 17;

                $flag = $flag * (-1);
            }
        }

        // Divide data by 8bit
        $codewords_counter  = 0;
        $codewords[0]       = 0;
        $remaining_bits     = 8;

        $i = 0;
        while ($i <= $data_counter) {
            $buffer = @$data_value[$i];
            $buffer_bits = @$data_bits[$i];

            $flag = 1;
            while ($flag) {
                if ($remaining_bits > $buffer_bits) {
                    $codewords[$codewords_counter] = ((@$codewords[$codewords_counter] << $buffer_bits) | $buffer);
                    $remaining_bits -= $buffer_bits;
                    $flag = 0;
                } else {
                    $buffer_bits -= $remaining_bits;
                    $codewords[$codewords_counter] = (($codewords[$codewords_counter] << $remaining_bits) | ($buffer >> $buffer_bits));

                    if ($buffer_bits == 0) {
                        $flag = 0;
                    } else {
                        $buffer = ($buffer & ((1 << $buffer_bits) - 1));
                        $flag = 1;
                    }

                    $codewords_counter++;

                    if ($codewords_counter < ($max_data_codewords - 1)) {
                        $codewords[$codewords_counter] = 0;
                    }

                    $remaining_bits = 8;
                }
            }
            $i++;
        }

        if ($remaining_bits != 8) {
            $codewords[$codewords_counter] = $codewords[$codewords_counter] << $remaining_bits;
        } else {
            $codewords_counter--;
        }

        // Set padding character
        if ($codewords_counter < ($max_data_codewords - 1)) {
            $flag = 1;

            while ($codewords_counter < ($max_data_codewords - 1)) {
                $codewords_counter++;

                $codewords[$codewords_counter] = ($flag == 1) ? 236 : 17;

                $flag = $flag * (-1);
            }
            return $codewords;
        }

        return $codewords;
    }

    /**
     * @param $matrix_content
     *
     * @throws ImageSizeTooLargeException
     */
    private function createImage($matrix_content)
    {
        $mask_number    = $this->getMaskNumber($matrix_content);
        $mask_content   = 1 << $mask_number;

        $mib = $this->getMaxModulesOneSide() + 8;

        if ( $this->getSize() == 0 ) {
            $this->setSize($mib * $this->getModuleSize());

            if ( $this->getSize() > 1480 ) {
                throw new ImageSizeTooLargeException('QRCode : Image size too large');
            }
        }

        $this->image    = imagecreate($this->size + $this->padding * 2, $this->size + $this->padding * 2);
        imagecolorallocate($this->image, 255, 255, 255);

        $base_image     = imagecreatefrompng($this->getImagePath() . "/qrv" . $this->getVersion() . ".png");

        // Foreground Color
        $col[1]         = imagecolorallocate($base_image, $this->frontDefaultColor->getRed(), $this->frontDefaultColor->getGreen(), $this->frontDefaultColor->getBlue());
        // Background Color
        $col[0]         = imagecolorallocate($base_image, $this->backDefaultColor->getRed(), $this->backDefaultColor->getGreen(), $this->backDefaultColor->getBlue());

        $mxe    = 4 + $this->getMaxModulesOneSide();
        $i      = 4;
        $ii     = 0;

        while ($i < $mxe) {
            $j  = 4;
            $jj = 0;

            while ($j < $mxe) {
                if ($matrix_content[$ii][$jj] & $mask_content) {
                    imagesetpixel($base_image, $i, $j, $col[1]);
                }

                $j++; $jj++;
            }

            $i++; $ii++;
        }

        imagecopyresampled($this->image, $base_image, $this->padding, $this->padding, 4, 4, $this->size, $this->size, $mib - 8, $mib - 8);

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