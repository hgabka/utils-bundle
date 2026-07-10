<?php

namespace Hgabka\UtilsBundle\Helper;

use Symfony\Component\HttpFoundation\Request;

/**
 * Replacement for the Request::get() shorthand removed in Symfony 8.
 *
 * Mirrors its exact lookup order: attributes, then query, then request (POST) body.
 */
class RequestHelper
{
    public static function get(Request $request, string $key, mixed $default = null): mixed
    {
        if ($request->attributes->has($key)) {
            return $request->attributes->get($key);
        }

        // InputBag::get() rejects array values (e.g. "foo[]" fields), so index into
        // the raw parameter map instead of using its type-restricted get()/has().
        if ($request->query->has($key)) {
            return $request->query->all()[$key];
        }

        if ($request->request->has($key)) {
            return $request->request->all()[$key];
        }

        return $default;
    }
}
