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

class InstanceOfValidator extends AbstractValidator
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
        if (! isset($parameters['of'])) {
            throw new ValidationException('实现接口必须设置');
        }

        if (Assert::instanceOfAny($attributes[$name], (array) $parameters['of'])) {
            return true;
        }

        $this->addError($name, $parameters, '%s 未实现特定接口');

        return false;
    }
}
