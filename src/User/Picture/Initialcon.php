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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User\Picture;

use Hubzero\Image\Initialcon as Processor;
use Hubzero\Content\Moderator;
use Hubzero\Utility\String;

/**
 * Initialcon User picture
 */
class Initialcon implements Resolver
{
	/**
	 * Primary color
	 *
	 * @var  string
	 */
	protected $color = null;

	/**
	 * File path
	 *
	 * @var  string
	 */
	protected $path = null;

	/**
	 * File size
	 *
	 * @var  string
	 */
	protected $pictureSize = 200;

	/**
	 * File name
	 *
	 * @var  string
	 */
	protected $pictureName = 'profile.png';

	/**
	 * Thumbnail size
	 *
	 * @var  string
	 */
	protected $thumbnailSize = 50;

	/**
	 * Thumbnail name
	 *
	 * @var  string
	 */
	protected $thumbnailName = 'thumb.png';

	/**
	 * Constructor
	 *
	 * @param   array  $config
	 * @return  void
	 */
	public function __construct($config=array())
	{
		if (array_key_exists('pictureSize', $config))
		{
			$this->pictureSize = $config['pictureSize'];
		}

		if (array_key_exists('pictureName', $config))
		{
			$this->pictureName = $config['pictureName'];
		}

		if (array_key_exists('thumbnailSize', $config))
		{
			$this->thumbnailSize = $config['thumbnailSize'];
		}

		if (array_key_exists('thumbnailName', $config))
		{
			$this->thumbnailName = $config['thumbnailName'];
		}

		if (array_key_exists('path', $config))
		{
			$this->path = $config['path'];
		}

		if (array_key_exists('color', $config))
		{
			$this->color = $config['color'];
		}
	}

	/**
	 * Get a path or URL to a user pciture
	 *
	 * @param   integer  $id
	 * @param   string   $name
	 * @param   string   $email
	 * @param   bool     $thumbnail
	 * @return  string
	 */
	public function picture($id, $name, $email, $thumbnail = true)
	{
		$processor = new Processor();

		$size = $this->pictureSize;
		$file = $this->pictureName;

		if ($thumbnail)
		{
			$size = $this->thumbnailSize;
			$file = $this->thumbnailName;
		}

		$dir  = $this->path . DIRECTORY_SEPARATOR . String::pad($id, 5) . DIRECTORY_SEPARATOR;
		$path = $dir . $file;

		if (file_exists($path))
		{
			return with(new Moderator($path))->getUrl();
		}

		// If the name has a space
		if (strstr($name, ' '))
		{
			$parts = explode(' ', $name);
			$first = array_shift($parts);
			$last  = array_pop($parts);

			$initials = substr($first, 0, 1) . substr($last, 0, 1);
		}
		// One word name? (e.g., "Madonna")
		// Take the first two letters
		else
		{
			$initials = substr($name, 0, 2);
		}
		$initials = strtoupper(trim($initials));

		$image = $processor->getImageData($initials, $size, $this->color);

		if (!is_dir($dir))
		{
			@mkdir($dir, 0755, true);
		}
		@file_put_contents($path, $image);

		if (!file_exists($path))
		{
			return sprintf('data:image/png;base64,%s', base64_encode($image));
		}

		return with(new Moderator($path))->getUrl();
	}
}
