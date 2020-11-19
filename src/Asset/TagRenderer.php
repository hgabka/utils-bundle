<?php

namespace Hgabka\UtilsBundle\Asset;

use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollection;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Symfony\WebpackEncoreBundle\Asset\IntegrityDataProviderInterface;
use Symfony\WebpackEncoreBundle\Asset\TagRenderer as BaseTagRenderer;

class TagRenderer extends BaseTagRenderer
{
    private $entrypointLookupCollection;
    private $defaultAttributes;
    private $renderedFiles = [];
    private $packages;

    public function __construct(
        $entrypointLookupCollection,
        Packages $packages,
        array $defaultAttributes = []
    ) {
        if ($entrypointLookupCollection instanceof EntrypointLookupInterface) {
            @trigger_error(sprintf('The "$entrypointLookupCollection" argument in method "%s()" must be an instance of EntrypointLookupCollection.', __METHOD__), E_USER_DEPRECATED);

            $this->entrypointLookupCollection = new EntrypointLookupCollection(
                new ServiceLocator(['_default' => function () use ($entrypointLookupCollection) {
                    return $entrypointLookupCollection;
                }])
            );
        } elseif ($entrypointLookupCollection instanceof EntrypointLookupCollection) {
            $this->entrypointLookupCollection = $entrypointLookupCollection;
        } else {
            throw new \TypeError('The "$entrypointLookupCollection" argument must be an instance of EntrypointLookupCollection.');
        }

        $this->packages = $packages;
        $this->defaultAttributes = $defaultAttributes;

        $this->reset();
    }

    public function renderWebpackLinkTags(string $entryName, string $packageName = null, string $entrypointName = '_default'): string
    {
        $scriptTags = [];
        $entryPointLookup = $this->getEntrypointLookup($entrypointName);
        $integrityHashes = ($entryPointLookup instanceof IntegrityDataProviderInterface) ? $entryPointLookup->getIntegrityData() : [];

        foreach ($entryPointLookup->getCssFiles($entryName) as $fileKey => $filename) {
            $path = $this->getAssetPath($filename, $packageName);
            $attributes = $this->defaultAttributes;
            $attributes['rel'] = 'stylesheet';
            $attributes['href'] = $path;

            if (isset($integrityHashes[$filename])) {
                $attributes['integrity'] = $integrityHashes[$filename];
            }
            $oldAttributes = $attributes;

            $attributes['rel'] = 'preload';
            $attributes['as'] = 'style';

            $scriptTags[] = sprintf(
                '<link %s>',
                $this->convertArrayToAttributes($attributes)
            );

            $scriptTags[] = sprintf(
                '<link %s>',
                $this->convertArrayToAttributes($oldAttributes)
            );

            $this->renderedFiles['styles'][] = $attributes['href'];
        }

        return implode('', $scriptTags);
    }

    private function getEntrypointLookup(string $buildName): EntrypointLookupInterface
    {
        return $this->entrypointLookupCollection->getEntrypointLookup($buildName);
    }

    private function getAssetPath(string $assetPath, string $packageName = null): string
    {
        if (null === $this->packages) {
            throw new \Exception('To render the script or link tags, run "composer require symfony/asset".');
        }

        return $this->packages->getUrl(
            $assetPath,
            $packageName
        );
    }

    private function convertArrayToAttributes(array $attributesMap): string
    {
        return implode(' ', array_map(
            function ($key, $value) {
                return sprintf('%s="%s"', $key, htmlentities($value));
            },
            array_keys($attributesMap),
            $attributesMap
        ));
    }
}
