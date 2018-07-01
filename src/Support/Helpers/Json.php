<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Support\Helpers
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Support\Helpers;

use One\Support\Exceptions\JsonException;

final class Json
{
    /**
     * 对内容进行 JSON 编码
     *
     * @param  mixed  $value
     *
     * @return string
     * @throws \One\Support\Exceptions\JsonException
     */
    public static function encode($value): string
    {
        $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            | (defined('JSON_PRESERVE_ZERO_FRACTION') ? JSON_PRESERVE_ZERO_FRACTION : 0)

        $json = json_encode($value, $flags);

        if ($error = json_last_error()) {
            throw new JsonException(json_last_error_msg(), $error);
        }

        return $json;
    }

    /**
     * 对 JSON 进行解码
     *
     * @param  string $json
     *
     * @return mixed
     * @throws \One\Support\Exceptions\JsonException
     */
    public static function decode(string $json)
    {
        $value = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);

        if ($error = json_last_error()) {
            throw new JsonException(json_last_error_msg(), $error);
        }

        return $value;
    }
}
