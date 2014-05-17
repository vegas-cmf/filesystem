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

/**
 * Interface AdapterInterface
 * @package Vegas\Filesystem
 */
interface AdapterInterface
{
    /**
     * Reads the content of the file
     *
     * @param string $key
     *
     * @return string|boolean if cannot read content
     */
    public function read($key);

    /**
     * Writes the given content into the file
     *
     * @param string $key
     * @param string $content
     *
     * @return integer|boolean The number of bytes that were written into the file
     */
    public function write($key, $content);

    /**
     * Indicates whether the file exists
     *
     * @param string $key
     *
     * @return boolean
     */
    public function exists($key);

    /**
     * Returns an array of all keys (files and directories)
     *
     * @return array
     */
    public function keys();

    /**
     * Returns the last modified time
     *
     * @param string $key
     *
     * @return integer|boolean An UNIX like timestamp or false
     */
    public function mtime($key);

    /**
     * Deletes the file
     *
     * @param string $key
     *
     * @return boolean
     */
    public function delete($key);

    /**
     * Renames a file
     *
     * @param string $sourceKey
     * @param string $targetKey
     *
     * @return boolean
     */
    public function rename($sourceKey, $targetKey);

    /**
     * Check if key is directory
     *
     * @param string $key
     *
     * @return boolean
     */
    public function isDirectory($key);

    /**
     * Returns the url for file
     * By default absolute path to file is returned
     * Otherwise when $options array contain key `relative` set as true, relative path will be returned
     *
     * @param $key
     * @param array $options
     * @internal param bool $absolute
     * @return mixed
     */
    public function getUrl($key, array $options = array());

    /**
     * Prepares adapter instance
     *
     * @param array $config
     *
     * @return AdapterInterface
     */
    public static function setup($config);
}
 