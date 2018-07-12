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

class RequiredValidator extends AbstractValidator
{
    /**
     * 未定义字段则放行，默认为 true，当规则校验健名未设置时，自动返回 true 通过校验
     *
     * @var bool
     */
    protected $ignoreUndefined = false;

    /**
     * 校验规则
     *
     * @param  array  $attributes
     * @param  string $name
     * @param  array  $parameters
     *
     * @return bool
     */
    protected function validate(array $attributes, string $name, array $parameters): bool
    {
        if (isset($attributes[$name]) && ! empty($attributes[$name])) {
            return true;
        }

        $this->addError($name, $parameters, '%s 必须填写');

        return false;
    }
}
