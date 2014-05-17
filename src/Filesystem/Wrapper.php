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

namespace Vegas\Filesystem;

use Gaufrette\Filesystem as GaufretteFilesystem;

class Wrapper extends GaufretteFilesystem
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    public function getUrl($key, array $options = array())
    {
        return $this->adapter->getUrl($key, $options);
    }
}
 