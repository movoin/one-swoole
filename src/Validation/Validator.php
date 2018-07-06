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
use One\Validation\Exceptions\ValidationException;

class Validator
{
    /**
     * 内建约束条件
     *
     * @var array
     */
    protected $constraints = [];
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
     * 添加限定规则
     *
     * @param string        $name
     * @param string|array  $constraint
     *
     * @throws \One\Validation\Exceptions\ValidationException
     */
    public function addConstraint(string $name, $constraint)
    {
        if (isset($this->constraints[$name])) {
            throw new ValidationException("Constraint `{$name}` already exists");
        } elseif (! Assert::stringNotEmpty($constraint) || ! Assert::array($constraint)) {
            throw new ValidationException("`Validator::addConstraint()` expects parameter 2 to be 'array' or 'string'");
        } elseif (Assert::array($constraint) && ! $this->isCustomConstraint($constraint)) {
            throw new ValidationException("`Validator::addConstraint()` expects parameter 2 to be 'callable array'");
        }

        $this->constraints[$name] = $constraint;
    }

    /**
     * 判断是否为自定义约束规则
     *
     * @param  array $constraint
     *
     * @return bool
     */
    protected function isCustomConstraint(array $constraint): bool
    {
        if (count($constraint) !== 2) {
            return false;
        }

        return method_exists($constraint[0], $constraint[1]);
    }
}
