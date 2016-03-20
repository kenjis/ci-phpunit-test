<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

require __DIR__ . '/Installer.php';

$app = 'application';
if ($argv && $argv[1] && is_dir($argv[1])) {
    $app = $argv[1];
}
$installer = new Installer();
$installer->install($app);
