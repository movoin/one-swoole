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

class NotNullValidator extends AbstractValidator
{
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
        if (! is_null($attributes[$name])) {
            return true;
        }

        $this->addError($name, $parameters, '%s 不能为 Null');

        return false;
    }
}
