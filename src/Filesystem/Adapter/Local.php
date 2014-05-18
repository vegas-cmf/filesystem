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

use Vegas\Filesystem\Adapter\Exception\Local\InvalidDirectoryException;
use Vegas\Filesystem\AdapterInterface;
use Gaufrette\Adapter\Local as GaufretteLocal;

/**
 * Class Local
 * @use https://github.com/KnpLabs/Gaufrette/blob/master/src/Gaufrette/Adapter/Local.php
 * @see https://github.com/KnpLabs/Gaufrette/#setup-your-filesystem
 * @package Vegas\Filesystem\Adapter
 */
class Local extends GaufretteLocal implements AdapterInterface
{

    /**
     * Prepares adapter instance
     *
     * @param array $config
     * @throws Exception\Local\InvalidDirectoryException
     * @return AdapterInterface
     */
    public static function setup($config)
    {
        if (!isset($config['directory'])) {
            throw new InvalidDirectoryException();
        }
        $localAdapter = new self($config['directory']);

        return $localAdapter;
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
        $url = $this->computePath($key);

        if (isset($options['relative']) && $options['relative']) {
            $url = str_replace($_SERVER['DOCUMENT_ROOT'], '', $url);
        }

        return $url;
    }
}
 