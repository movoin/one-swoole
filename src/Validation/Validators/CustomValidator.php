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

class CustomValidator extends AbstractValidator
{
    /**
     * 自定义回调
     *
     * @var callable
     */
    protected $callback;

    /**
     * 从数组回调创建自定义校验器
     *
     * @param  \One\Validation\Validator    $validator
     * @param  array                        $callback
     *
     * @return self
     */
    public static function createFromArray(Validator $validator, array $callback): self
    {
        $self = new static($validator);

        $self->setCallback(function (array $attributes, string $name, array $parameters) use ($callback) {
            return call_user_func_array($callback, [
                $attributes,
                $name,
                $parameters
            ]);
        });

        return $self;
    }

    /**
     * 从回调创建自定义校验器
     *
     * @param  \One\Validation\Validator    $validator
     * @param  callable                     $callback
     *
     * @return self
     */
    public static function createFromClosure(Validator $validator, callable $callback): self
    {
        $self = new static($validator);

        $self->setCallback(function (array $attributes, string $name, array $parameters) use ($callback) {
            return $callback($attributes, $name, $parameters);
        });

        return $self;
    }

    /**
     * 设置自定义回调
     *
     * @param callable $callback
     */
    protected function setCallback(callable $callback)
    {
        $this->callback = $callback;
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
    protected function validate(array $attributes, string $name, array $parameters = []): bool
    {
        $callback = $this->callback;

        if (($result = $callback($attributes, $name, $parameters)) === false) {
            $this->addError($name, $parameters, 'verification failed');
        }

        unset($callback);

        return $result;
    }
}
