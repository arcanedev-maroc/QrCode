<?php namespace Arcanedev\QrCode;

use Arcanedev\QrCode\Exceptions\ImageFunctionUnknownException;

// TODO: Refactor it
class QrCode implements Contracts\QrCodeInterface
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var Builder */
    protected $builder;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    public function __construct()
    {
        $this->builder  = new Builder;

        $this->initPaths();
    }

    protected function initPaths()
    {
        $this->setPath(qr_code_assets_path('data'));
        $this->setImagePath(qr_code_assets_path('image'));
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Return path to the data directory
     *
     * @return string
     */
    public function getPath()
    {
        return $this->builder->getPath();
    }

    /**
     * Set path to the data directory
     *
     * @param  string $path Data directory
     * @return QrCode
     */
    public function setPath($path)
    {
        $this->builder->setPath($path);

        return $this;
    }

    /**
     * Return path to the images directory
     *
     * @return string
     */
    public function getImagePath()
    {
        return $this->builder->getImagePath();
    }

    /**
     * Set path to the images directory
     *
     * @param  string $image_path Image directory
     * @return QrCode
     */
    public function setImagePath($image_path)
    {
        $this->builder->setImagePath($image_path);

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
    public function setStructureAppend($n, $m, $parity, $original_data)
    {
        $this->builder->setStructureAppend($n, $m, $parity, $original_data);

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
     * Set QR Code error correction level
     *
     * @param  int    $error_correction Error Correction Level
     * @return QrCode
     */
    public function setErrorCorrection($error_correction)
    {
        $this->builder->setErrorCorrection($error_correction);

        return $this;
    }

    /**
     * Return QR Code error correction level
     *
     * @return int
     */
    public function getErrorCorrection()
    {
        return $this->builder->getErrorCorrection();
    }

    /**
     * Return QR Code module size
     *
     * @return int
     */
    public function getModuleSize()
    {
        return $this->builder->getModuleSize();
    }

    /**
     * Set QR Code module size
     *
     * @param  int    $module_size Module size
     * @return QrCode
     */
    public function setModuleSize($module_size)
    {
        $this->builder->setModuleSize($module_size);

        return $this;
    }

    /**
     * Set image type for rendering
     *
     * @param  string $image_type Image type
     * @return QrCode
     */
    public function setImageType($image_type)
    {
        $this->builder->setImageType($image_type);

        return $this;
    }

    /**
     * Return image type for rendering
     *
     * @return string
     */
    public function getImageType()
    {
        return $this->builder->getImageType();
    }

    /**
     * Set image type for rendering via extension
     *
     * @param  string $extension Image extension
     * @return QrCode
     */
    public function setExtension($extension)
    {
        $this->builder->setExtension($extension);

        return $this;
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
     * Return QR Code size (width)
     *
     * @return int
     */
    public function getSize()
    {
        return $this->builder->getSize();
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
     * @return array
     */
    public function getForegroundColor()
    {
        return $this->builder->getForegroundColor();
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
     * @return array
     */
    public function getBackgroundColor()
    {
        return $this->builder->getBackgroundColor();
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

    protected function getAvailableImageTypes()
    {
        return $this->builder->getAvailableImageTypes();
    }

    /**
     * Return the image resource
     *
     * @return resource
     */
    public function getImage()
    {
        if ( ! $this->hasImage() ) {
            $this->create();
        }

        return $this->builder->getImage();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Return the data URI
     *
     * @return string
     */
    public function getDataUri()
    {
        ob_start();
        call_user_func('image' . $this->getImageType(), $this->getImage());
        $contents = ob_get_clean();

        return 'data:image/' . $this->getImageType() . ';base64,' . base64_encode($contents);
    }

    /**
     * Render the QR Code then save it to given file name
     *
     * @param string $filename  - File name of the QR Code
     * @param string $extension    - Format of the file (png, jpeg, jpg, gif, wbmp)
     *
     * @throws ImageFunctionUnknownException
     *
     * @return QrCode
     */
    public function save($filename, $extension)
    {
        $extension = str_replace('jpg', 'jpeg', $extension);

        if ( ! in_array($extension, $this->getAvailableImageTypes()) ) {
            $extension = $this->getImageType();
        }

        if ( ! function_exists('image' . $extension) ) {
            throw new ImageFunctionUnknownException('QRCode: function image' . $extension . ' does not exists.');
        }

        // TODO: Add a function to create an new directory
        call_user_func_array('image' . $extension, [
            $this->getImage(),
            $filename . '.' . $extension
        ]);

        return $this;
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
    public function render($format = 'png')
    {
        $format = str_replace('jpg', 'jpeg', $format);

        if ( ! in_array($format, $this->getAvailableImageTypes()) ) {
            $format = $this->getImageType();
        }

        if ( ! function_exists('image' . $format) ) {
            throw new ImageFunctionUnknownException('QRCode: function image' . $format . ' does not exists.');
        }

        call_user_func('image' . $format, $this->getImage());

        return $this;
    }

    /**
     * Create QR Code and return its content
     *
     * @param  string|null                   $format Image type (gif, png, wbmp, jpeg)
     *
     * @throws ImageFunctionUnknownException
     *
     * @return string
     */
    public function get($format = null)
    {
        $this->create();

        if ($format == 'jpg') {
            $format = 'jpeg';
        }

        if ( ! $this->isInAvailableImageTypes($format) ) {
            $format = $this->getImageType();
        }

        if ( ! function_exists("image{$format}") ) {
            throw new ImageFunctionUnknownException("QRCode: function image{$format} does not exists.");
        }

        ob_start();
        call_user_func('image' . $format, $this->getImage());

        return ob_get_clean();
    }

    public function create()
    {
        $this->image = $this->builder->create();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Check if image is not empty
     *
     * @return bool
     */
    public function hasImage()
    {
        return $this->builder->hasImage();
    }

    /**
     * @param $image_type
     *
     * @return bool
     */
    protected function isInAvailableImageTypes($image_type)
    {
        return $this->builder->isInAvailableImageTypes($image_type);
    }
}
