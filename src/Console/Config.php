<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console;

use Hubzero\Config\Registry;
use Hubzero\Error\Exception\RuntimeException;

/**
 * Console configuration class
 **/
class Config
{
	/**
	 * Parsed config vars
	 *
	 * @var  object
	 **/
	private $config = null;

	/**
	 * Config file path
	 *
	 * @var  string
	 **/
	private $path = null;

	/**
	 * Constructs a new config instance
	 *
	 * Parse for muse configuration file from user home directory
	 *
	 * @return  void
	 **/
	public function __construct()
	{
		// Build path
		$home = getenv('HOME');
		$path = $home . DS . '.muse';
		$this->path = $path;

		$this->config = new Registry();

		// See if there's an existing file
		if (is_file($path))
		{
			$this->config->parse($path);
		}
	}

	/**
	 * Creates a new instance of self
	 *
	 * @return  static
	 **/
	public static function getInstance()
	{
		static $instance;

		if (!isset($instance))
		{
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Gets the specified config var
	 *
	 * @param   string  $key      The key to fetch
	 * @param   mixed   $default  The default to return, should the key not exist
	 * @return  mixed
	 **/
	public static function get($key, $default = false)
	{
		$instance = self::getInstance();

		return (isset($instance->config[$key])) ? $instance->config[$key] : $default;
	}

	/**
	 * Saves the data to the config file
	 *
	 * Passed data will be merged with existing data.
	 *
	 * @param   array  $data  The data to save
	 * @return  bool
	 **/
	public static function save($data)
	{
		$instance = self::getInstance();

		// Merge and make sure values are unique
		$data = $instance->merge($instance->config->toArray(), $data);
		$data = $instance->unique($data);

		// Set data back to the instance
		$instance->config = new Registry($data);

		// Actually write out the data
		$instance->write();

		return true;
	}

	/**
	 * Writes the data to the configuration file
	 *
	 * @return  void
	 **/
	private function write()
	{
		$this->config->write($this->path, 'yaml');
	}

	/**
	 * Merge multiple arrays into one, recursively
	 *
	 * Dear future developer who comes in and says, "Why, there's a PHP function for that!
	 * It's called array_merge_recursive".  Don't do it!  This function works slightly 
	 * differently.  Namely, if a nested array is not associative, we want it to append items
	 * to it, rather than completely overwrite the value of the nested element.
	 *
	 * @param   array  $existing  The existing data
	 * @param   array  $incoming  The new data
	 * @return  array
	 **/
	private function merge($existing, $incoming)
	{
		foreach ($incoming as $k => $v)
		{
			if (is_array($v))
			{
				$existing[$k] = isset($existing[$k]) ? $this->merge($existing[$k], $v) : $this->merge(array(), $v);
			}
			else
			{
				if (is_numeric($k))
				{
					$existing[] = $v;
				}
				else
				{
					$existing[$k] = $v;
				}
			}
		}

		return $existing;
	}

	/**
	 * Multi-dimensional array_unique function
	 *
	 * @param   array  $var  The array to make unique
	 * @return  array
	 **/
	private function unique($var)
	{
		if (is_array($var))
		{
			// We only want to get unique items if they aren't associative
			if (isset($var[0]))
			{
				// Serialize vars, unique them, then unserialize
				$var = array_map('unserialize', array_unique(array_map('serialize', $var)));
			}

			foreach ($var as &$sub)
			{
				if (is_array($sub))
				{
					$sub = $this->unique($sub);
				}
			}
		}

		return $var;
	}
}
