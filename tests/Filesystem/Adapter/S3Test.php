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

use Vegas\Filesystem\Adapter\S3;
use Vegas\Filesystem;

class S3Test extends \PHPUnit_Framework_TestCase
{
    protected $config = [
        'key'   =>  'fakekey',
        'secret'    =>  'fakesecret',
        'region'  =>  'eu-west-1',
        'bucket'    =>  'test',
        'scheme'    =>  'http'
    ];

    public function testShouldCreateS3AdapterInstanceWithGivenConfiguration()
    {
        $s3 = \Vegas\Filesystem\Adapter\S3::setup($this->config);

        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\S3', $s3);
    }

    public function testShouldThrowExceptionWhenKeyIsInvalid()
    {
        try {
            \Vegas\Filesystem\Adapter\S3::setup([
                'key' => false,
                'secret' => $this->config['secret']
            ]);

            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Filesystem\Adapter\Exception\S3\InvalidCredentialsException', $e);
        }
    }

    public function testShouldThrowExceptionWhenSecretIsInvalid()
    {
        try {
            \Vegas\Filesystem\Adapter\S3::setup([
                'key' => $this->config['key'],
                'secret' => false
            ]);

            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Filesystem\Adapter\Exception\S3\InvalidCredentialsException', $e);
        }
    }

    public function testShouldThrowExceptionWhenBucketIsInvalid()
    {
        try {
            \Vegas\Filesystem\Adapter\S3::setup([
                'key' => $this->config['key'],
                'secret' => $this->config['bucket']
            ]);

            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Filesystem\Adapter\Exception\S3\InvalidBucketException', $e);
        }
    }

    protected function mockS3Client($filePath)
    {
        $s3MockBuilder = $this->getMockBuilder('\Aws\S3\S3Client')
            ->setMethods(['getObjectUrl'])
            ->disableOriginalConstructor();
        $awsS3 = $s3MockBuilder->getMock();
        $awsS3->expects($this->any())
            ->method('getObjectUrl')
            ->will($this->returnValue(
                $filePath
            ));
        $s3 = new S3($awsS3, $this->config['bucket']);

        return $s3;
    }

    public function testShouldReturnAbsolutePathToFile()
    {
        $fileName = 'test.txt';
        $filePath = sprintf('%s://%s.s3.amazonaws.com/%s',
            $this->config['scheme'],
            $this->config['bucket'],
            $fileName
        );

        $s3 = $this->mockS3Client($filePath);

        $this->assertEquals($filePath, $s3->getUrl($fileName));
    }

    public function testShouldReturnLocalAdapterWhenDefaultNoSpecified()
    {
        $filesystem = new Filesystem([
            'local' => [
                'directory' => TESTS_ROOT_DIR
            ]
        ]);
        $this->assertInstanceOf('\Vegas\Filesystem\Adapter\Local', $filesystem->default->getAdapter());
    }

    public function testShouldReturnRelativePathToFile()
    {
        $fileName = 'test.txt';
        $filePath = sprintf('%s://%s.s3.amazonaws.com/%s',
            $this->config['scheme'],
            $this->config['bucket'],
            $fileName
        );

        $s3 = $this->mockS3Client($filePath);

        $this->assertEquals(parse_url($filePath, PHP_URL_PATH), $s3->getUrl($fileName, ['relative' => true]));
    }
}
 