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

class LengthValidator extends AbstractValidator
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
        if (! isset($parameters['is']) &&
            ! isset($parameters['min']) &&
            ! isset($parameters['max'])
        ) {
            throw new ValidationException('长度限制条件必须填写');
        }

        $len = mb_strlen($attributes[$name]);

        if (isset($parameters['is']) && $len === (int) $parameters['is']) {
            return true;
        }

        if (isset($parameters['min']) &&
            isset($parameters['max']) &&
            $len >= (int) $parameters['min'] &&
            $len <= (int) $parameters['max']
        ) {
            return true;
        } elseif (isset($parameters['min']) && $len >= (int) $parameters['min']) {
            return true;
        } elseif (isset($parameters['max']) && $len <= (int) $parameters['max']) {
            return true;
        }

        $this->addError($name, $parameters, '%s 长度不符合');

        return false;
    }
}
