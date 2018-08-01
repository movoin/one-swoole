<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Context\Contracts
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Context\Contracts;

use One\Support\Contracts\Arrayable;
use One\Support\Contracts\Jsonable;

interface Payload extends Arrayable, Jsonable
{
    /**
     * 根据指定状态码克隆一个新的基本数据
     *
     * @param  int $code
     *
     * @return self
     */
    public function withCode(int $code): self;

    /**
     * 获得基本数据中的状态码
     *
     * @return int
     */
    public function getCode(): int;

    /**
     * 根据指定的消息数组克隆一个新的基本数据
     *
     * @param  string $message
     *
     * @return self
     */
    public function withMessage(string $message): self;

    /**
     * 获得基本数据中的消息数组
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * 根据指定的输出数组克隆一个新的基本数据
     *
     * @param  mixed $data
     *
     * @return self
     */
    public function withData($data): self;

    /**
     * 获得基本数据中的输出数组
     *
     * @return mixed
     */
    public function getData();

    /**
     * 根据指定的错误数组克隆一个新的基本数据
     *
     * @param  string $error
     *
     * @return self
     */
    public function withError(string $error): self;

    /**
     * 获得基本数据中的错误数组
     *
     * @return string
     */
    public function getError(): string;

    /**
     * 根据指定的选项数组克隆一个新的基本数据
     *
     * @param  array $options
     *
     * @return self
     */
    public function withOptions(array $options): self;

    /**
     * 获得基本数据中的选项数组
     *
     * @return array
     */
    public function getOptions(): array;
}
