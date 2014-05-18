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

namespace Vegas;

use Vegas\Filesystem\Wrapper as FilesystemWrapper;
use Phalcon\DI\InjectionAwareInterface;
use Vegas\DI\InjectionAwareTrait;
use Vegas\Filesystem\Exception\AdapterNotFoundException;

/**
 * Class Manager
 *
 * Simply filesystem abstraction layer based on Gaufrette library
 *
 * @see https://github.com/KnpLabs/Gaufrette/
 * @package Vegas
 */
class Filesystem implements InjectionAwareInterface
{
    use InjectionAwareTrait;

    /**
     * List of already initialized adapters
     * It prevents from creating adapter instance more than one time per request
     *
     * @var array
     */
    private $initializedAdapter = array();

    /**
     * Filesystem configuration
     *
     * @var array
     */
    private $config = array();

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Reading the property from the filesystem manager object
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getAdapter($name);
    }

    /**
     * Returns the instance of adapter by its name
     *
     * @param $adapterName
     * @return mixed
     */
    public function getAdapter($adapterName)
    {
        //obtains a default filesystem adapter
        if ('default' === $adapterName) {
            if (!isset($this->config['default'])) {
                $adapterName = 'local';
            } else {
                $adapterName = $this->config['default'];
            }
        }
        if (!isset($this->initializedAdapter[$adapterName])) {
            //creates instance of adapter
            $adapterInstance = $this->resolveAdapterInstance($adapterName);
            //creates filesystem using created adapter
            $filesystem = new FilesystemWrapper($adapterInstance);
            $this->initializedAdapter[$adapterName] = $filesystem;
        }

        return $this->initializedAdapter[$adapterName];
    }

    /**
     * Exchange adapter configuration. From this moment the adapter will be using new configuration
     *
     * @param $adapterName
     * @param $config
     * @return $this
     */
    public function setAdapterConfig($adapterName, $config)
    {
        $this->config[$adapterName] = $config;
        return $this;
    }

    /**
     * Resolves adapter instance using the classes from Adapter directory
     *
     * @param $adapterName
     * @return object
     * @throws Filesystem\Exception\AdapterNotFoundException
     */
    private function resolveAdapterInstance($adapterName)
    {
        $adapterNamespace = sprintf(__NAMESPACE__ . '\Filesystem\Adapter\\%s', ucfirst($adapterName));
        try {
            $reflectionClass = new \ReflectionClass($adapterNamespace);
            $adapterConfig = $this->getAdapterConfig($adapterName);

            $setupMethod = $reflectionClass->getMethod('setup');
            return $setupMethod->invokeArgs(null, array($adapterConfig));
        } catch (\ReflectionException $ex) {
            throw new AdapterNotFoundException($adapterName);
        }
    }

    /**
     * Returns configuration for indicated adapter
     *
     * @param $adapterName
     * @return array
     */
    private function getAdapterConfig($adapterName)
    {
        if (!isset($this->config[$adapterName])) {
            return array();
        }

        return $this->config[$adapterName];
    }
}
 