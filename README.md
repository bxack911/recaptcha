# recaptcha
Recaptcha widget and validator for yii2
USAGE:

Лезем в common/config/params.php добавляем в параметры sitekey и secret:
'siteKey' => '6Lf7CagUAAAAAMlkkDb9r2vNWEQ-PQdqDNSwlcZP',
'secretKey' => '6Lf7CagUAAAAAJTKcJQQBTqlBYYryzLBz0WeVH0C',


На вьюхе каптчу инициализируем так:
//Это callback функция
<?php
$this->registerJs(
<<<JS
function mycallback(){
    alert('ttst')   
}
JS
, $this::POS_END);   
?>
<?= $contact_form->field($subscribeForm, 'reCaptcha')->widget(RecaptchaWidget::className(),[
        //'siteKey' => '6Lf7CagUAAAAAMlkkDb9r2vNWEQ-PQdqDNSwlcZP', // Если по какой-то причине было лень залезть и указать сайткей в параметрах, то указываем тут
        'action' => 'homepage',// Action в каждой формы должен быть уникальным, так-что можешь в тупую указать название формы и будет жить проще.
        'badge' => 'bottomleft', // Разположение бэйджа: bottomleft (слева внизу), inline (прикрепить каптчу к своему контейнеру)
        'badge_container' => 'inline-bacge', // Если параметра badge указан inline, то тут задаем id своего дива
        'theme' => 'dark', // тема. dark (темная тема каптчи https://prnt.sc/qpr0la), light (светлая, по дефолту),
        'jsCallback' => 'mycallback', Функция js которая вызовется после инициализации каптчи. Может пригодится, если нужно написать свой ajax валидатор'
])->label(false); ?>
Подробнее об атрибутах можно почитать тут https://developers.google.com/recaptcha/docs/invisible#render_param

В 80% случаев, будешь юзать такую инициализацию каптчи:
<?= $contact_form->field($subscribeForm, 'reCaptcha')->widget(RecaptchaWidget::className(),[
        'action' => 'subscribeForm',
])->label(false); ?>


Дальше ползем к своей моделе и добавляем валидатор. Вот таким боком:
public function rules()
    {
        return [
           ...
            [['reCaptcha'], RecaptchaValidator::className(),
                //'secret' => '6Lf7CagUAAAAAJTKcJQQBTqlBYYryzLBz0WeVH0C', // Если по какой-то причине было лень залезть и указать секрет в параметрах, то указываем тут
                'action' => 'action', // Action  должен быть такой же как и action . Action в куждой формы должен быть уникальный, иначе задача прилетит в доработку и будешь мучаться - искать где забыл поменять action. Указываешь название формы и будет все ПУШКА.
                'message' => 'Чет пошло не так. Обнови страницу и пробуй еще раз отправить!',// Сообщение которое будет вылетать, если произойдет ошибка валидации
                'skipOnEmpty' => false, // Просто не думаем и вешаем. А если интерестно, то это для того, чтоб не указывать его у required, эта штуковина не позволит пропустить пустой респонс каптчи
            ]
            ...
        ];
    }
В 80% случаев, будешь юзать такую инициализацию карптчи:
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


Благодарочка автору этого фида, больше нигде не нашел нормального примера для инициализации капчти через render https://jsfiddle.net/thatguysam/g7juvkdb/