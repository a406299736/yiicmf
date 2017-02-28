<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\modules\user\models\LoginForm */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('common', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">

    <div class="row">
        <div class="well col-lg-5 col-lg-offset-3 bg-info">
            <div class="alert alert-info">
                请填写您的用户名和密码
            </div>
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <fieldset>
                <div class="input-group input-group-lg">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user red"></i></span>
                    <?php echo Html::input('type','LoginForm[username]', $model->username, ['class'=>'form-control','placeholder'=>'请输入用户名']); ?>
                </div>
                <div class="clearfix"></div><br>
                <div class="input-group input-group-lg">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock red"></i></span>
                    <?php echo Html::input('password','LoginForm[password]', $model->password, ['class'=>'form-control','placeholder'=>'请输入密码
']); ?>
                </div>
                <div class="clearfix"></div><br>

                <div class="input-group input-group-lg">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-pencil red"></i></span>
                    <?= Html::input('text', 'LoginForm[verifyCode]', $model->verifyCode, ['class' => 'form-control', 'id' => 'loginform-verifycode', 'placeholder' => '请输入验证码', 'style' => 'width: 270px;'])?>
                    <?= \yii\captcha\Captcha::widget([
                        'name' => 'captchaimg',
                        'captchaAction' => 'captcha',
                        'imageOptions' => [
                            'id' => 'loginform-verifycode-image',
                            'style' => 'cursor:pointer;margin-left:25px; float:right;height:46px;'
                        ],
                        'template' => '{image}',
                    ])?>
                </div>

                <div class="input-group input-group-lg" style="float: right">
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                </div>
                <div style="color:#999;margin:1em 0">
                    如果忘记了密码，你可以 <?= Html::a('重置密码', ['/user/security/request-password-reset']) ?>.
                </div>

                <div class="form-group text-center">
                    <?php
                    $loginOptions = [
                        'class' => 'btn btn-primary', 'name' => 'login-button'
                    ];
                    if (Yii::$app->request->isAjax) {
                        $loginOptions['data-ajax'] = 1;
                        $loginOptions['data-refresh-pjax-container'] = 'header-container';
                        $loginOptions['data-callback'] = '$.modal.close()';
                    }
                    ?>
                    <?= Html::submitButton('登录', $loginOptions) ?>

                    &nbsp;&nbsp;还没有帐号? <?= Html::a('马上注册', ['/user/registration/signup']) ?>
                </div>
            </fieldset>
            <?php ActiveForm::end(); ?>
            <?php
            if($model->errors){
                if ($model->errors['verifyCode']) {
                    echo '验证码错误';
                } else {
                    echo '用户名或密码错误';
                }
            }
            ?>
            <?php $this->trigger('afterLogin'); ?>
        </div>
    </div>
</div>
