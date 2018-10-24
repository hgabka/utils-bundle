<?php

namespace Hgabka\UtilsBundle\Helper;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class HgabkaUtils
{
    /** @var ContainerInterface */
    protected $container;

    protected $roman_values = [
        'I' => 1,
        'V' => 5,
        'X' => 10,
        'L' => 50,
        'C' => 100,
        'D' => 500,
        'M' => 1000,
    ];
    //values that should evaluate as 0
    protected $roman_zero = ['N', 'nulla'];
    //Regex - checking for valid Roman numerals
    protected $roman_regex = '/^M{0,3}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})$/';

    /**
     * KumaUtils constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param null $baseLocale
     * @param bool $frontend
     *
     * @return null|string
     */
    public function getCurrentLocale($baseLocale = null)
    {
        $availableLocales = $this->getAvailableLocales();

        if (!empty($baseLocale) && \in_array($baseLocale, $availableLocales, true)) {
            return $baseLocale;
        }

        $requestStack = $this->container->get('request_stack');

        $request = $requestStack->getMasterRequest();

        $locale = $request ? $request->getLocale() : null;

        if (!empty($locale) && \in_array($locale, $availableLocales, true)) {
            return $locale;
        }

        return $baseLocale;
    }

    /**
     * @param bool $frontend
     *
     * @return array
     */
    public function getAvailableLocales(): array
    {
        return explode('|', $this->container->getParameter('requiredlocales'));
    }

    /**
     * @return string
     */
    public function getAdminLocale()
    {
        return $this->container->get('session')->get('hgabka_utils.admin_locale', $this->container->getParameter('hgabka_utils.default_admin_locale'));
    }

    /**
     * @param string $adminLocale
     */
    public function setAdminLocale($adminLocale)
    {
        if (in_array($adminLocale, $this->container->getParameter('hgabka_utils.admin_locales'))) {
            $this->container->get('session')->set('hgabka_utils.admin_locale', $adminLocale);
        }
    }

    /**
     * @param bool   $frontend
     * @param string $prefix
     *
     * @return array
     */
    public function getLocaleChoices($prefix = 'wt_kuma_extension.locales.'): array
    {
        $locales = $this->getAvailableLocales();

        return $this->prefixArrayElements($locales, $prefix);
    }

    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->container->getParameter('defaultlocale');
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    public function getMasterRequest()
    {
        $requestStack = $this->container->get('request_stack');

        return $requestStack->getMasterRequest();
    }

    /**
     * @return RequestStack
     */
    public function getRequestStack(): RequestStack
    {
        return $this->container->get('request_stack');
    }

    /**
     * @return string
     */
    public function getProjectDir(): string
    {
        return $this->container->getParameter('kernel.project_dir');
    }

    /**
     * @return string
     */
    public function getWebDir(): string
    {
        return $this->container->getParameter('kernel.project_dir').'/web';
    }

    public function slugify($text, $default = '', $replace = ["'"], $delimiter = '-')
    {
        $slugifier = new Slugifier();

        return $slugifier->slugify($text, $default, $replace, $delimiter);
    }

    public function entityToArray($entity, $maxLevel = 2, $currentLevel = 0)
    {
        if ($currentLevel > $maxLevel || empty($entity)) {
            return [];
        }
        $doctrine = $this->container->get('doctrine');

        /** @var EntityManager $em */
        $em = $doctrine->getManager();
        $md = $em->getClassMetadata(\get_class($entity));

        $result = [];
        if ($md) {
            foreach ($md->getFieldNames() as $field) {
                $result[$field] = $md->getFieldValue($entity, $field);
            }
            if ($currentLevel < $maxLevel) {
                foreach ($md->getAssociationMappings() as $field => $data) {
                    $mapping = $md->getFieldValue($entity, $field);
                    if ($mapping instanceof \Traversable) {
                        $result[$field] = [];
                        foreach ($mapping as $ent) {
                            $result[$field][] = $this->entityToArray($ent, $maxLevel, $currentLevel + 1);
                        }
                    } else {
                        $result[$field] = $this->entityToArray($mapping, $maxLevel, $currentLevel + 1);
                    }
                }
            }
        }

        return $result;
    }

    public function entityFromArray($entity, array $array)
    {
        foreach ($array as $key => $value) {
            $method = 'set'.ucfirst($key);
            if (method_exists($entity, $method)) {
                $entity->$method($value);
            }
        }

        return $entity;
    }

