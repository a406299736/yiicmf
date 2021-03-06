<?php
/**
 * Created by PhpStorm.
 * User: xxx
 * Date: 16/7/23
 * Time: 下午9:00
 */

namespace common\behaviors;


use common\models\Vote;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class VoteBehavior extends Behavior
{
    /**
     * @var \yii\db\ActiveRecord
     */
    public $owner;

    public $type;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function getVoteModel()
    {

    }

    /**
     * 当前用户是否顶
     * @return bool
     */
    public function getIsUp()
    {
        if (!Yii::$app->user->isGuest) {
            $userId = Yii::$app->user->id;
            $up = Vote::find()->where(['type' => $this->type, 'type_id' => $this->owner->id, 'user_id' => $userId, 'action' => Vote::ACTION_UP])->one();
            if ($up) {
                return true;
            }
        }
        return false;
    }

    /**
     * 当前用户是否踩
     * @return bool
     */
    public function getIsDown()
    {
        if (!Yii::$app->user->isGuest) {
            $userId = Yii::$app->user->id;
            $down = Vote::find()->where(['type' => $this->type, 'type_id' => $this->owner->id, 'user_id' => $userId, 'action' => Vote::ACTION_DOWN])->one();
            if ($down) {
                return true;
            }
        }
        return false;
    }

    public function getType()
    {
        if ($this->type == null) {
            $this->type = $this->owner->className();
        }

        return ltrim($this->type,"\\");
    }

    public function getTypeId()
    {
        return $this->owner->getPrimaryKey();
    }

    public function afterDelete()
    {
        $type = $this->getType();
        $type_id = $this->getTypeId();
        Vote::deleteAll(['type' => $type, 'type_id' => $type_id]);
    }
}