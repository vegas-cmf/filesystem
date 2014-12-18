<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */ 

namespace Vegas\Tests;

use Vegas\Filesystem;

/**
 * Class ManagerTest
 *
 * ! IMPORTANT !
 * Check tests/README.md before you run tests
 *
 * @package Vegas\Tests
 */
class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    private function getFilesystem() {
        $config = [
            'default'   =>  'local',
            'local' => [
                'directory' =>  dirname(__FILE__) . '/fixtures/local'
            ],
            'ftp'   =>  [
                'path'  =>  './',
                'host'  =>  'localhost',
                'options'    => [
                    'username' => 'ftp-user',
                    'password' => 'test1234',
                    'ssl' => false
                ]
            ],
            's3'    =>  [
                'key'   =>  'fakekey',
                'secret'    =>  'fakesecret',
                'region'    => 'eu-west-1',
                'bucket'    =>  'test',
                'scheme'    =>  'http'
            ]
        ];
        $filesystem = new Filesystem($config);
        return $filesystem;
    }

    public function testShouldReturnAdapterInstance()
    {
        $filesystem = $this->getFilesystem();

        $this->assertInstanceOf('\Vegas\Filesystem\Wrapper', $filesystem->default);
        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\Local', $filesystem->default->getAdapter());
        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\Local', $filesystem->getAdapter('default')->getAdapter());
        $this->assertInstanceOf('\Vegas\Filesystem\Wrapper', $filesystem->getAdapter('default'));

        $this->assertInstanceOf('\Vegas\Filesystem\Wrapper', $filesystem->local);
        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\Local', $filesystem->local->getAdapter());
        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\Local', $filesystem->getAdapter('local')->getAdapter());
        $this->assertInstanceOf('\Vegas\Filesystem\Wrapper', $filesystem->getAdapter('local'));

        $this->assertInstanceOf('\Vegas\Filesystem\Wrapper', $filesystem->ftp);
        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\Ftp', $filesystem->ftp->getAdapter());
        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\Ftp', $filesystem->getAdapter('ftp')->getAdapter());
        $this->assertInstanceOf('\Vegas\Filesystem\Wrapper', $filesystem->getAdapter('ftp'));

        $this->assertInstanceOf('\Vegas\Filesystem\Wrapper', $filesystem->s3);
        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\S3', $filesystem->s3->getAdapter());
        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\S3', $filesystem->getAdapter('s3')->getAdapter());
        $this->assertInstanceOf('\Vegas\Filesystem\Wrapper', $filesystem->getAdapter('s3'));
    }

    public function testShouldThrowExceptionForNotExistingAdapter()
    {
        $filesystem = $this->getFilesystem();

        $exception = null;
        try {
            $filesystem->getAdapter('fake');
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Filesystem\Exception\AdapterNotFoundException', $exception);

        $exception = null;
        try {
            $filesystem->fake;
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Filesystem\Exception\AdapterNotFoundException', $exception);
    }

    public function testShouldChangeAdapterConfiguration()
    {
        $filesystem = new Filesystem([
            'local' => [
                'directory' => TESTS_ROOT_DIR . '/fixtures/tmp'
            ]
        ]);

        $filesystem->local->write('test.txt', 'Test');
        $filesystem->local->delete('test.txt');

        $this->assertContains(TESTS_ROOT_DIR . '/fixtures/tmp', $filesystem->local->getUrl('test.txt'));
        $filesystem->setAdapterConfig('local', [
            'directory' => TESTS_ROOT_DIR . '/fixtures/tmp/test'
        ]);

        $filesystem->local->write('test.txt', 'Test');
        $this->assertContains(TESTS_ROOT_DIR . '/fixtures/tmp/test', $filesystem->local->getUrl('test.txt'));
        $filesystem->local->delete('test.txt');
    }

    public function testShouldThrowExceptionWhenInitializeAdapterWithoutConfig()
    {
        $filesystem = new Filesystem([]);

        $exception = null;
        try {
            $filesystem->local;
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Filesystem\Exception', $exception);

        $exception = null;
        try {
            $filesystem->s3;
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Filesystem\Exception', $exception);

        $exception = null;
        try {
            $filesystem->ftp;
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Filesystem\Exception', $exception);
    }

    /**
     * @source http://stackoverflow.com/a/4263181
     * @param $a
     * @param $b
     * @return bool
     */
    private function compareRefs(&$a, &$b) {
        if (is_object($a) && is_object($b)) {
            return ($a === $b);
        }

        $temp_a = $a;
        $temp_b = $b;

        $key = uniqid('is_ref_to', true);
        $b = $key;

        if ($a === $key) $return = true;
        else $return = false;

        $a = $temp_a;
        $b = $temp_b;
        return $return;
    }

    public function testObjectInstantiate()
    {
        $filesystem = $this->getFilesystem();
        $local = $filesystem->local;
        $local2 = $filesystem->local;
        $this->assertTrue($this->compareRefs($local, $local2));

        $local3 = \Vegas\Filesystem\Adapter\Local::setup(
            ['directory' => TESTS_ROOT_DIR . '/fixtures/tmp']
        );
        $this->assertFalse($this->compareRefs($local, $local3));
        $this->assertFalse($this->compareRefs($local2, $local3));
    }
}
 