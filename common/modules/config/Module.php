<?php
/**
 * Created by PhpStorm.
 * User: xxx
 * Date: 2016/11/7
 * Time: 下午7:35
 */

namespace common\modules\config;


use yii\base\BootstrapInterface;

class Module extends \yii\base\Module implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->set('config', [
            'class' => 'common\\modules\\config\\components\\Config',
            'localConfigFile' => '@common/config/main-local.php'
        ]);
    }
}