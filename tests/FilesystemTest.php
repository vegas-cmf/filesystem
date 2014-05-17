<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://github.com/vegas-cmf
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
        $config = array(
            'local' => array(
                'directory' =>  dirname(__FILE__) . '/fixtures/local'
            ),
            'ftp'   =>  array(
                'path'  =>  './',
                'host'  =>  'localhost',
                'options'    => array(
                    'username' => 'ftp-user',
                    'password' => 'test1234',
                    'ssl' => false
                )
            ),
            's3'    =>  array(
                'key'   =>  'fakekey',
                'secret'    =>  'fakesecret',
                'endpoint'  =>  'localhost:4567',
                'bucket'    =>  'test',
                'scheme'    =>  'http'
            )
        );
        $filesystem = new Filesystem($config);
        return $filesystem;
    }

    public function testAdapterInitialization()
    {
        $filesystem = $this->getFilesystem();

        $this->assertInstanceOf('\Gaufrette\Filesystem', $filesystem->local);
        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\Local', $filesystem->local->getAdapter());

        $this->assertInstanceOf('\Gaufrette\Filesystem', $filesystem->ftp);
        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\Ftp', $filesystem->ftp->getAdapter());

        $filesystem->s3->getAdapter()->getService()->setBaseUrl('localhost:4567');
        $this->assertInstanceOf('\Gaufrette\Filesystem', $filesystem->s3);
        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\S3', $filesystem->s3->getAdapter());
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

        $local3 = \Vegas\Filesystem\Adapter\Local::setup(array('directory' => dirname(__FILE__) . '/fixtures/local'));
        $this->assertFalse($this->compareRefs($local, $local3));
        $this->assertFalse($this->compareRefs($local2, $local3));
    }

    public function testLocalAdapter()
    {
        $_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__) . '/fixtures/local';
        $filesystem = $this->getFilesystem();

        $this->assertNotNull($filesystem->local->write('test.txt', 'Example content', true));
        $this->assertNotNull($filesystem->local->checksum('test.txt'));
        $this->assertEquals('Example content', $filesystem->local->read('test.txt'));

        $this->assertEquals($_SERVER['DOCUMENT_ROOT'] . '/test.txt', $filesystem->local->getUrl('test.txt'));
        $this->assertEquals('/test.txt', $filesystem->local->getUrl('test.txt', array('relative' => true)));

        $this->assertTrue($filesystem->local->delete('test.txt'));

        $this->setExpectedException('\Gaufrette\Exception\FileNotFound');
        $filesystem->local->read('test.txt');

        $this->assertNotNull($filesystem->local->write('sub/test.txt', 'Example content', true));
        $this->assertEquals('Example content', $filesystem->local->read('sub/test.txt'));
        $this->assertTrue($filesystem->local->delete('sub/test.txt'));

        $this->setExpectedException('\Gaufrette\Exception\FileNotFound');
        $filesystem->local->read('sub/test.txt');
    }

    public function testFtpAdapter()
    {
        $filesystem = $this->getFilesystem();

        $this->assertNotNull($filesystem->ftp->write('test.txt', 'Example ftp content', true));
        $this->assertNotNull($filesystem->ftp->checksum('test.txt'));
        $this->assertEquals('Example ftp content', $filesystem->ftp->read('test.txt'));

        $this->assertEquals('ftp://ftp-user@localhost/home/ftp-user/test.txt', $filesystem->ftp->getUrl('test.txt'));
        $this->assertEquals('/home/ftp-user/test.txt', $filesystem->ftp->getUrl('test.txt', array('relative' => true)));

        $this->assertTrue($filesystem->ftp->delete('test.txt'));

        $this->setExpectedException('\Gaufrette\Exception\FileNotFound');
        $filesystem->ftp->read('test.txt');
    }

    public function testS3Adapter()
    {
        $filesystem = $this->getFilesystem();

        $this->assertNotNull($filesystem->s3->write('test.txt', 'Example s3 content', true));
        $this->assertNotNull($filesystem->s3->checksum('test.txt'));
        $this->assertEquals('Example s3 content', $filesystem->s3->read('test.txt'));

        $this->assertEquals('/test.txt', $filesystem->s3->getUrl('test.txt', array('relative' => true)));
        $this->assertEquals('http://test.s3.amazonaws.com/test.txt', $filesystem->s3->getUrl('test.txt'));

        $this->assertTrue($filesystem->s3->delete('test.txt'));

        $this->setExpectedException('\Gaufrette\Exception\FileNotFound');
        $filesystem->s3->read('test.txt');
    }
}
 