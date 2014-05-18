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

namespace Vegas\Filesystem\Adapter;

use Gaufrette\Util\Path;
use Vegas\Filesystem\Adapter\Exception\Ftp\InvalidHostException;
use Vegas\Filesystem\Adapter\Exception\Ftp\InvalidPathException;
use Vegas\Filesystem\AdapterInterface;
use Gaufrette\Adapter\Ftp as GaufretteFtp;

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
        if (!isset($config['path'])) {
            throw new InvalidPathException();
        }
        if (!isset($config['host'])) {
            throw new InvalidHostException();
        }
        $config['options'] = !isset($config['options']) ? array() : $config['options'];

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
    public function getUrl($key, array $options = array())
    {
        $absolutePathPattern = ':protocol://:username@:host:pwd/:directory/:file';

        $protocol = $this->ssl ? 'ftps' : 'ftp';
        $pwd = ftp_pwd($this->connection);
        $url = strtr($absolutePathPattern, array(
            ':protocol' =>  $protocol,
            ':username' =>  $this->username,
            ':host' =>  $this->host,
            ':pwd'  =>  $pwd,
            ':directory'    =>  $this->directory,
            ':file' =>  $key
        ));

        if (isset($options['relative']) && $options['relative']) {
            $relativePathPattern = ':pwd/:directory/:file';
            $url = strtr($relativePathPattern, array(
                ':pwd'   =>  $pwd,
                ':directory'    =>  $this->directory,
                ':file' =>  $key
            ));
        }

        return Path::normalize($url);
    }
}
 