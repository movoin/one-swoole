<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Traits
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Traits;

use Exception;

trait HasServerContainer
{
    /**
     * __get()
     *
     * @param  string $name
     *
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        if (method_exists($this, 'getServer') && $this->getServer()->has($name)) {
            return $this->getServer()->get($name);
        } elseif (method_exists(get_parent_class(), '__get')) {
            return parent::__get($name);
        }

        throw new Exception("Container identifier `{$name}` is not found.");
    }
}
