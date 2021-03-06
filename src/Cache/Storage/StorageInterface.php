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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Storage;

/**
 * Cache storage interface
 */
interface StorageInterface
{
	/**
	 * Add cache item. If item already exist in storage return false.
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @param   int     $ttl
	 * @return  bool
	 */
	public function add($key, $value, $minutes);

	/**
	 * Set cache item.
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @param   int     $ttl
	 * @return  bool
	 */
	public function put($key, $value, $minutes);

	/**
	 * Get cache item
	 *
	 * @param   string  $key
	 * @return  mixed
	 */
	public function get($key);

	/**
	 * Check cache item.
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function has($key);

	/**
	 * Delete cache item.
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function forget($key);
}
