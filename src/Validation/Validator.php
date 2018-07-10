<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Validation
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Validation;

use One\Support\Helpers\Assert;
use One\Support\Helpers\Reflection;
use One\Validation\Exceptions\ValidationException;

class Validator
{
    /**
     * 内建校验器
     *
     * @var array
     */
    protected $validators = [];
    /**
     * 校验规则
     *
     * @var array
     */
    protected $rules = [];
    /**
     * 错误信息
     *
     * @var array
     */
    protected $errors = [];
    /**
     * 规则适用场景
     *
     * @var string
     */
    protected $scenario;

    /**
     * 构造
     *
     * @param array $rules
     */
    public function __construct(array $rules = [])
    {
        $this->addRules($rules);
    }

    /**
     * 重置
     *
     * @return void
     */
    public function reset()
    {
        $this->rules    = [];
        $this->errors   = [];
        $this->scenario = null;
    }

    /**
     * 添加校验规则
     *
     * @param array $rule
     */
    public function addRule(array $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * 批量添加校验规则
     *
     * @param array $rules
     */
    public function addRules(array $rules)
    {
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }
    }

    /**
     * 添加错误信息
     *
     * @param string $error
     */
    public function addError(string $error)
    {
        $this->errors[] = $error;
    }

    /**
     * 获得所有错误信息
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * 获得最后一条错误信息
     *
     * @return string
     */
    public function getLastError(): string
    {
        if (empty($this->errors)) {
            return '';
        }

        return array_shift($this->errors);
    }

    /**
     * 设置校验场景
     *
     * @param string $scenario
     */
    public function setScenario(string $scenario)
    {
        $this->scenario = $scenario;
    }

    /**
     * 创建校验器实例
     *
     * @param  string|array $validator
     *
     * @return \One\Validation\Validator
     */
    public function ensureValidator($validator): Validator
    {
        if ($this->isValidator($validator)) {
            if ($this->isCustomValidator($validator)) {
                return function ($attributes, $name, $parameters) use ($validator) {
                    if (! call_user_func_array($validator, [
                        $attributes,
                        $name,
                        $parameters
                    ])) {
                        if (isset($parameters['message'])) {
                            $this->addError($parameters['message']);
                        } else {
                            $this->addError($validator[1] . ' verify failure');
                        }
                    }
                };
            }

            if (Assert::stringNotEmpty($this->validators[$validator])) {
                $this->validators[$validator] = Reflection::newInstance(
                    '\\One\\Valication\\Validators\\' . $this->validators[$validator],
                    $this
                );
            }
        }

        throw new ValidationException(((string) $validator) . ' not exists');
    }

    /**
     * 添加校验器
     *
     * ```
     * // 自定义校验器 1
     * $validator->addValidator('unique', [
     *     [$this, 'unique'],
     *     'on' => [ 'create', 'update' ]
     * ]);
     *
     * // 自定义校验器 2
     * $validator->addValidator('unique', [
     *     '\\App\\Validators\\Unique',
     *     'except' => [ 'delete' ]
     * ]);
     * ```
     *
     * @param string        $name
     * @param string|array  $validator
     *
     * @throws \One\Validation\Exceptions\ValidationException
     */
    public function addValidator(string $name, $validator)
    {
        if (isset($this->validators[$name])) {
            throw new ValidationException("Validator `{$name}` already exists");
        } elseif (! $this->isValidator($validator)) {
            throw new ValidationException(
                "`Validator::addValidator()` expects parameter 2 to be 'callable array' or 'string'"
            );
        }

        $this->validators[$name] = $validator;
    }

    /**
     * 判断是否校验器
     *
     * @param  string|array $validator
     *
     * @return bool
     */
    protected function isValidator($validator): bool
    {
        return Assert::namespace($validator) || $this->isCustomValidator($validator);
    }

    /**
     * 判断是否为自定义校验器
     *
     * @param  array $validator
     *
     * @return bool
     */
    protected function isCustomValidator(array $validator): bool
    {
        if (count($validator) !== 2) {
            return false;
        } elseif (! Assert::object($validator[0])) {
            return false;
        }

        return method_exists($validator[0], $validator[1]);
    }
}
