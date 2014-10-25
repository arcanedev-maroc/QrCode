<?php namespace Arcanedev\QrCode;

use Arcanedev\QrCode\Contracts\QrCodeInterface;
use Arcanedev\QrCode\Contracts\BuilderInterface;
use Arcanedev\QrCode\Entities\Exceptions\ImageFunctionUnknownException;

// TODO: Clean & Refactor it
class QrCode implements QrCodeInterface
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var Builder */
    private $builder;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    public function __construct(BuilderInterface $builder = null)
    {
        if ( is_null($builder) )
            $builder  = new Builder;

        $this->builder  = $builder;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * Return text that will be hid in QR Code
     *
     * @return string
     */
    public function getText()
    {
        return $this->builder->getText();
    }

    /**
     * Set text to hide in QR Code
     *
     * @param  string $text Text to hide
     * @return QrCode
     */
    public function setText($text)
    {
        $this->builder->setText($text);

        return $this;
    }

    /**
     * Return image type for rendering
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->builder->getImageFormat();
    }

    /**
     * Set image type for rendering
     *
     * @param string $format
     *
     * @return QrCode
     */
    public function setFormat($format)
    {
        $this->builder->setImageFormat($format);

        return $this;
    }

    /**
     * Get available all available image types
     *
     * @return array
     */
    public function getAvailableFormats()
    {
        return $this->builder->getAvailableImageTypes();
    }

    /**
     * Return QR Code size (width)
     *
     * @return int
     */
    public function getSize()
    {
        return $this->builder->getSize();
    }

    /**
     * Set QR Code size (width)
     *
     * @param  int    $size Width of the QR Code
     * @return QrCode
     */
    public function setSize($size)
    {
        $this->builder->setSize($size);

        return $this;
    }

    /**
     * Return padding around the QR Code
     *
     * @return int
     */
    public function getPadding()
    {
        return $this->builder->getPadding();
    }

    /**
     * Set padding around the QR Code
     *
     * @param  int    $padding Padding around QR Code
     * @return QrCode
     */
    public function setPadding($padding)
    {
        $this->builder->setPadding($padding);

        return $this;
    }

    /**
     * Return foreground color of the QR Code
     *
     * @return Entities\Color
     */
    public function getForegroundColor()
    {
        return $this->builder->getForegroundColor();
    }

    /**
     * Return foreground color of the QR Code
     *
     * @return array
     */
    public function getForeground()
    {
        return $this->getForegroundColor()->toArray();
    }

    /**
     * Return foreground color of the QR Code
     *
     * @return string
     */
    public function getForegroundHex()
    {
        return $this->getForegroundColor()->getHex();
    }

    /**
     * Set foreground color of the QR Code
     *
     * @param array $foregroundColor
     *
     * @return QrCode
     */
    public function setForegroundColor($foregroundColor)
    {
        $this->builder->setForegroundColor($foregroundColor);

        return $this;
    }

    /**
     * Set foreground color of the QR Code
     *
     * @param string $foregroundHexColor
     *
     * @return QrCode
     */
    public function setForegroundHexColor($foregroundHexColor)
    {
        $this->builder->setForegroundHexColor($foregroundHexColor);

        return $this;
    }

    /**
     * Return background color of the QR Code
     *
     * @return Entities\Color
     */
    public function getBackgroundColor()
    {
        return $this->builder->getBackgroundColor();
    }

    /**
     * Return background color of the QR Code
     *
     * @return Entities\Color
     */
    public function getBackground()
    {
        return $this->getBackgroundColor()->toArray();
    }

    /**
     * Return background color of the QR Code
     *
     * @return Entities\Color
     */
    public function getBackgroundHex()
    {
        return $this->getBackgroundColor()->getHex();
    }

    /**
     * @param string $backgroundHexColor
     *
     * @return QrCode
     */
    public function setBackgroundHexColor($backgroundHexColor)
    {
        $this->builder->setBackgroundHexColor($backgroundHexColor);

        return $this;
    }

    /**
     * Set background color of the QR Code
     *
     * @param  array  $color_background RGB color
     * @return QrCode
     */
    public function setBackgroundColor($color_background)
    {
        $this->builder->setBackgroundColor($color_background);

        return $this;
    }

    /**
     * Return QR Code version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->builder->getVersion();
    }

    /**
     * Set QR Code version
     *
     * @param  int    $version QR Code version
     * @return QrCode
     */
    public function setVersion($version)
    {
        $this->builder->setVersion($version);

        return $this;
    }

    /**
     * Set structure append
     *
     * @param  int    $n
     * @param  int    $m
     * @param  int    $parity        Parity
     * @param  string $original_data Original data
     *
     * @return QrCode
     */
    protected function setStructureAppend($n, $m, $parity, $original_data)
    {
        $this->builder->setStructureAppend($n, $m, $parity, $original_data);

        return $this;
    }

    /**
     * Set QR Code error correction level
     *
     * @param  int    $error_correction Error Correction Level
     * @return QrCode
     */
    protected function setErrorCorrection($error_correction)
    {
        $this->builder->setErrorCorrection($error_correction);

        return $this;
    }

    /**
     * Return QR Code error correction level
     *
     * @return int
     */
    protected function getErrorCorrection()
    {
        return $this->builder->getErrorCorrection();
    }

    /**
     * Return QR Code module size
     *
     * @return int
     */
    protected function getModuleSize()
    {
        return $this->builder->getModuleSize();
    }

    /**
     * Set QR Code module size
     *
     * @param  int $moduleSize - Module size
     *
     * @return QrCode
     */
    protected function setModuleSize($moduleSize)
    {
        $this->builder->setModuleSize($moduleSize);

        return $this;
    }

    public function setHeaderResponse()
    {
        header($this->getHeaderAttributes());
    }

    private function getHeaderAttributes()
    {
        return $this->builder->getHeaderAttributes();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Create QR Code and return its content
     *
     * @param  string|null $format - Image type (gif, png, wbmp, jpeg)
     *
     * @throws ImageFunctionUnknownException
     *
     * @return string
     */
    public function get($format = null)
    {
        if ( ! is_null($format) )
            $this->setFormat($format);

        ob_start();
        call_user_func($this->getFunctionName(), $this->getImage());

        return ob_get_clean();
    }

    /**
     * Return the data URI
     *
     * @return string
     */
    public function getDataUri()
    {
        return 'data:image/' . $this->getFormat() . ';base64,' . base64_encode($this->get());
    }

    /**
     * Render the QR Code then save it to given file name or
     * output it to the browser when file name omitted.
     *
     * @param  null|string $format - Format of the file (png, jpeg, jpg, gif, wbmp)
     *
     * @throws ImageFunctionUnknownException
     *
     * @return QrCode
     */
    public function render($format = null)
    {
        if ( ! is_null($format) )
            $this->setFormat($format);

        $this->setHeaderResponse();
        call_user_func($this->getFunctionName(), $this->getImage());
    }

    /**
     * Render the QR Code then save it to given file name
     *
     * @param string $filename
     *
     * @throws ImageFunctionUnknownException
     *
     * @return string
     */
    public function save($filename)
    {
        $this->setFilename($filename);

        call_user_func_array($this->getFunctionName(), [$this->getImage(), $filename]);

        return $filename;
    }

    /**
     * Generate img Tag with the qr-code
     *
     * @param string|null   $alt
     * @param array|null    $attributes
     *
     * @return string
     */
    public function image($alt = "", $attributes = [])
    {
        return img_tag($this->getDataUri(), $alt, $attributes);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Return the image resource
     *
     * @return resource
     */
    private function getImage()
    {
        return $this->builder->getImage();
    }

    /**
     * Get the Function name
     * @return string
     */
    private function getFunctionName()
    {
        return $this->builder->getFunctionName();
    }

    private function setFilename($filename)
    {
        $this->builder->setFilename($filename);
    }
}
