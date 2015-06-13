<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Installer
{
    const TEST_FOLDER = 'application/tests';

    public static function install()
    {
        self::recursiveCopy(
            'vendor/kenjis/ci-phpunit-test/application/tests',
            static::TEST_FOLDER
        );
        self::fixPath();
    }

    /**
     * Fix paths in Bootstrap.php
     */
    private static function fixPath()
    {
        $file = static::TEST_FOLDER . '/Bootstrap.php';
        $contents = file_get_contents($file);
        
        if (! file_exists('system')) {
            if (file_exists('vendor/codeigniter/framework/system')) {
                $contents = str_replace(
                    '$system_path = \'../../system\';',
                    '$system_path = \'../../vendor/codeigniter/framework/system\';',
                    $contents
                );
            } else {
                throw new Exception('Can\'t find "system" folder.');
            }
        }
        
        if (! file_exists('index.php')) {
            if (file_exists('public/index.php')) {
                $contents = str_replace(
                    "define('FCPATH', realpath(dirname(__FILE__).'/../..').'/');",
                    "define('FCPATH', realpath(dirname(__FILE__).'/../../public').'/');",
                    $contents
                );
            } else {
                throw new Exception('Can\'t find "index.php".');
            }
        }
        
        file_put_contents($file, $contents);
    }

    public static function update()
    {
        self::recursiveUnlink('application/tests/_ci_phpunit_test');
        self::recursiveCopy(
            'vendor/kenjis/ci-phpunit-test/application/tests/_ci_phpunit_test',
            'application/tests/_ci_phpunit_test'
        );
    }

    /**
     * Recursive Copy
     *
     * @param string $src
     * @param string $dst
     */
    private static function recursiveCopy($src, $dst)
    {
        @mkdir($dst, 0755);
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($src, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                @mkdir($dst . '/' . $iterator->getSubPathName());
            } else {
                $success = copy($file, $dst . '/' . $iterator->getSubPathName());
                if ($success) {
                    echo 'copied: ' . $dst . '/' . $iterator->getSubPathName() . PHP_EOL;
                }
            }
        }
    }

    /**
     * Recursive Unlink
     *
     * @param string $dir
     */
    private static function recursiveUnlink($dir)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file);
            } else {
                unlink($file);
            }
        }
        
        rmdir($dir);
    }
}
