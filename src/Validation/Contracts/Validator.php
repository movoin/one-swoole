<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Validation\Contracts
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Validation\Contracts;

interface Validator
{
    /**
     * 执行校验规则
     *
     * @param  array  $attributes
     * @param  string $name
     * @param  array  $parameters
     *
     * @return bool
     */
    public function __invoke(array $attributes, string $name, array $parameters = []): bool;
}
