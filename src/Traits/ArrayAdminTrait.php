<?php

namespace Hgabka\UtilsBundle\Traits;

trait ArrayAdminTrait
{
    public function getTemplate($name)
    {
        if ('inner_list_row' === $name) {
            return '@HgabkaUtils/Admin/array_list_inner_row.html.twig';
        }

        if ('base_list_field' === $name) {
            return '@HgabkaUtils/Admin/array_list_field.html.twig';
        }

        return parent::getTemplate($name);
    }

    public function getUrlsafeIdentifier($entity)
    {
        return \is_array($entity) ? $entity['id'] : parent::getUrlsafeIdentifier($entity);
    }
}
