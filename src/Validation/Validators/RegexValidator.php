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

use One\Validation\Exceptions\ValidationException;

class RegexValidator extends AbstractValidator
{
    /**
     * 校验规则
     *
     * @param  array  $attributes
     * @param  string $name
     * @param  array  $parameters
     *
     * @return bool
     * @throws \One\Validation\Exceptions\ValidationException
     */
    protected function validate(array $attributes, string $name, array $parameters): bool
    {
        if (! isset($parameters['pattern'])) {
            throw new ValidationException('匹配正则必须填写');
        }

        if (preg_match($parameters['pattern'], $attributes[$name])) {
            return true;
        }

        $this->addError($name, $parameters, '%s 匹配错误');

        return false;
    }
}
