<?php namespace Arcanedev\QrCode\Entities;

use Arcanedev\QrCode\Entities\Exceptions\ImageFunctionUnknownException;
use Arcanedev\QrCode\Entities\Exceptions\InvalidFileExtensionException;
use Arcanedev\QrCode\Entities\Exceptions\InvalidImageTypeException;

class ImageType implements Contracts\ImageTypeInterface
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    public $selected;

    /** @const string Image type png */
    const IMAGE_TYPE_PNG        = 'png';

    /** @const string Image type gif */
    const IMAGE_TYPE_GIF        = 'gif';

    /** @const string Image type jpeg */
    const IMAGE_TYPE_JPEG       = 'jpeg';

    /** @const string Image type wbmp */
    const IMAGE_TYPE_WBMP       = 'wbmp';

    /** @var array */
    protected $availableTypes   = [
        self::IMAGE_TYPE_PNG,
        self::IMAGE_TYPE_GIF,
        self::IMAGE_TYPE_JPEG,
        self::IMAGE_TYPE_WBMP,
    ];

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    function __construct($selected = '')
    {
        $this->setSelectedType($selected);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @return string
     */
    public function get()
    {
        return $this->selected;
    }

    /**
     * @param string $type
     *
     * @throws Exceptions\InvalidImageTypeException
     *
     * @return ImageType
     */
    public function set($type)
    {
        $this->setSelectedType($type);

        return $this;
    }

    /**
     * @param string $type
     *
     * @throws InvalidImageTypeException
     */
    private function setSelectedType($type)
    {
        $type = ( empty($type) || ! is_string($type) )
            ? $this->getDefaultType()
            : $type;

        if ( ! $this->isValidType($type) ) {
            throw new InvalidImageTypeException("The image type [{$type}] is invalid.");
        }

        $this->selected = $this->convert($type);
    }

    /**
     * Get the default image type
     *
     * @return string
     */
    public function getDefaultType()
    {
        return self::IMAGE_TYPE_PNG;
    }

    /**
     * Get available types
     *
     * @return array
     */
    public function getAllAvailable()
    {
        return $this->availableTypes;
    }

    /**
     * Get the extension of the selected type
     *
     * @return mixed
     */
    public function getExtension()
    {
        // TODO: Complete the getExtension function implementation
        return $this->convert($this->selected, true);
    }

    /**
     * @param string $filename
     *
     * @throws InvalidFileExtensionException
     *
     * @return ImageType
     */
    public function setTypeFromFilename($filename)
    {
        $extension = $this->getExtensionFromFilename($filename);

        if ( empty($extension) ) {
            throw new InvalidFileExtensionException("You must specify an image extension.");
        }

        return $this->setTypeFromExtension($extension);
    }

    /**
     * @param string $extension
     *
     * @throws InvalidFileExtensionException
     * @throws InvalidImageTypeException
     *
     * @return ImageType
     */
    public function setTypeFromExtension($extension)
    {
        if ( ! $this->checkExtension($extension) ) {
            throw new InvalidFileExtensionException("Invalid File Extension [{$extension}].");
        }

        $this->setSelectedType($extension);

        return $this;
    }

    /**
     * Get the function name to create the image
     *
     * @throws ImageFunctionUnknownException
     *
     * @return string
     */
    public function getFunctionName()
    {
        $functionName = 'image' . $this->get();

        if ( ! function_exists($functionName) ) {
            throw new ImageFunctionUnknownException('QRCode: function ' . $functionName . ' does not exists.');
        }

        return $functionName;
    }

    public function getHeaderAttributes()
    {
        return 'Content-Type: image/' . $this->convert($this->get());
    }

    /* ------------------------------------------------------------------------------------------------
     |  Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Extract extension from the filename
     *
     * @param string $filename
     *
     * @return string
     */
    private function getExtensionFromFilename($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        return $extension;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Conversion Function
     | ------------------------------------------------------------------------------------------------
     */
    private function convert($type, $reverse = false)
    {
        $replace = array_merge(
            $this->getJpgReplace(),
            $this->getBmpReplace()
        );

        if ( $reverse ) {
            $replace = array_flip($replace);
        }

        return str_replace(array_keys($replace), array_values($replace), $type);
    }

    private function getJpgReplace()
    {
        return ['jpg' => 'jpeg'];
    }

    private function getBmpReplace()
    {
        // TODO: Verify the correct WBMP Format replacement
        return ['wbmp' => 'wbmp'];
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param string $extension
     *
     * @return bool
     */
    private function checkExtension($extension)
    {
        return $this->isValidType($extension);
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    // TODO: Complete the checkType function implementation
    private function isValidType($type)
    {
        return $this->isInAvailableImageTypes($type);
    }

    /**
     * @param $type
     *
     * @return bool
     */
    public function isAvailable($type)
    {
        return $this->isInAvailableImageTypes($type);
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function isInAvailableImageTypes($type)
    {
        return in_array($this->convert($type), $this->getAllAvailable());
    }
}
