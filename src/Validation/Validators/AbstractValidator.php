<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Validation\Validators
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Validation\Validators;

use One\Validation\Validator;
use One\Validation\Contracts\Validator as ValidatorInterface;
use One\Support\Helpers\Assert;

abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * 校验器
     *
     * @var \One\Validation\Validator
     */
    private $validator;

    /**
     * 构造
     *
     * @param  \One\Validation\Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * 执行校验规则
     *
     * @param  array  $attributes
     * @param  string $name
     * @param  array  $parameters
     *
     * @return bool
     */
    public function __invoke(array $attributes, string $name, array $parameters = []): bool
    {
        return (bool) $this->validate($attributes, $name, $parameters);
    }

    /**
     * 写入错误信息
     *
     * @param  string $name
     * @param  array  $parameters
     * @param  string $message
     */
    protected function addError(string $name, array $parameters, string $message)
    {
        if (Assert::stringNotEmpty($parameters['message'])) {
            $message = trim($parameters['message']);
        }

        $this->validator->addError(sprintf($message, $name));
    }

    /**
     * 校验规则
     *
     * @param  array  $attributes
     * @param  string $name
     * @param  array  $parameters
     *
     * @return bool
     */
    abstract protected function validate(array $attributes, string $name, array $parameters = []): bool;
}
