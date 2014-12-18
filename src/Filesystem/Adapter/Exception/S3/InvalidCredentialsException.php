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

namespace Vegas\Filesystem\Adapter\Exception\S3;

use Vegas\Filesystem\Exception as FilesystemException;

/**
 * Class InvalidCredentialsException
 * @package Vegas\Filesystem\Adapter\Exception
 */
class InvalidCredentialsException extends FilesystemException
{
    protected $message = 'Amazon S3 credentials are invalid';
}
 