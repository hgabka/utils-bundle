<?php

namespace Hgabka\UtilsBundle\Imagine\Filter;

use Imagine\Filter\FilterInterface;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;

class Fill implements FilterInterface
{
    /** @var int|null */
    private $width;

    /** @var int|null */
    private $height;

    private $position;

    /**
     * Fill constructor.
     *
     * @param int|null $width
     * @param int|null $height
     * @param $position
     */
    public function __construct($width, $height, $position)
    {
        $this->width = $width;
        $this->height = $height;
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        $width = $this->width;
        $height = $this->height;

        $filter = new Fit($width, $height, Fit::MODE_OUTBOUND);
        $image = $filter->apply($image);

        $newWidth = $image->getSize()->getWidth();
        $newHeight = $image->getSize()->getHeight();
        if ($newWidth === $width && $newHeight === $height) {
            return $image;
        }

        $position = $this->position;

        if (false !== strstr($position, 'top')) {
            $top = 0;
        } elseif (false !== strstr($position, 'bottom')) {
            $top = $newHeight - $height;
        } else {
            $top = (int) ceil(($newHeight - $height) / 2);
        }

        if (false !== strstr($position, 'left')) {
            $left = 0;
        } elseif (false !== strstr($position, 'right')) {
            $left = $newWidth - $width;
        } else {
            $left = (int) ceil(($newWidth - $width) / 2);
        }

        $image->crop(new Point($left, $top), new Box($width, $height));

        return $image;
    }
}
