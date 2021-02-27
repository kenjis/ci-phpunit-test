<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2021 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch;

use Composer\InstalledVersions;
use OutOfBoundsException;

class PhpParserVersion
{
	/**
	 * @var string|null
	 */
	private $version;

	public function __construct()
	{
		try {
			$this->version = InstalledVersions::getVersion('nikic/php-parser');
		} catch (OutOfBoundsException $e) {
			$this->version = null;
		}
	}

	public function isComposerInstalled()
	{
		if ($this->version === null) {
			return false;
		}

		return true;
	}

	/**
	 * @return string|null
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @param string $version eg. '4.5'
	 */
	public function isGreaterThanOrEqualTo($version)
	{
		$parts = explode('.', $version);
		$major = (int) $parts[0];
		$minor = (int) $parts[1];

		$parts = explode('.', $this->version);
		$major_installed = (int) $parts[0];
		$minor_installed = (int) $parts[1];

		if ($major_installed < $major) {
			return false;
		}

		if ($major_installed > $major) {
			return true;
		}

		if ($minor_installed >= $minor) {
			return true;
		}

		return false;
	}
}
