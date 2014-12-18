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

namespace Vegas\Tests\Filesystem\Adapter;

class FtpTest extends \PHPUnit_Framework_TestCase
{
    protected $config = [
        'path'  =>  './',
        'host'  =>  'localhost',
        'options'    => [
            'username' => 'ftp-user',
            'password' => 'test1234',
            'ssl' => false
        ]
    ];

    public function testShouldCreateFtpAdapterInstanceWithGivenConfiguration()
    {
        $ftp = \Vegas\Filesystem\Adapter\Ftp::setup($this->config);

        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\Ftp', $ftp);
    }

    public function testShouldThrowExceptionWhenPathIsInvalid()
    {
        $exception = null;
        try {
            \Vegas\Filesystem\Adapter\Ftp::setup([
                'path' => false,
                'host' => $this->config['host']
            ]);
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\Exception\Ftp\InvalidPathException', $exception);
    }

    public function testShouldThrowExceptionWhenHostIsInvalid()
    {
        $exception = null;
        try {
            \Vegas\Filesystem\Adapter\Ftp::setup([
                'path' => $this->config['path'],
                'host' => false
            ]);
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\Exception\Ftp\InvalidHostException', $exception);
    }

    public function testShouldReturnAbsolutePathToFile()
    {
        $ftp = \Vegas\Filesystem\Adapter\Ftp::setup($this->config);

        $ftp->write('test.txt', 'Test file');

        $url = $ftp->getUrl('test.txt');
        $this->assertContains('ftp://', $url);
        $this->assertContains($this->config['options']['username'], $url);
        $this->assertEquals('Test file', $ftp->read('test.txt'));

        $ftp->delete('test.txt');
    }

    public function testShouldReturnRelativePathToFile()
    {
        $ftp = \Vegas\Filesystem\Adapter\Ftp::setup($this->config);

        $ftp->write('test.txt', 'Test file');

        $this->assertEquals(
            '/home/ftp-user/test.txt',
            $ftp->getUrl('test.txt', ['relative' => true])
        );
        $this->assertFileExists($ftp->getUrl('test.txt', ['relative' => true]));

        $ftp->delete('test.txt');
    }
}
 