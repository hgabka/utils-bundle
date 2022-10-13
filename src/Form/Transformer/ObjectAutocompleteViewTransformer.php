<?php

namespace Hgabka\UtilsBundle\Form\Transformer;

use App\Repository\ChipboardMatchingQualityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ObjectAutocompleteViewTransformer implements DataTransformerInterface
{
    public function __construct(protected EntityRepository $repository, protected ?string $callback) {}

    /**
     * Transforms a collection into an array.
     *
     * @param Collection $collection A collection of entities
     *
     * @throws TransformationFailedException
     *
     * @return mixed An array of entities
     */
    public function transform($collection)
    {
        $result = [
                'items' => [],
                'labels' => [],
        ];

        if (null === $collection) {
            return $result;
        }

        foreach ($collection as $entity) {
            $result['items'][] = [
                'label' => null === $this->callback
                    ? (string) $entity : $entity->{$this->callback}(),
                'id' => $entity->getId(),
            ];
        }

        return $result;
    }

    /**
     * Transforms choice keys into entities.
     *
     * @param mixed $value
     *
     * @return Collection A collection of entities
     */
    public function reverseTransform($value)
    {
        $collection = new ArrayCollection();

        if (empty($value) || empty($value['items'])) {
            return $collection;
        }

        foreach ($value['items'] as $data) {
            $collection->add($this->repository->find($data));
        }

        return $collection;
    }
}
