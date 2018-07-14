<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Context
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Context;

use One\Context\Contracts\Payload as PayloadInterface;
use One\Support\Helpers\Json;

class Payload implements PayloadInterface
{
    /**
     * 状态码
     *
     * @var int
     */
    protected $code;
    /**
     * 状态消息
     *
     * @var string
     */
    protected $message;
    /**
     * 错误信息
     *
     * @var string
     */
    protected $error;
    /**
     * 数据
     *
     * @var mixed
     */
    protected $data;
    /**
     * 选项
     *
     * @var array
     */
    protected $options;

    /**
     * 根据指定状态码克隆一个新的基本数据
     *
     * @param  int $code
     *
     * @return self
     */
    public function withCode(int $code): self
    {
        if ($this->code === $code) {
            return $this;
        }

        $clone = clone $this;
        $clone->code = $code;

        return $clone;
    }

    /**
     * 获得基本数据中的状态码
     *
     * @return int
     */
    public function getCode(): int
    {
        return $this->code ?? 200;
    }

    /**
     * 根据指定的消息数组克隆一个新的基本数据
     *
     * @param  string $message
     *
     * @return self
     */
    public function withMessage(string $message): self
    {
        if ($this->message === $message) {
            return $this;
        }

        $clone = clone $this;
        $clone->message = $message;

        return $clone;
    }

    /**
     * 获得基本数据中的消息数组
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * 根据指定的输出数组克隆一个新的基本数据
     *
     * @param  mixed $data
     *
     * @return self
     */
    public function withData($data): self
    {
        if ($this->data === $data) {
            return $this;
        }

        $clone = clone $this;
        $clone->data = $data;

        return $clone;
    }

    /**
     * 获得基本数据中的输出数组
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 根据指定的错误数组克隆一个新的基本数据
     *
     * @param  string $error
     *
     * @return self
     */
    public function withError(string $error): self
    {
        if ($this->error === $error) {
            return $this;
        }

        $clone = clone $this;
        $clone->error = $error;

        return $clone;
    }

    /**
     * 获得基本数据中的错误数组
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * 根据指定的选项数组克隆一个新的基本数据
     *
     * @param  array $options
     *
     * @return self
     */
    public function withOptions(array $options): self
    {
        if ($this->options === $options) {
            return $this;
        }

        $clone = clone $this;
        $clone->options = $options;

        return $clone;
    }

    /**
     * 获得基本数据中的选项数组
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * 获得数组内容
     *
     * @return array
     */
    public function toArray(): array
    {
        $payload = [
            'code' => $this->getCode()
        ];

        if ($this->getMessage() !== null) {
            $payload['message'] = $this->getMessage();
        }

        if ($this->getError() !== null) {
            $payload['error'] = $this->getError();
        }

        if ($this->getData() !== null) {
            $payload['data'] = $this->getData();
        }

        if ($this->getOptions() !== null) {
            $payload += $this->getOptions();
        }

        return $payload;
    }

    /**
     * 获得 JSON 内容
     *
     * @return string
     */
    public function toJson(): string
    {
        return Json::decode($this->toArray());
    }
}