    /**
     * Returns subject replaced with regular expression matchs.
     *
     * @param mixed $search       subject to search
     * @param array $replacePairs array of search => replace pairs
     *
     * @return mixed
     */
    public function pregtr($search, $replacePairs)
    {
        foreach ($replacePairs as $pattern => $replacement) {
            if (preg_match('/(.*)e$/', $pattern, $matches)) {
                $pattern = $matches[1];
                $search = preg_replace_callback($pattern, function ($matches) use ($replacement) {
                    preg_match("/('::'\.)?([a-z]*)\('\\\\([0-9]{1})'\)/", $replacement, $match);

                    return ('' === $match[1] ? '' : '::').\call_user_func($match[2], $matches[$match[3]]);
                }, $search);
            } else {
                $search = preg_replace($pattern, $replacement, $search);
            }
        }

        return $search;
    }

    /**
     * Returns a camelized string from a lower case and underscored string by replaceing slash with
     * double-colon and upper-casing each letter preceded by an underscore.
     *
     * @param string $lower_case_and_underscored_word string to camelize
     *
     * @return string camelized string
     */
    public function camelize($lower_case_and_underscored_word)
    {
        return $this->pregtr($lower_case_and_underscored_word, ['#/(.?)#e' => "'::'.strtoupper('\\1')", '/(^|_|-)+(.)/e' => "strtoupper('\\2')"]);
    }

    /**
     * Returns an underscore-syntaxed version or the CamelCased string.
     *
     * @param string $camel_cased_word string to underscore
     *
     * @return string underscored string
     */
    public function underscore($camel_cased_word)
    {
        $tmp = $camel_cased_word;
        $tmp = str_replace('::', '/', $tmp);
        $tmp = $this->pregtr($tmp, [
            '/([A-Z]+)([A-Z][a-z])/' => '\\1_\\2',
            '/([a-z\d])([A-Z])/' => '\\1_\\2',
        ]);

        return strtolower($tmp);
    }

    /**
     * Returns classname::module with classname:: stripped off.
     *
     * @param string $class_name_in_module classname and module pair
     *
     * @return string module name
     */
    public function demodulize($class_name_in_module)
    {
        return preg_replace('/^.*::/', '', $class_name_in_module);
    }

    /**
     * Returns classname in underscored form, with "_id" tacked on at the end.
     * This is for use in dealing with foreign keys in the database.
     *
     * @param string $class_name               class name
     * @param bool   $separate_with_underscore separate with underscore
     *
     * @return strong Foreign key
     */
    public function foreign_key($class_name, $separate_with_underscore = true)
    {
        return $this->underscore($this->demodulize($class_name)).($separate_with_underscore ? '_id' : 'id');
    }

    /**
     * Returns corresponding table name for given classname.
     *
     * @param string $class_name name of class to get database table name for
     *
     * @return string name of the databse table for given class
     */
    public function tableize($class_name)
    {
        return $this->underscore($class_name);
    }

    /**
     * Returns model class name for given database table.
     *
     * @param string $table_name table name
     *
     * @return string classified table name
     */
    public function classify($table_name)
    {
        return $this->camelize($table_name);
    }

    /**
     * Returns a human-readable string from a lower case and underscored word by replacing underscores
     * with a space, and by upper-casing the initial characters.
     *
     * @param string $lower_case_and_underscored_word string to make more readable
     *
     * @return string human-readable string
     */
    public function humanize($lower_case_and_underscored_word)
    {
        if ('_id' === substr($lower_case_and_underscored_word, -3)) {
            $lower_case_and_underscored_word = substr($lower_case_and_underscored_word, 0, -3);
        }

        return ucfirst(str_replace('_', ' ', $lower_case_and_underscored_word));
    }

    /**
     * Adds a path to the PHP include_path setting.
     *
     * @param mixed  $path     Single string path or an array of paths
     * @param string $position Either 'front' or 'back'
     *
     * @return string The old include path
     */
    public function addIncludePath($path, $position = 'front')
    {
        if (\is_array($path)) {
            foreach ('front' === $position ? array_reverse($path) : $path as $p) {
                $this->addIncludePath($p, $position);
            }

            return;
        }

        $paths = explode(PATH_SEPARATOR, get_include_path());

        // remove what's already in the include_path
        if (false !== $key = array_search(realpath($path), array_map('realpath', $paths), true)) {
            unset($paths[$key]);
        }

        switch ($position) {
            case 'front':
                array_unshift($paths, $path);

                break;
            case 'back':
                $paths[] = $path;

                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unrecognized position: "%s"', $position));
        }

        return set_include_path(implode(PATH_SEPARATOR, $paths));
    }

    /**
     * Converts UTF-8 strings to a different encoding. NB. The result may not have been encoded if iconv fails.
     *
     * This file comes from Prado (BSD License)
     *
     * @param string $string the UTF-8 string for conversion
     * @param string $to     new encoding
     *
     * @return string encoded string
     */
    public function I18N_toEncoding($string, $to)
    {
        $to = strtoupper($to);
        if ('UTF-8' !== $to) {
            $s = iconv('UTF-8', $to, $string);

            return false !== $s ? $s : $string;
        }

        return $string;
    }

