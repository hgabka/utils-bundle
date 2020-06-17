<?php

namespace Hgabka\UtilsBundle\Imagine\Filter;

use Imagine\Filter\FilterInterface;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;

class Paste implements FilterInterface
{
    /** @var null|int */
    private $width;

    /** @var null|int */
    private $height;

    /** @var string */
    private $color;

    /** @var string */
    private $position;

    /** @var int */
    private $transparency;

    /** @var ImagineInterface */
    private $imagine;

    /**
     * Paste constructor.
     *
     * @param $imagine
     * @param null|int $width
     * @param null|int $height
     * @param string   $position
     * @param string   $color
     * @param int      $transparency
     */
    public function __construct($imagine, $width, $height, $position = 'center', $color = '#fff', $transparency = 0)
    {
        $this->imagine = $imagine;
        $this->width = $width ?? 0;
        $this->height = $height ?? 0;
        $this->color = $color;
        $this->transparency = $transparency;
        $this->position = $position;
    }

    public function apply(ImageInterface $image)
    {
        $imageWidth = $image->getSize()->getWidth();
        $imageHeight = $image->getSize()->getHeight();

        if ($this->width < $imageWidth || $this->height < $imageHeight ||
            ($this->width === $imageWidth && $this->height === $imageHeight)
        ) {
            return $image;
        }

        $background = $image->palette()->color(
            $this->color,
            $this->transparency
        );

        $position = strtolower($this->position);

        if (false !== strstr($position, 'top')) {
            $top = 0;
        } elseif (false !== strstr($position, 'bottom')) {
            $top = $this->height - $imageHeight;
        } else {
            $top = (int) ceil(($this->height - $imageHeight) / 2);
        }

        if (false !== strstr($position, 'left')) {
            $left = 0;
        } elseif (false !== strstr($position, 'right')) {
            $left = $this->width - $imageWidth;
        } else {
            $left = (int) ceil(($this->width - $imageWidth) / 2);
        }

        $newSize = new Box($this->width, $this->height);

        $canvas = $this->imagine->create($newSize, $background);

        return $canvas->paste($image, new Point($left, $top));
    }
}
