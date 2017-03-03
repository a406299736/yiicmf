<?php
/**
 * Created by PhpStorm.
 * User: xxx
 * Date: 16/7/26
 * Time: 下午10:41
 */

namespace common\modules\user\controllers;


use common\models\Attachment;
use common\modules\user\models\Profile;
use Yii;
use yii\filters\AccessControl;
use yii\imagine\Image;
use yii\web\Controller;

class SettingsController extends Controller
{
    public function behaviors()
    {
        return [
            'accessControl' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    public function actionProfile()
    {
        $userId = \Yii::$app->user->id;
        $profile = Profile::find()->where(['user_id' => $userId])->one();
        if ($profile->load(\Yii::$app->request->post()) && $profile->save()) {
            return $this->redirect(['profile']);
        } else {
            return $this->render('profile', [
                'model' => $profile
            ]);
        }
    }
    public function actionAvatar()
    {
        $user = \Yii::$app->user->identity;

        if (Yii::$app->getRequest()->isAjax) {
            $avatar = Yii::$app->getRequest()->post("avatar");
            $x = Yii::$app->getRequest()->post("x");
            $y = Yii::$app->getRequest()->post("y");
            $w = Yii::$app->getRequest()->post("w");
            $h = Yii::$app->getRequest()->post("h");
            $attachment = Attachment::findOne($avatar);
            $original= $attachment->getUrl();
            //$fileInfo = pathinfo($original);
            //$target = $fileInfo['dirname'] . '/' . $fileInfo['filename'] . '.' . $fileInfo['extension'];
            $fileData = file_get_contents($original);
            $target = Yii::$app->storage->baseDir . '/storage/upload/' . $attachment->path;
            file_put_contents($target, $fileData);
            Image::crop($target, $w, $h, [
                $x,
                $y
            ])->save($target);
            //$targetUrl = Yii::$app->storage->path2url($target);
            $user->saveAvatar(Yii::$app->request->hostInfo . '/storage/upload/' . $attachment->path);

            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $result = [
                'status' => true,
                'msg' => '保存成功'
            ];
            return $result;
        }

        return $this->render('avatar', [
            'user' => $user
        ]);
    }
}