    /**
     * Converts strings to UTF-8 via iconv. NB, the result may not by UTF-8 if the conversion failed.
     *
     * This file comes from Prado (BSD License)
     *
     * @param string $string string to convert to UTF-8
     * @param string $from   current encoding
     *
     * @return string UTF-8 encoded string, original string if iconv failed
     */
    public function I18N_toUTF8($string, $from)
    {
        $from = strtoupper($from);
        if ('UTF-8' !== $from) {
            $s = iconv($from, 'UTF-8', $string);  // to UTF-8

            return false !== $s ? $s : $string; // it could return false
        }

        return $string;
    }

    /**
     * Get path to php cli.
     *
     * @throws sfException If no php cli found
     *
     * @return string
     */
    public function getPhpCli()
    {
        $path = getenv('PATH') ? getenv('PATH') : getenv('Path');
        $suffixes = \DIRECTORY_SEPARATOR === '\\' ? (getenv('PATHEXT') ? explode(PATH_SEPARATOR, getenv('PATHEXT')) : ['.exe', '.bat', '.cmd', '.com']) : [''];
        foreach (['php5', 'php'] as $phpCli) {
            foreach ($suffixes as $suffix) {
                foreach (explode(PATH_SEPARATOR, $path) as $dir) {
                    if (is_file($file = $dir.\DIRECTORY_SEPARATOR.$phpCli.$suffix) && is_executable($file)) {
                        return $file;
                    }
                }
            }
        }

        throw new \Exception('Unable to find PHP executable.');
    }

