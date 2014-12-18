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

namespace Vegas\Filesystem;

use Gaufrette\Filesystem as GaufretteFilesystem;

/**
 * Class Wrapper
 * @package Vegas\Filesystem
 */
class Wrapper extends GaufretteFilesystem
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

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
        return $this->adapter->getUrl($key, $options);
    }
}
 