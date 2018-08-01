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

class BetweenValidator extends AbstractValidator
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
        if (! isset($parameters['min'])) {
            throw new ValidationException('允许最小值必须设置');
        }

        if (! isset($parameters['max'])) {
            throw new ValidationException('允许最大值必须设置');
        }

        if (Assert::range($attributes[$name], $parameters['min'], $parameters['max'])) {
            return true;
        }

        $this->addError(
            $name,
            $parameters,
            "%s 必须于 {$parameters['min']} 与 {$parameters['max']} 之间"
        );

        return false;
    }
}
