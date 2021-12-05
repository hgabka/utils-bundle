<?php

namespace Hgabka\UtilsBundle\Imagine\Filter;

use Imagine\Filter\FilterInterface;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class Fit implements FilterInterface
{
    public const MODE_OUTBOUND = 'outbound';
    public const MODE_INSET = 'inset';

    /** @var int|null */
    private $width;

    /** @var int|null */
    private $height;

    /** @var string */
    private $mode;

    /**
     * Fit constructor.
     *
     * @param int|null $width
     * @param int|null $height
     * @param string   $mode
     */
    public function __construct($width, $height, $mode = self::MODE_INSET)
    {
        $this->width = $width;
        $this->height = $height;
        $this->mode = $mode;
    }

    public function apply(ImageInterface $image)
    {
        $origWidth = $image->getSize()->getWidth();
        $origHeight = $image->getSize()->getHeight();

        $imgWidth = $this->width;
        $imgHeight = $this->height;
        $mode = $this->mode;

        if ($origWidth === $imgWidth && $imgHeight === $origHeight) {
            return $image;
        }

        if (null === $imgWidth || null === $imgHeight) {
            if (null === $imgHeight) {
                $imgHeight = (int) ceil(($imgWidth / $origWidth) * $origHeight);
            } elseif (null === $imgWidth) {
                $imgWidth = (int) ceil(($imgHeight / $origHeight) * $origWidth);
            }
        }

        if (self::MODE_OUTBOUND !== $mode) {
            if ($imgWidth / $origWidth < $imgHeight / $origHeight) {
                $newWidth = $imgWidth;
                $newHeight = ceil($origHeight * ($newWidth / $origWidth));
            } else {
                $newHeight = $imgHeight;
                $newWidth = ceil($origWidth * ($newHeight / $origHeight));
            }
        } else {
            if ($imgWidth / $origWidth > $imgHeight / $origHeight) {
                $newWidth = $imgWidth;
                $newHeight = ceil($origHeight * ($newWidth / $origWidth));
            } else {
                $newHeight = $imgHeight;
                $newWidth = ceil($origWidth * ($newHeight / $origHeight));
            }
        }

        return $image->resize(new Box($newWidth, $newHeight));
    }
}
