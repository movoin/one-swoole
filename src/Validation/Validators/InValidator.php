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

use One\Support\Helpers\Assert;
use One\Validation\Exceptions\ValidationException;

class InValidator extends AbstractValidator
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
        if (! isset($parameters['range'])) {
            throw new ValidationException('允许内容必须设置');
        }

        if (Assert::oneOf($attributes[$name], (array) $parameters['range'])) {
            return true;
        }

        $this->addError($name, $parameters, '%s 不在允许范围');

        return false;
    }
}
