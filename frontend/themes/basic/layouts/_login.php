<?php
use yii\helpers\Html;
?>
<?php \yii\bootstrap\Modal::begin([
    'id' => 'modal-login',
    'header' => '<span style="color: #e26005;font-size: 18px;">登录</span>',
    'size' => 'modal-sm'
]) ?>
    <?php
    $loginFormModel = new \common\modules\user\models\LoginForm();
    $loginForm = \yii\widgets\ActiveForm::begin(['action' => ['/user/security/login']]);
    ?>
    <?= $loginForm
        ->field($loginFormModel, 'username')
        ->label(false)
        ->textInput(['placeholder' => $loginFormModel->getAttributeLabel('username')]) ?>

    <?= $loginForm
        ->field($loginFormModel, 'password')
        ->label(false)
        ->passwordInput(['placeholder' => $loginFormModel->getAttributeLabel('password')]) ?>

    <div class="form-group required">
    <?= Html::input('text', 'LoginForm[verifyCode]', $loginFormModel->verifyCode, ['class' => 'form-control', 'placeholder' => '请输入验证码', 'style' => 'width: 138px;'])?>
    <?= \yii\captcha\Captcha::widget([
        'name' => 'captchaimg',
        'captchaAction' => 'user/security/captcha',
        'imageOptions' => [
            'id' => 'LoginForm-verifycode-image',
            'style' => 'cursor:pointer; margin-top: -40px; float:right;height:46px;'
        ],
        'template' => '{image}',
    ])?>
    </div>
    <input type="hidden" name="LoginForm[rememberMe]" value="1">

    <div class="form-group">
        <?= Html::submitButton('登录', ['class' => 'btn btn-primary btn-block', 'data-ajax' => '1', 'data-refresh-pjax-container' => 'header-container', 'data-callback' => '$("#modal-login").modal("hide")']) ?>
    </div>
    <?php \yii\widgets\ActiveForm::end();$this->trigger('afterLogin'); ?>
<?php \yii\bootstrap\Modal::end() ?>