    /**
     * Returns an array value for a path.
     *
     * @param array  $values  The values to search
     * @param string $name    The token name
     * @param array  $default Default if not found
     *
     * @return array
     */
    public function getArrayValueForPath($values, $name, $default = null)
    {
        if (false === $offset = strpos($name, '[')) {
            return isset($values[$name]) ? $values[$name] : $default;
        }

        if (!isset($values[substr($name, 0, $offset)])) {
            return $default;
        }

        $array = $values[substr($name, 0, $offset)];

        while (false !== $pos = strpos($name, '[', $offset)) {
            $end = strpos($name, ']', $pos);
            if ($end === $pos + 1) {
                // reached a []
                if (!\is_array($array)) {
                    return $default;
                }

                break;
            } elseif (!isset($array[substr($name, $pos + 1, $end - $pos - 1)])) {
                return $default;
            } elseif (\is_array($array)) {
                $array = $array[substr($name, $pos + 1, $end - $pos - 1)];
                $offset = $end;
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * Checks if a string is an utf8.
     *
     * Yi Stone Li<yili@yahoo-inc.com>
     * Copyright (c) 2007 Yahoo! Inc. All rights reserved.
     * Licensed under the BSD open source license
     *
     * @param string
     * @param mixed $string
     *
     * @return bool true if $string is valid UTF-8 and false otherwise
     */
    public function isUTF8($string)
    {
        for ($idx = 0, $strlen = \strlen($string); $idx < $strlen; ++$idx) {
            $byte = \ord($string[$idx]);

            if ($byte & 0x80) {
                if (0xC0 === ($byte & 0xE0)) {
                    // 2 byte char
                    $bytes_remaining = 1;
                } elseif (0xE0 === ($byte & 0xF0)) {
                    // 3 byte char
                    $bytes_remaining = 2;
                } elseif (0xF0 === ($byte & 0xF8)) {
                    // 4 byte char
                    $bytes_remaining = 3;
                } else {
                    return false;
                }

                if ($idx + $bytes_remaining >= $strlen) {
                    return false;
                }

                while ($bytes_remaining--) {
                    if (0x80 !== (\ord($string[++$idx]) & 0xC0)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Checks if array values are empty.
     *
     * @param array $array the array to check
     *
     * @return bool true if empty, otherwise false
     */
    public function isArrayValuesEmpty($array)
    {
        static $isEmpty = true;
        foreach ($array as $value) {
            $isEmpty = (\is_array($value)) ? $this->isArrayValuesEmpty($value) : (0 === \strlen($value));
            if (!$isEmpty) {
                break;
            }
        }

        return $isEmpty;
    }

    // code from php at moechofe dot com (array_merge comment on php.net)
    /*
     * array arrayDeepMerge ( array array1 [, array array2 [, array ...]] )
     *
     * Like array_merge
     *
     *  arrayDeepMerge() merges the elements of one or more arrays together so
     * that the values of one are appended to the end of the previous one. It
     * returns the resulting array.
     *  If the input arrays have the same string keys, then the later value for
     * that key will overwrite the previous one. If, however, the arrays contain
     * numeric keys, the later value will not overwrite the original value, but
     * will be appended.
     *  If only one array is given and the array is numerically indexed, the keys
     * get reindexed in a continuous way.
     *
     * Different from array_merge
     *  If string keys have arrays for values, these arrays will merge recursively.
     */
    public function arrayDeepMerge()
    {
        switch (\func_num_args()) {
            case 0:
                return false;
            case 1:
                return func_get_arg(0);
            case 2:
                $args = \func_get_args();
                $args[2] = [];
                if (\is_array($args[0]) && \is_array($args[1])) {
                    foreach (array_unique(array_merge(array_keys($args[0]), array_keys($args[1]))) as $key) {
                        $isKey0 = array_key_exists($key, $args[0]);
                        $isKey1 = array_key_exists($key, $args[1]);
                        if ($isKey0 && $isKey1 && \is_array($args[0][$key]) && \is_array($args[1][$key])) {
                            $args[2][$key] = $this->arrayDeepMerge($args[0][$key], $args[1][$key]);
                        } elseif ($isKey0 && $isKey1) {
                            $args[2][$key] = $args[1][$key];
                        } elseif (!$isKey1) {
                            $args[2][$key] = $args[0][$key];
                        } elseif (!$isKey0) {
                            $args[2][$key] = $args[1][$key];
                        }
                    }

                    return $args[2];
                }

                return $args[1];
            default:
                $args = \func_get_args();
                $args[1] = $this->arrayDeepMerge($args[0], $args[1]);
                array_shift($args);

                return \call_user_func_array([$this, 'arrayDeepMerge'], $args);

                break;
        }
    }

    /**
     * Strip slashes recursively from array.
     *
     * @param array $value the value to strip
     *
     * @return array clean value with slashes stripped
     */
    public function stripslashesDeep($value)
    {
        return \is_array($value) ? array_map([$this, 'stripslashesDeep'], $value) : stripslashes($value);
    }

    /**
     * Strips comments from php source code.
     *
     * @param string $source PHP source code
     *
     * @return string comment free source code
     */
    public function stripComments($source)
    {
        if (!\function_exists('token_get_all')) {
            return $source;
        }

        $ignore = [T_COMMENT => true, T_DOC_COMMENT => true];
        $output = '';

        foreach (token_get_all($source) as $token) {
            // array
            if (isset($token[1])) {
                // no action on comments
                if (!isset($ignore[$token[0]])) {
                    // anything else -> output "as is"
                    $output .= $token[1];
                }
            } else {
                // simple 1-character token
                $output .= $token;
            }
        }

        return $output;
    }

    /**
     * Determine if a filesystem path is absolute.
     *
     * @param path $path a filesystem path
     *
     * @return bool true, if the path is absolute, otherwise false
     */
    public function isPathAbsolute($path)
    {
        if ('/' === $path[0] || '\\' === $path[0] ||
            (
                \strlen($path) > 3 && ctype_alpha($path[0]) &&
                ':' === $path[1] &&
                ('\\' === $path[2] || '/' === $path[2])
            )
        ) {
            return true;
        }

        return false;
    }

    /**
     * Clear all files and directories corresponding to a glob pattern.
     *
     * @param string $pattern an absolute filesystem pattern
     */
    public function clearGlob($pattern)
    {
        if (false === $files = glob($pattern)) {
            return;
        }

        // order is important when removing directories
        sort($files);

        foreach ($files as $file) {
            if (is_dir($file)) {
                // delete directory
                $this->clearDirectory($file);
            } else {
                // delete file
                unlink($file);
            }
        }
    }

    /**
     * Clear all files in a given directory.
     *
     * @param string $directory an absolute filesystem path to a directory
     */
    public function clearDirectory($directory)
    {
        if (!is_dir($directory)) {
            return;
        }

        // open a file point to the cache dir
        $fp = opendir($directory);

        // ignore names
        $ignore = ['.', '..', 'CVS', '.svn'];

        while (false !== ($file = readdir($fp))) {
            if (!\in_array($file, $ignore, true)) {
                if (is_link($directory.'/'.$file)) {
                    // delete symlink
                    unlink($directory.'/'.$file);
                } elseif (is_dir($directory.'/'.$file)) {
                    // recurse through directory
                    $this->clearDirectory($directory.'/'.$file);

                    // delete the directory
                    rmdir($directory.'/'.$file);
                } else {
                    // delete the file
                    unlink($directory.'/'.$file);
                }
            }
        }

        // close file pointer
        closedir($fp);
    }

    /**
     * Extract the class or interface name from filename.
     *
     * @param string $filename a filename
     *
     * @return string a class or interface name, if one can be extracted, otherwise null
     */
    public function extractClassName($filename)
    {
        $retval = null;

        if ($this->isPathAbsolute($filename)) {
            $filename = basename($filename);
        }

        $pattern = '/(.*?)\.(class|interface)\.php/i';

        if (preg_match($pattern, $filename, $match)) {
            $retval = $match[1];
        }

        return $retval;
    }

    public function isRomanNumber($roman)
    {
        return preg_match($this->roman_regex, $roman) > 0;
    }

    //Conversion: Roman Numeral to Integer
    public function Roman2Int($roman)
    {
        //checking for zero values
        if (\in_array($roman, $this->roman_zero, true)) {
            return 0;
        }

        //validating string
        if (!$this->isRomanNumber($roman)) {
            return false;
        }

        $values = $this->roman_values;
        $result = 0;
        //iterating through characters LTR
        for ($i = 0, $length = \strlen($roman); $i < $length; ++$i) {
            //getting value of current char
            $value = $values[$roman[$i]];
            //getting value of next char - null if there is no next char
            $nextvalue = !isset($roman[$i + 1]) ? null : $values[$roman[$i + 1]];
            //adding/subtracting value from result based on $nextvalue
            $result += (null !== $nextvalue && $nextvalue > $value) ? -$value : $value;
        }

        return $result;
    }

    public static function Int2Roman($integer)
    {
        $table = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
        $return = '';
        while ($integer > 0) {
            foreach ($table as $rom => $arb) {
                if ($integer >= $arb) {
                    $integer -= $arb;
                    $return .= $rom;

                    break;
                }
            }
        }

        return $return;
    }

    public function isSlug($slug)
    {
        return preg_match('|^[a-zA-Z0-9_-]+$|', $slug) && \strlen($slug) >= 3;
    }

    public function mbUcfirst($str, $encoding = 'UTF-8', $lower_str_end = false)
    {
        $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
        $str_end = '';
        if ($lower_str_end) {
            $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
        } else {
            $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
        }
        $str = $first_letter.$str_end;

        return $str;
    }

    public function stripNewlines($string)
    {
        if (!\is_string($string)) {
            return $string;
        }

        return str_replace(["\r", "\n"], [], $string);
    }

    public function num2text($nsz)
    {
        $hatv = [
            '',
            'ezer',
            'millió',
            'milliárd',
            'billió',
            'billiárd',
            'trillió',
            'trilliárd',
            'kvadrillió',
            'kvadrilliárd',
            'kvintillió',
            'kvintilliárd',
            'szextillió',
            'szextilliárd',
            'szeptillió',
            'szeptilliárd',
            'oktillió',
            'oktilliárd',
            'nonillió',
            'nonilliárd',
            'decillió',
            'decilliárd',
            'centillió',
        ];

        $tizesek = ['', '', 'harminc', 'negyven', 'ötven', 'hatvan', 'hetven', 'nyolcvan', 'kilencven'];
        $szamok = ['egy', 'kettő', 'három', 'négy', 'öt', 'hat', 'hét', 'nyolc', 'kilenc'];

        $tsz = '';
        $ej = ($nsz < 0 ? '- ' : '');
        $sz = trim(''.floor($nsz));
        $hj = 0;
        if ('0' === $sz) {
            $tsz = 'nulla';
        } else {
            while ($sz > '') {
                ++$hj;
                $t = '';
                $wsz = substr('00'.substr($sz, -3), -3);
                $tizesek[0] = ('0' === $wsz[2] ? 'tíz' : 'tizen');
                $tizesek[1] = ('0' === $wsz[2] ? 'húsz' : 'huszon');
                if ($c = $wsz[0]) {
                    $t = $szamok[$c - 1].'száz';
                }
                if ($c = $wsz[1]) {
                    $t .= $tizesek[$c - 1];
                }
                if ($c = $wsz[2]) {
                    $t .= $szamok[$c - 1];
                }
                //        $tsz=($t?$t.$hatv[$hj-1]:'').($tsz==''?'':'-').$tsz;
                $tsz = ($t ? $t.$hatv[$hj - 1] : '').('' === $tsz ? '' : ($nsz > 2000 ? '-' : '')).$tsz;
                $sz = substr($sz, 0, -3);
            }
        }

        return ucfirst($ej.$tsz);
    }

    public function getKozteruletJellegek()
    {
        return [
            'utca',
            'út',
            'útja',
            'tér',
            'tere',
            'körtér',
            'körút',
            'köz',
            'határút',
            'sétány',
            'körönd',
            'árok',
            'átjáró',
            'dűlősor',
            'dűlőút',
            'erdősor',
            'fasor',
            'forduló',
            'gát',
            'határsor',
            'híd',
            'kert',
            'lakótelep',
            'lejáró',
            'lejtő',
            'lépcső',
            'liget',
            'mélyút',
            'orom',
            'országút',
            'ösvény',
            'park',
            'part',
            'pincesor',
            'rakpart',
            'sétaút',
            'sor',
            'sugárút',
            'turistaút',
            'udvar',
            'üdülőpart',
        ];
    }

    public function removeAccents($text)
    {
        return str_replace(
            ['Á', 'É', 'Í', 'Ó', 'Ö', 'Ő', 'Ú', 'Ü', 'Ű', 'á', 'é', 'í', 'ó', 'ö', 'ő', 'ú', 'ü', 'ű'],
            ['A', 'E', 'I', 'O', 'O', 'O', 'U', 'U', 'U', 'a', 'e', 'i', 'o', 'o', 'o', 'u', 'u', 'u'],
            $text
        );
    }

    public function xmlEscape($string)
    {
        return str_replace(['&', '<', '>', '\'', '"'], ['&amp;', '&lt;', '&gt;', '&apos;', '&quot;'], $string);
    }

    /**
     *  Egy tömb permutációja.
     *
     * @param $items
     * @param array $perms
     * @param array $return
     *
     * @return array
     */
    public function permuteUnique($items, $perms = [], &$return = [])
    {
        if (empty($items)) {
            $return[] = $perms;
        } else {
            sort($items);
            $prev = false;
            for ($i = \count($items) - 1; $i >= 0; --$i) {
                $newitems = $items;
                $arr = array_splice($newitems, $i, 1);
                $tmp = $arr[0];
                if ($tmp !== $prev) {
                    $prev = $tmp;
                    $newperms = $perms;
                    array_unshift($newperms, $tmp);
                    $this->permuteUnique($newitems, $newperms, $return);
                }
            }

            return $return;
        }
    }

    /**
     * DatePeriod hívás shortcut. Két dátum között visszaadja az összes, $interval paraméternek megfelelő dátumot.
     * Ha a végdátum 00:00:00 időpontot tartalmaz akkor nem lesz benne az eredményben, egyébként igen.
     *
     * @param mixed      $from
     * @param mixed      $to
     * @param string     $interval
     * @param bool       $returnArray     Tömbben adja vissza a dátumokat?
     * @param null|mixed $intervalOptions
     *
     * @return array|\DatePeriod
     */
    public function getDatePeriod($from, $to, $interval = null, $returnArray = false, $intervalOptions = null)
    {
        $period = new \DatePeriod(
            $this->createDateTime($from),
            new \DateInterval($interval ?: 'P1D'),
            $this->createDateTime($to),
            $intervalOptions
        );

        if ($returnArray) {
            $ret = [];
            foreach ($period as $dt) {
                $ret[] = $dt->format('Y-m-d');
            }
            $period = $ret;
        }

        return $period;
    }

    public function getDateDiff($from, $to, $format = null)
    {
        $from = $this->createDateTime($from);
        $to = $this->createDateTime($to);

        $interval = $from->diff($to);

        if (empty($format)) {
            return $interval->days;
        }

        return $interval->format($format);
    }

    /**
     * Css osztályok összefésülése, főleg generátorhoz.
     *
     * @param array|string $currentClass
     * @param array|string $newClasses
     * @param bool         $returnAsString
     *
     * @return array|string
     */
    public function mergeClasses($currentClass, $newClasses, $returnAsString = false)
    {
        if (!\is_array($currentClass)) {
            $currentClass = array_filter(array_map('trim', explode(' ', $currentClass)));
        }

        if (!empty($newClasses)) {
            if (!\is_array($newClasses)) {
                $newClasses = array_filter(array_map('trim', explode(' ', $newClasses)));
            }

            foreach ($newClasses as $class) {
                $this->replaceClass($currentClass, $class);
            }
        }

        $currentClass = !\is_array($currentClass) ? (array) $currentClass : $currentClass;
        $currentClass = array_unique($currentClass);

        return $returnAsString ? implode(' ', $currentClass) : $currentClass;
    }

    /**
     * Bootstrap osztályok cserélése. Ha pl btn-primary van egy gombon és btn-default-ra akarjuk
     * cserélni, akkor ez leveszi a primaryt előbb.
     *
     * @param array  $classes
     * @param string $newClass
     *
     * @return array
     */
    public function replaceClass(array &$classes, $newClass)
    {
        $sizes = [
            'xs',
            'sm',
            'md',
            'lg',
        ];
        $sizesStackedRegexp = '(?P<size>'.implode('|', $sizes).')'; // ha minden méretből lehet egy
        $sizesRegexp = '('.implode('|', $sizes).')'; // ha csak egy féle méret lehet

        $states = [
            'default',
            'primary',
            'success',
            'info',
            'warning',
            'danger',
            'link',
            'muted',
        ];
        $statesRegexp = '('.implode('|', $states).')';

        $map = [
            'glyphicon-.+',
            'col-'.$sizesStackedRegexp.'-\d+',
            'col-'.$sizesStackedRegexp.'-push-\d+',
            'col-'.$sizesStackedRegexp.'-pull-\d+',
            'col-'.$sizesStackedRegexp.'-offset-\d+',
            'btn-'.$sizesRegexp,
            'btn-'.$statesRegexp,
            'btn-group-'.$sizesRegexp,
            'bg-'.$statesRegexp,
            'text-'.$statesRegexp,
            'hidden-'.$sizesStackedRegexp,
            'visible-'.$sizesStackedRegexp.'-block',
            'visible-'.$sizesStackedRegexp.'-inline',
            'visible-'.$sizesStackedRegexp.'-inline-block',
            'well-'.$sizesRegexp,
            'panel-'.$statesRegexp,
            'alert-'.$statesRegexp,
            'label-'.$statesRegexp,
            'pull-(left|right)',
        ];

        foreach ($map as $regexp) {
            $matches = [];
            $pattern = '/^'.$regexp.'$/';
            if (preg_match($pattern, $newClass, $matches)) {
                foreach ($classes as $idx => $cls) {
                    $submatches = [];
                    if (preg_match($pattern, $cls, $submatches)) {
                        if (isset($matches['size'])) {
                            $unset = $matches['size'] === $submatches['size'];
                        } else {
                            $unset = true;
                        }

                        if ($unset) {
                            unset($classes[$idx]);
                        }
                    }
                }
            }
        }

        $classes[] = $newClass;

        return $classes;
    }

    /**
     * DateTime készítése egy bejövő dátumból vagy timestampból.
     *
     * @param \DateTime|int|string $date
     * @param bool                 $throwOnError
     *
     * @throws \Exception
     *
     * @return null|\DateTime
     */
    public function createDateTime($date, $throwOnError = false)
    {
        if (null !== $date && !($date instanceof \DateTime)) {
            try {
                if (ctype_digit($date)) {
                    $dt = new \DateTime();
                    $dt->setTimestamp($date);

                    $date = $dt;
                } else {
                    $date = new \DateTime($date);
                }
            } catch (\Exception $e) {
                if ($throwOnError) {
                    throw $e;
                }

                $date = null;
            }
        }

        return $date;
    }

    /**
     * Egy tömb minden eleme elé rak egy szöveget.
     *
     * @param array  $choices
     * @param string $prefix
     *
     * @return array
     */
    public function prefixArrayElements(array $choices, $prefix)
    {
        $data = [];
        foreach ($choices as $choice) {
            $data[$choice] = $prefix.$choice;
        }

        return $data;
    }

    public function htmlToText($html)
    {
        return preg_replace("/\n\s+/", "\n", rtrim(html_entity_decode(strip_tags($html))));
    }

    public function truncateToNearestWord($text, $length, $encoding = 'UTF-8')
    {
        if (mb_strlen($text, $encoding) <= $length) {
            return $text;
        }

        $newtext = mb_substr($text, 0, $length, $encoding);

        if (' ' === mb_substr($text, mb_strlen($newtext, $encoding) - 1, 1, $encoding)) {
            return rtrim($newtext);
        }

        if (',' === mb_substr($text, mb_strlen($newtext, $encoding) - 1, 1, $encoding)) {
            return rtrim($newtext, ', ');
        }

        $newtext = rtrim($newtext);

        for ($i = mb_strlen($newtext, $encoding) - 1; $i >= 0; --$i) {
            if (' ' === mb_substr($newtext, $i, 1, $encoding) || ',' === mb_substr($newtext, $i, 1, $encoding)) {
                return mb_substr($newtext, 0, $i, $encoding);
            }
        }

        return $newtext;
    }

    public function convertToMetaText($text)
    {
        return empty($text) ? false : trim(preg_replace('/\s\s+/', ' ', str_replace("\n", ' ', strip_tags(html_entity_decode(str_replace(['<br />', '<br>'], ' ', $text), null, 'utf-8')))));
    }

    /**
     * Szövegek rövidítése.
     *
     * @param string $text
     * @param int    $max         Hány karakternél vágjuk le?
     * @param bool   $addEllipsis ... hozzáadása a végéhez, ha rövidítés történt?
     *
     * @return string
     */
    public function shortenText($text, $max = 100, $addEllipsis = false)
    {
        $len = mb_strlen($text, 'UTF-8');
        if ($len > $max) {
            $max = $addEllipsis ? $max - 3 : $max;
            $truncated = mb_substr($text, 0, $max, 'UTF-8');

            if (!$addEllipsis) {
                $endChars = [
                    '.',
                    '!',
                    '?',
                ];
                // mondat befejezése
                do {
                    $char = mb_substr($text, $max, 1, 'UTF-8');
                    $truncated .= $char;

                    ++$max;
                } while ($char && !\in_array($char, $endChars, true));
            } else {
                $truncated .= '&hellip;';
            }

            $text = $truncated;
        }

        return $text;
    }

    public function curlPost($url, $params)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, \count($params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    public function curlGet($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.(empty($params) ? '' : ('?'.http_build_query($params))));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    public function getTimezoneChoices($what = \DateTimeZone::ALL)
    {
        $timezones = [];
        $zones = \DateTimeZone::listIdentifiers($what);
        foreach ($zones as $timezone) {
            $timezones[$timezone] = $timezone;
        }

        return $timezones;
    }

    public function getTimezones($what = \DateTimeZone::ALL)
    {
        $timezones = [];
        $zones = \DateTimeZone::listIdentifiers($what);
        foreach ($zones as $timezone) {
            $timezones[] = $timezone;
        }

        return $timezones;
    }

    public function getGroupedTimezoneChoices()
    {
        $regions = [
            'Africa' => \DateTimeZone::AFRICA,
            'America' => \DateTimeZone::AMERICA,
            'Antarctica' => \DateTimeZone::ANTARCTICA,
            'Asia' => \DateTimeZone::ASIA,
            'Atlantic' => \DateTimeZone::ATLANTIC,
            'Europe' => \DateTimeZone::EUROPE,
            'Indian' => \DateTimeZone::INDIAN,
            'Pacific' => \DateTimeZone::PACIFIC,
        ];
        $timezones = [];
        foreach ($regions as $name => $mask) {
            $zones = \DateTimeZone::listIdentifiers($mask);
            foreach ($zones as $timezone) {
                // Lets sample the time there right now
                $time = new \DateTime(null, new \DateTimeZone($timezone));
                // Us dumb Americans can't handle millitary time
                $ampm = $time->format('H') > 12 ? ' ('.$time->format('g:i a').')' : '';
                // Remove region name and add a sample time
                if (empty($timezones[$name])) {
                    $timezones[$name] = [];
                }
                $timezones[$name][$timezone] = $timezone;
            }
        }

        return $timezones;
    }

    public function getObjectProperty($object, $property, $default = null)
    {
        if (!\is_object($object) || !property_exists($object, $property)) {
            return $default;
        }

        return $object->$property;
    }

    /**
     * @return string
     */
    public function getSchemeAndHttpHost()
    {
        $router = $this->container->get('router');
        $requestStack = $this->container->get('request_stack');

        $request = $requestStack->getCurrentRequest();
        $context = $router->getContext();

        return $request ? $request->getSchemeAndHttpHost() : $context->getScheme().'://'.$context->getHost();
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        $router = $this->container->get('router');
        $requestStack = $this->container->get('request_stack');

        $request = $requestStack->getCurrentRequest();
        $context = $router->getContext();

        return $request ? $request->getScheme() : $context->getScheme();
    }

    /**
     * @return string
     */
    public function getHost()
    {
        $router = $this->container->get('router');
        $requestStack = $this->container->get('request_stack');

        $request = $requestStack->getCurrentRequest();
        $context = $router->getContext();

        return $request ? $request->getHost() : $context->getHost();
    }

    /**
     * @return int|string
     */
    public function getPort()
    {
        $router = $this->container->get('router');
        $requestStack = $this->container->get('request_stack');

        $request = $requestStack->getCurrentRequest();
        $context = $router->getContext();
        $scheme = $context->getScheme();

        return $request ? $request->getPort() : ('https' === $scheme ? $context->getHttpsPort() : $context->getHttpPort());
    }

    /**
     * Returns the HTTP host being requested.
     *
     * The port name will be appended to the host if it's non-standard.
     *
     * @return string
     */
    public function getHttpHost()
    {
        $router = $this->container->get('router');
        $requestStack = $this->container->get('request_stack');

        $request = $requestStack->getCurrentRequest();
        $context = $router->getContext();
        if ($request) {
            return $request->getHttpHost();
        }

        $scheme = $context->getScheme();
        $port = 'https' === $scheme ? $context->getHttpsPort() : $context->getHttpPort();

        if (('http' === $scheme && 80 === $port) || ('https' === $scheme && 443 === $port)) {
            return $context->getHost();
        }

        return $context->getHost().':'.$port;
    }
}
