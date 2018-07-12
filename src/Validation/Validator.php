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
use One\Validation\Contracts\Validator as ValidatorInterface;
use One\Validation\Exceptions\ValidationException;

class Validator
{
    /**
     * 内建校验器
     *
     * @var array
     */
    protected $builtinValidators = [];
    /**
     * 自定义校验器
     *
     * @var array
     */
    protected $customValidators = [];
    /**
     * 校验器实例
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
     * 添加校验规则
     *
     * @param array $rule
     */
    public function addRule(array $rule)
    {
        $this->rules[] = $rule;
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
     * 获得当前场景校验规则
     *
     * @return array
     */
    public function getScenarios(): array
    {
        if ($this->scenario === null) {
            return $this->rules;
        }

        $rules = [];

        foreach ($this->rules as $rule) {
            if (! isset($rule['on']) && ! isset($rule['except'])) {
                $rules[] = $rule;
            } elseif (isset($rule['on']) && $this->matchScenario($rule['on'])) {
                unset($rule['on']);
                $rules[] = $rule;
            } elseif (isset($rule['except']) && $this->matchScenario($rule['except'])) {
                unset($rule['except']);
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    /**
     * 校验数据
     *
     * @param  array  $attributes
     *
     * @return bool
     * @throws \One\Validation\Exceptions\ValidationException
     */
    public function validate(array $attributes): bool
    {
        $rules = $this->getScenarios();

        foreach ($rules as $rule) {
            $names = array_map('trim', explode(',', $rule[0]));
            array_walk($names, function ($name) use ($attributes, $rule) {
                $this->validateValue($attributes, $name, $rule);
            });
            unset($names);
        }

        unset($rules);

        return count($this->errors) === 0;
    }

    /**
     * 校验值（此方法忽略场景）
     *
     * @param  array  $attributes
     * @param  string $name
     * @param  array  $rule
     *
     * @return bool
     * @throws \One\Validation\Exceptions\ValidationException
     */
    public function validateValue(array $attributes, string $name, array $rule): bool
    {
        // 删除开始的字段名
        array_shift($rule);
        // 取出校验器识别
        $validatorName = array_shift($rule);
        // 创建校验器
        $validator = $this->createValidator($validatorName);

        return $validator($attributes, $name, $rule);
    }

    /**
     * 判断是否校验器
     *
     * @param  string $name
     *
     * @return bool
     */
    public function hasValidator(string $name): bool
    {
        return isset($this->builtinValidators[$name]) ||
               isset($this->customValidators[$name]) ||
               isset($this->validators[$name]);
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
     *
     * // 自定义校验器 3
     * $validator->addValidator('unique', [
     *     Unique::class,
     *     'except' => [ 'delete' ]
     * ]);
     * ```
     *
     * // 自定义校验器 4
     * $validator->addValidator('unique', [
     *     function ($attributes, $name, $parameters) {
     *         // do something
     *         return true;
     *     },
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
        if ($this->hasValidator($name)) {
            throw new ValidationException("Validator `{$name}` already exists");
        } elseif (! $this->isCustomValidator($validator)) {
            throw new ValidationException(
                "`Validator` expects to be 'callable', 'callable array' or 'namespace string'"
            );
        }

        $this->customValidators[$name] = $validator;
    }

    /**
     * 创建校验器实例
     *
     * @param  string $name
     *
     * @return \One\Validation\Contracts\Validator
     * @throws \One\Validation\Exceptions\ValidationException
     */
    public function createValidator(string $name): ValidatorInterface
    {
        if (isset($this->validators[$name])) {
            return $this->validators[$name];
        }

        if (($validator = $this->getValidatorDefine($name)) !== false) {
            if (Assert::string($validator)) {
                $this->validators[$name] = Reflection::newInstance(
                    '\\One\\Valication\\Validators\\' . $validator,
                    $this
                );
            } elseif (Assert::array($validator)) {
                $this->validators[$name] = CustomValidator::createFromArray($this, $validator);
            } else {
                $this->validators[$name] = CustomValidator::createFromCallback($this, $validator);
            }

            return $this->validators[$name];
        }

        throw new ValidationException(sprintf('Validator "%s" not found', $name));
    }

    /**
     * 获得校验器定义
     *
     * @param  string $name
     *
     * @return array|string|callable|false
     */
    public function getValidatorDefine(string $name)
    {
        if (isset($this->builtinValidators[$name])) {
            return $this->builtinValidators[$name];
        }

        if (isset($this->customValidators[$name])) {
            return $this->customValidators[$name];
        }

        return false;
    }

    /**
     * 判断是否自定义校验器
     *
     * @param  callable|string|array $validator
     *
     * @return bool
     */
    protected function isCustomValidator($validator): bool
    {
        return Assert::callable($validator) ||
               Assert::callableArray($validator) ||
               Assert::namespace($validator);
    }

    /**
     * 匹配场景
     *
     * @param  string|array $scenarios
     *
     * @return bool
     */
    private function matchScenario($scenarios): bool
    {
        if (Assert::stringNotEmpty($scenarios)) {
            $scenarios = explode(',', $scenarios);
        }

        $scenarios = array_map('trim', $scenarios);

        return Assert::oneOf($this->scenario, $scenarios);
    }
}
