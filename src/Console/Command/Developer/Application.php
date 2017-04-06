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
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Developer;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Database\Query;
use Hubzero\Database\Exception\QueryFailedException;
use Components\Developer\Models\Application as DevApp;
use Hubzero\Utility\Sanitize;

/**
 * Developer Application commands
 **/
class Application extends Base implements CommandInterface
{
	/**
	 * Default (required) command
	 *
	 * @return void
	 **/
	public function execute()
	{
		$this->output = $this->output->getHelpOutput();
		$this->help();
		$this->output->render();
		return;
	}

	/**
	 * Create a new API Application
	 *
	 * @return string	Comma-separated client_id, client_secret
	 **/
	public function create()
	{
		$name = Sanitize::clean($this->arguments->getOpt('name'));
		$description = Sanitize::clean($this->arguments->getOpt('description'));
		$user = Sanitize::clean($this->arguments->getOpt('user'));

		if (!$name)
		{
			$this->output->error('Error: You must provide a name for the application.');
			return false;
		}
		elseif (!$description)
		{
			$this->output->error('Error: You must provide a description of the application.');
			return false;
		}
		elseif (!$user)
		{
			$this->output->error('Error: You must provide a User ID.');
			return false;
		}

		require_once Component::path('com_developer') . '/models/application.php';
		$application = DevApp::oneOrNew(0);
		$application->set('name', $name);
		$application->set('description', $description);
		$application->set('created_by', $user);
		if ($application->save())
		{
			$application = $application->toObject();
			$response = $application->client_id . ',' . $application->client_secret;
			$this->output->addLine($response);
		}
		else
		{
			$this->output->error('Error: There was an error saving your application');
		}
	}

	/**
	 * Output help documentation
	 *
	 * @return void
	 **/
	public function help()
	{
		$this
			->output
			->addOverview(
				'Create a new API application'
			);
	}
}
