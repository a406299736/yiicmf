<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\modules\user\models\SignupForm */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('common', 'Signup');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">

    <div class="row">
        <div class="well col-lg-5 col-lg-offset-3 bg-info">
            <div class="alert alert-info">
                请填写您的注册信息
            </div>
            <?php $form = ActiveForm::begin(['id' => 'form-signup', 'enableAjaxValidation' => true]); ?>

            <fieldset>
                <div class="input-group input-group-lg">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user red"></i></span>
                    <?php echo Html::input('text','SignupForm[username]', $model->username, ['class'=>'form-control','placeholder'=>'请输入用户名']); ?>
                </div>
                <div class="clearfix"></div><br>
                <div class="input-group input-group-lg field-signupform-email required">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope red"></i></span>
                    <?php echo Html::input('text','SignupForm[email]', $model->email, ['class'=>'form-control', 'id' => 'signupform-email', 'placeholder'=>'请输入邮箱']); ?>
                    <p class="help-block help-block-error"></p>
                </div>
                <div class="clearfix"></div><br>
                <div class="input-group input-group-lg">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock red"></i></span>
                    <?php echo Html::input('password','SignupForm[password]', $model->password, ['class'=>'form-control','placeholder'=>'请输入密码
']); ?>
                </div>
                <div class="clearfix"></div><br>

                <div class="input-group input-group-lg">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-check red"></i></span>
                    <?= Html::input('text', 'SignupForm[verifyCode]', $model->verifyCode, ['class' => 'form-control', 'id' => 'SignupForm-verifycode', 'placeholder' => '请输入验证码', 'style' => 'width: 270px;'])?>
                    <?= \yii\captcha\Captcha::widget([
                        'name' => 'captchaimg',
                        'captchaAction' => 'captcha',
                        'imageOptions' => [
                            'id' => 'SignupForm-verifycode-image',
                            'style' => 'cursor:pointer;margin-left:25px; float:right;height:46px;'
                        ],
                        'template' => '{image}',
                    ])?>
                </div>

                <div class="form-group text-center">
                    <?php
                    $loginOptions = [
                        'class' => 'btn btn-primary', 'name' => 'signup-button'
                    ];
                    if (Yii::$app->request->isAjax) {
                        $loginOptions['data-ajax'] = 1;
                        $loginOptions['data-refresh-pjax-container'] = 'header-container';
                        $loginOptions['data-callback'] = '$.modal.close()';
                    }
                    ?>
                    <div class="clearfix"></div><br>
                    <?= Html::submitButton('注册', $loginOptions) ?>
                    &nbsp;&nbsp;已有帐号? <?= Html::a('马上登录', ['/user/security/login']) ?>
                </div>
            </fieldset>
            <?php ActiveForm::end(); ?>
            <?php
            if($model->errors){
                if ($model->errors['verifyCode']) {
                    echo '验证码错误';
                } elseif ($model->errors['password']) {
                    echo $model->errors['password'][0];
                } elseif ($model->errors['username']) {
                    echo $model->errors['username'][0];
                } else {
                    echo '请检查你的注册信息是否正确';
                }
            }
            ?>
            <?php $this->trigger('afterLogin'); ?>
        </div>
    </div>
</div>
