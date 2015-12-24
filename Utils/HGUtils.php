<?php
  
namespace HG\UtilsBundle\Utils;

use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class HGUtils
{
  /**
   * Converts string to array
   *
   * @param  string $string  the value to convert to array
   *
   * @return array
   */
  public static function stringToArray($string)
  {
    preg_match_all('/
      \s*(\w+)              # key                               \\1
      \s*=\s*               # =
      (\'|")?               # values may be included in \' or " \\2
      (.*?)                 # value                             \\3
      (?(2) \\2)            # matching \' or " if needed        \\4
      \s*(?:
        (?=\w+\s*=) | \s*$  # followed by another key= or the end of the string
      )
    /x', $string, $matches, PREG_SET_ORDER);

    $attributes = array();
    foreach ($matches as $val)
    {
      $attributes[$val[1]] = self::literalize($val[3]);
    }

    return $attributes;
  }

  public static function literalize($value, $quoted = false)
  {
    // lowercase our value for comparison
    $value  = trim($value);
    $lvalue = strtolower($value);

    if (in_array($lvalue, array('null', '~', '')))
    {
      $value = null;
    }
    else if (in_array($lvalue, array('true', 'on', '+', 'yes')))
    {
      $value = true;
    }
    else if (in_array($lvalue, array('false', 'off', '-', 'no')))
    {
      $value = false;
    }
    else if (ctype_digit($value))
    {
      $value = (int) $value;
    }
    else if (is_numeric($value))
    {
      $value = (float) $value;
    }
    else
    {
      if ($quoted)
      {
        $value = '\''.str_replace('\'', '\\\'', $value).'\'';
      }
    }

    return $value;
  }
  
  public static function isSlug($slug, $minLength = 0)
  {
    return preg_match('|^[a-z0-9_-]+$|', $slug) && ($minLength <= 0 || strlen($slug) >= $minLength);
  }
  
  public static function slugify($text, $substitute = '-')
  {
   // replace all non letters or digits by substitute
    $text = preg_replace('~[^\\pL0-9]+~u', $substitute, $text); // substitutes anything but letters, numbers and '_' with separator
    $text = trim($text, $substitute);
    $text = iconv("utf-8", "us-ascii//TRANSLIT", $text); // TRANSLIT does the whole job
    $text = strtolower($text);
    $text = preg_replace('~[^-a-z0-9_]+~', '', $text); // keep only letters, numbers, '_' and separator  $text = preg_replace('~[^\\pL0-9_]+~u', '-', $text); // substitutes anything but letters, numbers and '_' with separator
    $text = trim($text, "-");
    $text = iconv("utf-8", "us-ascii//TRANSLIT", $text); // TRANSLIT does the whole job
    $text = strtolower($text);
    $text = preg_replace('~[^-a-z0-9_]+~', '', $text); // keep only letters, numbers, '_' and separator 
 
    return $text;
   }
   
  /**
   * Clear all files in a given directory.
   *
   * @param string $directory  An absolute filesystem path to a directory.
   */
  public static function clearDirectory($directory)
  {
    if (!is_dir($directory))
    {
      return;
    }

    // open a file point to the cache dir
    $fp = opendir($directory);

    // ignore names
    $ignore = array('.', '..', 'CVS', '.svn');

    while (($file = readdir($fp)) !== false)
    {
      if (!in_array($file, $ignore))
      {
        if (is_link($directory.'/'.$file))
        {
          // delete symlink
          unlink($directory.'/'.$file);
        }
        else if (is_dir($directory.'/'.$file))
        {
          // recurse through directory
          self::clearDirectory($directory.'/'.$file);

          // delete the directory
          rmdir($directory.'/'.$file);
        }
        else
        {
          // delete the file
          unlink($directory.'/'.$file);
        }
      }
    }

    // close file pointer
    closedir($fp);
  }
  
  /**
   * Clear all files and directories corresponding to a glob pattern.
   *
   * @param string $pattern  An absolute filesystem pattern.
   */
  public static function clearGlob($pattern)
  {
    if (false === $files = glob($pattern))
    {
      return;
    }

    // order is important when removing directories
    sort($files);

    foreach ($files as $file)
    {
      if (is_dir($file))
      {
        // delete directory
        self::clearDirectory($file);
      }
      else
      {
        // delete file
        unlink($file);
      }
    }
  }
  
  /**
   * Determine if a filesystem path is absolute.
   *
   * @param  path $path  A filesystem path.
   *
   * @return bool true, if the path is absolute, otherwise false.
   */
  public static function isPathAbsolute($path)
  {
    if ($path[0] == '/' || $path[0] == '\\' ||
        (strlen($path) > 3 && ctype_alpha($path[0]) &&
         $path[1] == ':' &&
         ($path[2] == '\\' || $path[2] == '/')
        )
       )
    {
      return true;
    }

    return false;
  }
  
  /**
   * Strip slashes recursively from array
   *
   * @param  array $value  the value to strip
   *
   * @return array clean value with slashes stripped
   */
  public static function stripslashesDeep($value)
  {
    return is_array($value) ? array_map(array('sfToolkit', 'stripslashesDeep'), $value) : stripslashes($value);
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
  public static function arrayDeepMerge()
  {
    switch (func_num_args())
    {
      case 0:
        return false;
      case 1:
        return func_get_arg(0);
      case 2:
        $args = func_get_args();
        $args[2] = array();
        if (is_array($args[0]) && is_array($args[1]))
        {
          foreach (array_unique(array_merge(array_keys($args[0]),array_keys($args[1]))) as $key)
          {
            $isKey0 = array_key_exists($key, $args[0]);
            $isKey1 = array_key_exists($key, $args[1]);
            if ($isKey0 && $isKey1 && is_array($args[0][$key]) && is_array($args[1][$key]))
            {
              $args[2][$key] = self::arrayDeepMerge($args[0][$key], $args[1][$key]);
            }
            else if ($isKey0 && $isKey1)
            {
              $args[2][$key] = $args[1][$key];
            }
            else if (!$isKey1)
            {
              $args[2][$key] = $args[0][$key];
            }
            else if (!$isKey0)
            {
              $args[2][$key] = $args[1][$key];
            }
          }
          return $args[2];
        }
        else
        {
          return $args[1];
        }
      default :
        $args = func_get_args();
        $args[1] = sfToolkit::arrayDeepMerge($args[0], $args[1]);
        array_shift($args);
        return call_user_func_array(array('sfToolkit', 'arrayDeepMerge'), $args);
        break;
    }
  }
  
  /**
   * Returns subject replaced with regular expression matchs
   *
   * @param mixed $search        subject to search
   * @param array $replacePairs  array of search => replace pairs
   */
  public static function pregtr($search, $replacePairs)
  {
    return preg_replace(array_keys($replacePairs), array_values($replacePairs), $search);
  }
  
  /**
   * Checks if array values are empty
   *
   * @param  array $array  the array to check
   * @return boolean true if empty, otherwise false
   */
  public static function isArrayValuesEmpty($array)
  {
    static $isEmpty = true;
    foreach ($array as $value)
    {
      $isEmpty = (is_array($value)) ? self::isArrayValuesEmpty($value) : (strlen($value) == 0);
      if (!$isEmpty)
      {
        break;
      }
    }

    return $isEmpty;
  }
  
  /**
   * Checks if a string is an utf8.
   *
   * Yi Stone Li<yili@yahoo-inc.com>
   * Copyright (c) 2007 Yahoo! Inc. All rights reserved.
   * Licensed under the BSD open source license
   *
   * @param string
   *
   * @return bool true if $string is valid UTF-8 and false otherwise.
   */
  public static function isUTF8($string)
  {
    for ($idx = 0, $strlen = strlen($string); $idx < $strlen; $idx++)
    {
      $byte = ord($string[$idx]);

      if ($byte & 0x80)
      {
        if (($byte & 0xE0) == 0xC0)
        {
          // 2 byte char
          $bytes_remaining = 1;
        }
        else if (($byte & 0xF0) == 0xE0)
        {
          // 3 byte char
          $bytes_remaining = 2;
        }
        else if (($byte & 0xF8) == 0xF0)
        {
          // 4 byte char
          $bytes_remaining = 3;
        }
        else
        {
          return false;
        }

        if ($idx + $bytes_remaining >= $strlen)
        {
          return false;
        }

        while ($bytes_remaining--)
        {
          if ((ord($string[++$idx]) & 0xC0) != 0x80)
          {
            return false;
          }
        }
      }
    }

    return true;
  }
  
  /**
   * Adds a path to the PHP include_path setting.
   *
   * @param   mixed  $path     Single string path or an array of paths
   * @param   string $position Either 'front' or 'back'
   *
   * @return  string The old include path
   */
  static public function addIncludePath($path, $position = 'front')
  {
    if (is_array($path))
    {
      foreach ('front' == $position ? array_reverse($path) : $path as $p)
      {
        self::addIncludePath($p, $position);
      }

      return;
    }

    $paths = explode(PATH_SEPARATOR, get_include_path());

    // remove what's already in the include_path
    if (false !== $key = array_search(realpath($path), array_map('realpath', $paths)))
    {
      unset($paths[$key]);
    }

    switch ($position)
    {
      case 'front':
        array_unshift($paths, $path);
        break;
      case 'back':
        $paths[] = $path;
        break;
      default:
        throw new InvalidArgumentException(sprintf('Unrecognized position: "%s"', $position));
    }

    return set_include_path(join(PATH_SEPARATOR, $paths));
  }
  
  public static function camelize($lower_case_and_underscored_word)
  {
    $tmp = $lower_case_and_underscored_word;
    $tmp = self::pregtr($tmp, array('#/(.?)#e'    => "'::'.strtoupper('\\1')",
                                         '/(^|_|-)+(.)/e' => "strtoupper('\\2')"));

    return $tmp;
  }

  /**
   * Returns an underscore-syntaxed version or the CamelCased string.
   *
   * @param  string $camel_cased_word  String to underscore.
   *
   * @return string Underscored string.
   */
  public static function underscore($camel_cased_word)
  {
    $tmp = $camel_cased_word;
    $tmp = str_replace('::', '/', $tmp);
    $tmp = self::pregtr($tmp, array('/([A-Z]+)([A-Z][a-z])/' => '\\1_\\2',
                                         '/([a-z\d])([A-Z])/'     => '\\1_\\2'));

    return strtolower($tmp);
  }
  
  public static function tableize($class_name)
  {
    return self::underscore($class_name);
  }

  /**
   * Returns a human-readable string from a lower case and underscored word by replacing underscores
   * with a space, and by upper-casing the initial characters.
   *
   * @param  string $lower_case_and_underscored_word String to make more readable.
   *
   * @return string Human-readable string.
   */
  public static function humanize($lower_case_and_underscored_word)
  {
    if (substr($lower_case_and_underscored_word, -3) === '_id')
    {
      $lower_case_and_underscored_word = substr($lower_case_and_underscored_word, 0, -3);
    }

    return ucfirst(str_replace('_', ' ', $lower_case_and_underscored_word));
  }
  
  public static function genericUpload(UploadedFile $uploadedFile, $name, $uploadDir)
  {
    $fs = new Filesystem();
    $uDir = $uploadDir.'/'.$name;
    if (!$fs->exists($uDir))
    {
      try 
      {
        $fs->mkdir($uDir);
      }
      catch (IOException $e)
      {
        return new Response(json_encode(array('valid' => false, 'msgs' => $e->getMessage())));
      }
    }
    $origName = pathinfo($uploadedFile->getClientOriginalName(),  PATHINFO_FILENAME);
    $newFilename = self::slugify($origName).'_'.date('YmdHis').'.'.$uploadedFile->getClientOriginalExtension();
    
    $uploadedFile->move($uDir, $newFilename);
    
    return new Response(json_encode(array('valid' => true)));
  }
}
