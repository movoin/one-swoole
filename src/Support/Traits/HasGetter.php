<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Support\Traits
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Support\Traits;

use Exception;

trait HasGetter
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
        $getter = 'get' . ucfirst($name);

        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (method_exists(get_parent_class(), '__get')) {
            return parent::__get($name);
        }

        unset($getter);

        throw new Exception("Undefined property: `{$name}`");
    }
}
