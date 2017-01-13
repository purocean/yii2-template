<?php
namespace common\models;

use Yii;
use yii\base\Model;
use common\models\Logs;

/**
 * Login form
 */
class LoginForm extends Model
{
    const SCENARIO_QRLOGIN = 'qrlogin'; // 二维码登录场景
    const SCENARIO_CODELOGIN = 'codelogin'; // 微信回调 code 登录场景

    public $username;
    public $password;
    public $rememberMe = true;
    public $nonce = '';
    public $code = '';

    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required', 'on' => [self::SCENARIO_DEFAULT]],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean', 'on' => [self::SCENARIO_DEFAULT]],
            // password is validated by validatePassword()
            ['password', 'validatePassword', 'on' => [self::SCENARIO_DEFAULT]],

            [['nonce'], 'validateNonce', 'on' => [self::SCENARIO_QRLOGIN]],
            [['code'], 'validateCode', 'on' => [self::SCENARIO_CODELOGIN]],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '用户名或者密码不正确');
            }
        }
    }

    public function validateNonce($attribute, $params)
    {
        if ($nonce = trim($this->nonce)) {
            if ($log = Logs::getLastOne(null, 'user', 'qrlogin_'.$nonce)) {
                $content = json_decode($log->content);
                if ($content->isAllow) {
                    $this->username = $content->username;
                    if (!$this->getUser()) {
                        $this->addError($attribute, '登录用户不存在');
                    }
                } else {
                    $this->addError($attribute, '请登录');
                }
            } else {
                $this->addError($attribute, '请扫码');
            }
        } else {
            $this->addError($attribute, 'nonce 参数未提供');
        }
    }

    public function validateCode($attribute, $params)
    {
        if ($code = trim($this->code)) {
            if (!$userid = Yii::$app->qywx->wx->getUserId($code)) {
                $this->addError($attribute, '不属于企业号，请联系管理员');
            } else {
                $this->username = $userid;
                if (!$this->getUser()) {
                    $this->addError($attribute, '登录用户不存在');
                }
            }
        } else {
            $this->addError($attribute, 'code 参数未提供');
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login($useAccess = false)
    {
        if ($this->validate()) {
            if ($useAccess) {
                if ($this->user->accessToken) {
                    return true;
                } else {
                    $this->user->generateAccessToken($this->rememberMe ? 3600 * 24 * 30 : 3600 * 24);
                    return $this->user->save();
                }
            } else {
                return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
            }
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
