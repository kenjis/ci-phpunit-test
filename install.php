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

$silent = false;
$app_dir = 'application';
$pub_dir = 'public';

if($argc > 1){
    for ($i = 1; $i <= $argc; $i++){
        if(!isset($argv[$i]))
            break;
        switch ($argv[$i]){
            // php install.php -s
            case '-s':
                $silent = true;
                break;
            // php install.php -a application
            case '-a':
                if(is_dir($argv[$i+1]))
                    $app_dir = $argv[$i+1];
                $i++;
                break;
            // php install.php -p public
            case '-p':
                if(is_dir($argv[$i+1]))
                    $pub_dir = $argv[$i+1];
                $i++;
                break;
            default:
                throw new Exception('Unknown argument: ' . $argv[$i]);
        }
    }
}

$installer = new Installer($silent);
$installer->install($app_dir, $pub_dir);
