<?php

namespace Hgabka\UtilsBundle\Form\Transformer;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ObjectAutocompleteItemViewTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $callback;

    public function __construct(ObjectRepository $repository, $callback)
    {
        $this->repository = $repository;
        $this->callback = $callback;
    }

    /**
     * Transforms a string into an array.
     *
     * @param mixed $value
     *
     * @throws TransformationFailedException
     *
     * @return mixed An array of entities
     */
    public function transform($value)
    {
        if (!empty($value)) {
            if (is_string($value)) {
                $obj = $this->repository->find($value);
                if (!$obj) {
                    return;
                }
                $label = null === $this->callback ? (string) $obj : $obj->{$this->callback}();

                return ['id' => $value, 'label' => $label];
            }
        }

        return $value;
    }

    /**
     * Transforms choice keys into entities.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function reverseTransform($value)
    {
        if (is_array($value)) {
            return $value['id'];
        }

        return $value;
    }
}
