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

use Gaufrette\Adapter\Ftp as GaufretteFtp;
use Gaufrette\Util\Path;
use Vegas\Filesystem\Adapter\Exception\Ftp\InvalidHostException;
use Vegas\Filesystem\Adapter\Exception\Ftp\InvalidPathException;
use Vegas\Filesystem\AdapterInterface;

/**
 * Class Ftp
 *
 * @use https://github.com/KnpLabs/Gaufrette/blob/master/src/Gaufrette/Adapter/Ftp.php
 * @see https://github.com/KnpLabs/Gaufrette
 * @package Vegas\Filesystem\Adapter
 */
class Ftp extends GaufretteFtp implements AdapterInterface
{

    /**
     * Prepares adapter instance
     *
     * @param array $config
     * @throws Exception\Ftp\InvalidHostException
     * @throws Exception\Ftp\InvalidPathException
     * @return AdapterInterface
     */
    public static function setup($config)
    {
        if (!isset($config['host']) || empty($config['host'])) {
            throw new InvalidHostException();
        }
        if (!isset($config['path']) || empty($config['path'])) {
            throw new InvalidPathException();
        }
        $config['options'] = !isset($config['options']) ? [] : $config['options'];

        $ftpClient = new self($config['path'], $config['host'], $config['options']);

        return $ftpClient;
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
        $absolutePathPattern = ':protocol://:username@:host:pwd/:directory/:file';

        $protocol = $this->ssl ? 'ftps' : 'ftp';
        $pwd = ftp_pwd($this->connection);
        $url = strtr($absolutePathPattern, [
            ':protocol' =>  $protocol,
            ':username' =>  $this->username,
            ':host' =>  $this->host,
            ':pwd'  =>  $pwd,
            ':directory'    =>  $this->directory,
            ':file' =>  $key
        ]);

        if (isset($options['relative']) && $options['relative']) {
            $relativePathPattern = ':pwd/:directory/:file';
            $url = strtr($relativePathPattern, [
                ':pwd'   =>  $pwd,
                ':directory'    =>  $this->directory,
                ':file' =>  $key
            ]);
        }

        return Path::normalize($url);
    }
}
 