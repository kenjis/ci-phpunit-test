<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Installer
{
    private $silent = false;
    private $app_dir = 'application';
    private $pub_dir = 'public';
    private $test_dir = null;

    public function __construct($argv)
    {
        $this->parse_args($argv);
    }

    private function parse_args($argv)
    {
        $argc = count($argv);

        if ($argc === 1) {
            return;
        }

        for ($i = 1; $i <= $argc; $i++) {
            if (! isset($argv[$i])) {
                break;
            }

            switch ($argv[$i]) {
                // php install.php -s
                case '-s':
                    $this->silent = true;
                    break;

                // php install.php -a application
                case '-a':
                    if (is_dir($argv[$i+1])) {
                        $this->app_dir = $argv[$i+1];
                    } else {
                        throw new Exception('No such directory: '.$argv[$i+1]);
                    }
                    $i++;
                    break;

                // php install.php -p public
                case '-p':
                    if (is_dir($argv[$i+1])) {
                        $this->pub_dir = $argv[$i+1];
                    } else {
                        throw new Exception('No such directory: '.$argv[$i+1]);
                    }
                    $i++;
                    break;
				// php install.php -p public
                case '-t':
                    if (is_dir($argv[$i+1])) {
                        $this->test_dir = $argv[$i+1];
                    } else {
                        throw new Exception('No such directory: '.$argv[$i+1]);
                    }
                    $i++;
                    break;

                default:
                    throw new Exception('Unknown argument: '.$argv[$i]);
            }
        }
        if (is_null($this->test_dir)) {
			$test_dir = $this->app_dir.'/tests';
		}
    }

    public function install()
    {
        $this->recursiveCopy(
            dirname(dirname(__FILE__)).'/application/tests',
            $this->test_dir
        );
        $this->fixPath();
    }

    /**
     * Fix paths in Bootstrap.php
     */
    private function fixPath()
    {
        $file = $this->test_dir.'/Bootstrap.php';
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
            if (file_exists($this->pub_dir.'/index.php')) {
                // CodeIgniter 3.0.6 and after
                $contents = str_replace(
                    "define('FCPATH', realpath(dirname(__FILE__).'/../..').DIRECTORY_SEPARATOR);",
                    "define('FCPATH', realpath(dirname(__FILE__).'/../../{$this->pub_dir}').DIRECTORY_SEPARATOR);",
                    $contents
                );
                // CodeIgniter 3.0.5 and before
                $contents = str_replace(
                    "define('FCPATH', realpath(dirname(__FILE__).'/../..').'/');",
                    "define('FCPATH', realpath(dirname(__FILE__).'/../../{$this->pub_dir}').'/');",
                    $contents
                );
            } elseif (file_exists($this->app_dir.'/public/index.php')) {
                // CodeIgniter 3.0.6 and after
                $contents = str_replace(
                    "define('FCPATH', realpath(dirname(__FILE__).'/../..').DIRECTORY_SEPARATOR);",
                    "define('FCPATH', realpath(dirname(__FILE__).'/../public').DIRECTORY_SEPARATOR);",
                    $contents
                );
                // CodeIgniter 3.0.5 and before
                $contents = str_replace(
                    "define('FCPATH', realpath(dirname(__FILE__).'/../..').'/');",
                    "define('FCPATH', realpath(dirname(__FILE__).'/../public').'/');",
                    $contents
                );
                if ($this->app_dir !== 'application') {
                    $contents = str_replace(
                        "\$application_folder = '../../application';",
                        "\$application_folder = '../../{$this->app_dir}';",
                        $contents
                    );
                }
            } else {
                throw new Exception('Can\'t find "index.php".');
            }
        }

        file_put_contents($file, $contents);
    }

    public function update()
    {
        $target_dir = $this->test_dir.'/_ci_phpunit_test';
        $this->recursiveUnlink($target_dir);
        $this->recursiveCopy(
            dirname(dirname(__FILE__)).'/application/tests/_ci_phpunit_test',
            $target_dir
        );
    }

    /**
     * Recursive Copy
     *
     * @param string $src
     * @param string $dst
     */
    private function recursiveCopy($src, $dst)
    {
        @mkdir($dst, 0755);

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($src, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                @mkdir($dst.'/'.$iterator->getSubPathName());
            } else {
                $success = copy($file, $dst.'/'.$iterator->getSubPathName());
                if ($success) {
                    if (! $this->silent) {
                        echo 'copied: '.$dst.'/'.$iterator->getSubPathName().PHP_EOL;
                    }
                }
            }
        }
    }

    /**
     * Recursive Unlink
     *
     * @param string $dir
     */
    private function recursiveUnlink($dir)
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
