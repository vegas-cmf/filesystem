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

class LocalTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldCreateLocalAdapterInstanceWithGivenConfiguration()
    {
        $local = \Vegas\Filesystem\Adapter\Local::setup([
            'directory' => TESTS_ROOT_DIR . '/fixtures/tmp'
        ]);

        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\Local', $local);
    }

    public function testShouldThrowExceptionWhenDirectoryIsInvalid()
    {
        $exception = null;
        try {
            \Vegas\Filesystem\Adapter\Local::setup([
                'directory' => null
            ]);
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\Exception\Local\InvalidDirectoryException', $exception);
    }

    public function testShouldReturnAbsolutePathToFile()
    {
        $local = \Vegas\Filesystem\Adapter\Local::setup([
            'directory' => TESTS_ROOT_DIR . '/fixtures/tmp'
        ]);

        $local->write('test.txt', 'Test file');

        $this->assertFileExists($local->getUrl('test.txt'));
        $this->assertEquals('Test file', $local->read('test.txt'));

        $local->delete('test.txt');
    }

    public function testShouldReturnRelativePathToFile()
    {
        $local = \Vegas\Filesystem\Adapter\Local::setup([
            'directory' => TESTS_ROOT_DIR . '/fixtures/tmp'
        ]);

        $local->write('test.txt', 'Test file');

        $_SERVER['DOCUMENT_ROOT'] = TESTS_ROOT_DIR;
        $this->assertEquals(
            '/fixtures/tmp/test.txt',
            $local->getUrl('test.txt', ['relative' => true])
        );

        $local->delete('test.txt');
    }
}
 