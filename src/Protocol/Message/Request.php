<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Message
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Message;

use InvalidArgumentException;
use One\Protocol\Factory;
use One\Protocol\Contracts\Request as RequestInterface;
use One\Protocol\Traits\HasMessage;
use One\Protocol\Traits\HasProtocol;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;

class Request implements RequestInterface
{
    use HasMessage,
        HasProtocol;

    /**
     * 请求方法
     *
     * @var string
     */
    protected $method;
    /**
     * 请求 URI 对象
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $uri;
    /**
     * 请求 URI 目标 (Path + QueryString)
     *
     * @var string
     */
    protected $requestTarget;
    /**
     * 查询参数
     *
     * @var array
     */
    protected $queryParams;
    /**
     * 请求 Cookies
     *
     * @var array
     */
    protected $cookies;
    /**
     * 服务器环境变量
     *
     * @var array
     */
    protected $serverParams;
    /**
     * 请求参数
     *
     * @var array
     */
    protected $attributes;
    /**
     * 标识请求内容是否已经解析
     *
     * @var bool
     */
    protected $bodyParsed = false;
    /**
     * 请求内容解析器列表 (e.g., url-encoded, JSON, XML, multipart)
     *
     * @var array
     */
    protected $bodyParsers = [];
    /**
     * 上传文件列表
     *
     * @var array
     */
    protected $uploadedFiles;
}
