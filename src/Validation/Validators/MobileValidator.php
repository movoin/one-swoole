<?php
/**
 * 手机号类型
 *
 * @copyright   本软件和相关文档仅限安乐窝和/或其附属公司开发团队内部交流使用，
 *              并受知识产权法的保护。除非公司以适用法律明确授权，否则不得以任
 *              何形式、任何方式使用、拷贝、复制、翻译、广播、修改、授权、传播、
 *              分发、展示、执行、发布或显示本软件和相关 文档的任何部分。
 *
 * @package     ApiBucket\Support\Validation\Validators
 * @author      罗熠 <luoyi@anlewo.com>
 * @since       0.4
 */

namespace ApiBucket\Support\Validation\Validators;

use One\Support\Helpers\Assert;

class MobileValidator extends AbstractValidator
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
        if (Assert::mobile($attributes[$name])) {
            return true;
        }

        $this->addError($name, $parameters, '%s 必须为手机号码');

        return false;
    }
}
