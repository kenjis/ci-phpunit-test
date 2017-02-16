# Tips for ci-phpunit-test for CodeIgniter 3.x

## Using ci-phpunit-test for Multiple Projects

You can use symlink.

After installing ci-phpunit-test, run the following commands:

```
$ cd /path/to/your/codeigniter/application/test
$ rm -rf _ci_phpunit_test
$ ln -s /path/to/ci-phpunit-test/application/tests/_ci_phpunit_test/ .
```
