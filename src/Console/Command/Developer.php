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

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Console\Command\Developer\AccessTokens;

/**
 * Developer command class
 **/
class Developer extends Base implements CommandInterface
{

	/**
	 * Constructor - sets output mechanism and arguments for use by command
	 *
	 * @param   \Hubzero\Console\Output    $output     The ouput renderer
	 * @param   \Hubzero\Console\Arguments $arguments  The command arguments
	 * @return  void
	 **/
	public function __construct(Output $output, Arguments $arguments)
	{
		parent::__construct($output, $arguments);
	}

	/**
	 * Default (required) command - just executes run
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->output = $this->output->getHelpOutput();
		$this->help();
		$this->output->render();
		return;
	}

	/**
	 * Revokes all oauth related tokens/codes
	 * 
	 * @return  void
	 */
	public function reset_hard()
	{
		$force = $this->arguments->getOpt('force');
		if ($force == 1)
		{
			App::get('client')->call('Developer:AccessTokens', 'revokeall', $this->arguments, $this->output);
			App::get('client')->call('Developer:RefreshTokens', 'revokeall', $this->arguments, $this->output);
			App::get('client')->call('Developer:AuthorizationCodes', 'revokeall', $this->arguments, $this->output);
		}
		else
		{
			$this->output->error('You must specify --force. Use with caution.');
		}
	}

	/**
	 * Output help documentation
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this
			->output
			->addOverview(
				'Developer Console Commands for interacting with the API.'
			);
	}
}
