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

namespace Vegas\Filesystem\Adapter;

use Aws\S3\S3Client;
use Gaufrette\Adapter\AwsS3 as GaufretteAwsS3;
use Vegas\Filesystem\Adapter\Exception\S3\InvalidBucketException;
use Vegas\Filesystem\Adapter\Exception\S3\InvalidCredentialsException;
use Vegas\Filesystem\AdapterInterface;

/**
 * Class S3
 *
 * @use https://github.com/KnpLabs/Gaufrette/blob/master/src/Gaufrette/Adapter/AwsS3.php
 * @use https://github.com/aws/aws-sdk-php/blob/master/src/Aws/S3/S3Client.php
 * @see https://github.com/KnpLabs/Gaufrette/#using-amazon-s3
 * @package Vegas\Filesystem\Adapter
 */
class S3 extends GaufretteAwsS3 implements AdapterInterface
{

    /**
     * Prepares adapter instance
     *
     * @param array $config
     * @throws Exception\S3\InvalidBucketException
     * @throws Exception\S3\InvalidCredentialsException
     * @return \Vegas\Filesystem\Adapter\S3|\Vegas\Filesystem\AdapterInterface
     */
    public static function setup($config)
    {
        if (!isset($config['key']) || empty($config['key'])) {
            throw new InvalidCredentialsException();
        }
        if (!isset($config['secret']) || empty($config['secret'])) {
            throw new InvalidCredentialsException();
        }

        //instantiate Amazon AWS S3 client
        $service = S3Client::factory([
            'key' => $config['key'],
            'secret' => $config['secret'],
            'scheme'    =>  !isset($config['scheme']) ? 'https' : 'http'
        ]);

        if (!isset($config['bucket']) || empty($config['bucket'])) {
            throw new InvalidBucketException();
        }
        $client = new self($service, $config['bucket']);

        if (isset($config['region'])) {
            $client->getService()->setRegion($config['region']);
        }

        return $client;
    }

    /**
     * @return S3Client
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Returns the url for file
     * By default absolute path to file is returned
     * Otherwise when $options array contain key `relative` set as true, relative path will be returned
     *
     * @param $key
     * @param array $options
     * @return mixed
     */
    public function getUrl($key, array $options = [])
    {
        $url = parent::getUrl($key, $options);

        if (isset($options['relative']) && $options['relative']) {
            $url = parse_url($url, PHP_URL_PATH);
        }

        return $url;
    }
}
 