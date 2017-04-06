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
 * @author    Sam Wilson <samwilson@purdue.edu>
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
use Components\Developer\Models\Accesstoken;

/**
 * Developer access tokens command class
 **/
class AccessTokens extends Base implements CommandInterface
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
	 * Delete all access tokens
	 *
	 * @return void
	 **/
	public function revokeAll()
	{
		$force = $this->arguments->getOpt('force');
		if ($force == 1)
		{
			// Attempt to delete tokens
			try
			{
				with(new Query)->delete('#__developer_access_tokens')->execute();
			}
			catch (QueryFailedException $e)
			{
				$this->output->error('Error:' . $e->getMessage());
			}

			// Successfully deleted tokens
			$this->output->addLine('All access tokens successfully revoked.', 'success');
		}
		else
		{
			$this->output->error('Warning: You must specify --force. Use with caution.');
		}
	}

	public function generate()
	{
		require_once Component::path('com_developer') . '/models/application.php';
		require_once Component::path('com_developer') . '/models/accesstoken.php';

		$user = $this->arguments->getOpt('user');
		$application_id = $this->arguments->getOpt('client');
		$applicationSecret = $this->arguments->getOpt('secret');

		if (!$user)
		{
			$this->output->error('Error: Provide a user id: --user=<user_id>');
			return false;
		}
		elseif (!$application_id)
		{
			$this->output->error('Error: Provide a Client ID: --client=<client_id>');
			return false;
		}
		elseif (!$applicationSecret)
		{
			$this->output->error('Error: Provide a Client Secret: --secret=<client_secret>');
			return false;
		}

		// Validate application
		$application = DevApp::all()
			->whereEquals('client_id', $application_id)
			->whereEquals('client_secret', $applicationSecret)
			->limit(1)
			->row()
			->toObject();

		// @TODO: Application verification
		if (is_object($application) && isset($application->id))
		{
			$accessToken = Accesstoken::oneOrNew(0);
			$accessToken->set('application_id', $application->id);
			$accessToken->set('uidNumber', $user);
			$accessToken->set('access_token', $this->generateAccessToken());

			if ($accessToken->save())
			{
				$token = $accessToken->access_token;
				$this->output->addLine($token);
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			$this->output->error('Error: No valid application found.');
			return false;
		}
	}

	/**
	 * Generates an Access Token to be used by the CLI
	 * This code was borrowed from bshaffer/outh2-server-php
	 * @TODO: Formally replace this and use the correct OAuth2 Flow.
	 **/
	protected function generateAccessToken()
	{
		$tokenLen = 40;
		if (function_exists('mcrypt_create_iv'))
		{
				$randomData = mcrypt_create_iv(100, MCRYPT_DEV_URANDOM);
		}
		else if (function_exists('openssl_random_pseudo_bytes'))
		{
				$randomData = openssl_random_pseudo_bytes(100);
		}
		else if (@file_exists('/dev/urandom')) { // Get 100 bytes of random data
				$randomData = file_get_contents('/dev/urandom', false, null, 0, 100) . uniqid(mt_rand(), true);
		}
		else
		{
				$randomData = mt_rand() . mt_rand() . mt_rand() . mt_rand() . microtime(true) . uniqid(mt_rand(), true);
		}
			return substr(hash('sha512', $randomData), 0, $tokenLen);
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
				'Api access token related commands.'
			);
	}
}
