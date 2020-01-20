<?php

/**
 * Recaptcha widget
 *
 * @param $siteKey // Ключ сайта
 * @param $action // Указываем такой-же как и в валидаторе
 * @param $badge // Разположение бэйджа: bottomleft (слева внизу), inline (прикрепить каптчу к своему контейнеру)
 * @param $badge_container // Если параметра badge указан inline, то тут задаем id своего дива
 */

namespace common\components\recaptcha;

use Yii;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/** 

	<?= $contact_form->field($subscribeForm, 'reCaptcha')->widget(RecaptchaWidget::className(),[
        'action' => 'subscribeForm',
	])->label(false); ?>

 */
class RecaptchaWidget extends Widget
{
    public $siteKey;
    public $action;
    public $jsCallback;
    public $model;
    public $attribute;
    public $badge;
    public $badge_container;
    public $jsApiUrl = "//www.google.com/recaptcha/api.js";
    public $options;
    public $theme;

    public function run()
    {
        parent::run();
        $view = $this->view;

        if(Yii::$app->params['siteKey']){
            $this->siteKey = Yii::$app->params['siteKey'];
        }

        $arguments = \http_build_query([
            'render' => 'explicit',
            'onload' => 'onRecaptchaLoadCallback',
        ]);
        if(!$this->badge_container){
            $this->badge_container = "badge-container-".$this->getReCaptchaId();
$view->registerJs(
            <<<JS
jQuery('body').append('<div id="{$this->badge_container}"></div>');    
JS
            , $view::POS_END);               
        }

$view->registerJs(
            <<<JS
function onRecaptchaLoadCallback() {
        var clientId = grecaptcha.render('{$this->badge_container}', {
            'sitekey': '{$this->siteKey}',
            'badge': '{$this->badge}',
            'size': 'invisible',
            'theme': '{$this->theme}'
        });

        grecaptcha.ready(function() {
            grecaptcha.execute(clientId, {
                    action: '{$this->action}'
                })
                .then(function(token) {
                    jQuery('#' + '{$this->getReCaptchaId()}').val(token);

                    const jsCallback = '{$this->jsCallback}';
                    if (jsCallback) {
                        eval('(' + jsCallback + ')(token)');
                    }
                });
        });
}
JS
            , $view::POS_END);


        $view->registerJsFile(
            $this->jsApiUrl . '?' . $arguments,
             ['depends' => [yii\web\JqueryAsset::className()]]
        );


        $this->customFieldPrepare();
    }

    protected function getReCaptchaId()
    {
        if (isset($this->options['id'])) {
            return $this->options['id'];
        }

            return Html::getInputId($this->model, $this->attribute);

        return $this->id . '-' . $this->inputNameToId($this->name);
    }

    protected function customFieldPrepare()
    {
            $inputName = Html::getInputName($this->model, $this->attribute);


        $options = $this->options;
        $options['id'] = $this->getReCaptchaId();

        echo Html::input('hidden', $inputName, null, $options);
    }
}