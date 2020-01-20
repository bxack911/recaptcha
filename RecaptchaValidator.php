<?php

/**
 * Recaptcha widget
 *
 * @param $secret // Секретный ключ
 * @param $action // Указываем такой-же как и в виджете
 * @param $message // Сообщение об ошибке
 */

namespace common\components\recaptcha;

use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\validators\Validator;

/** 
    public function rules()
    {
        return [
           ...
            [['reCaptcha'], RecaptchaValidator::className(),
                'action' => 'action',
                'skipOnEmpty' => subscribeForm,
            ]
            ...
        ];
    }
 */
    
class ReCaptchaValidator extends Validator
{
    /** @var string|boolean Set to false if you don`t need to check action. */
    public $secret;
    public $action;
    public $message;

    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;

        if(!$this->get_response($value)){
            if(!$this->message){
                $model->addError($attribute, "Invalid recaptcha verify response.");
            }else{
                $model->addError($attribute, $this->message);
            }
        }else{
            return true;
        }
    }

    private function get_response($recaptchaCode)
    {
        if(Yii::$app->params['secretKey']){
            $this->secret = Yii::$app->params['secretKey'];
        }
        
        $data = [
            'secret' => $this->secret,
            'response' => $recaptchaCode,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        if (!isset($response['success'], $response['action'], $response['hostname'], $response['score']) ||
            ($response['success'] === false || $response['action'] != $this->action)) {
            return false;
        } else {
            return true;
        }
    }
}
