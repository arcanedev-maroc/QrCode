<?php namespace Arcanedev\QrCode\Entities\Contracts;

use Arcanedev\QrCode\Entities\Exceptions\InvalidFileExtensionException;
use Arcanedev\QrCode\Entities\Exceptions\InvalidImageTypeException;

interface ImageTypeInterface
{
    /**
     * @return string
     */
    public function get();

    /**
     * @param string $type
     *
     * @throws InvalidImageTypeException
     *
     * @return ImageTypeInterface
     */
    public function set($type);

    /**
     * Get the default image type
     *
     * @return string
     */
    public function getDefaultType();

    /**
     * Get available types
     *
     * @return array
     */
    public function getAllAvailable();

    /**
     * Get the extension of the selected type
     *
     * @return mixed
     */
    public function getExtension();

    /**
     * @param string $filename
     *
     * @throws InvalidFileExtensionException
     *
     * @return ImageTypeInterface
     */
    public function setTypeFromFilename($filename);

    /**
     * @param string $extension
     *
     * @throws InvalidFileExtensionException
     * @throws InvalidImageTypeException
     *
     * @return ImageTypeInterface
     */
    public function setTypeFromExtension($extension);
}