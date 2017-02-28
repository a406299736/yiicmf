<?php
/**
 * Created by PhpStorm.
 * User: xxx
 * Date: 16/6/22
 * Time: 下午8:54
 */

namespace common\models\behaviors;


use common\models\Vote;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\Html;

class VoteBehavior extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'sendNotify'
        ];
    }

    public function sendNotify($event)
    {
        // 赞才发通知
        if ($event->sender->action == Vote::ACTION_UP) {
            $fromUid = $event->sender->user_id;
            switch ($event->sender->type) {
                case 'article':
                    $category = 'up_article';
                    $article = $event->sender->article;
                    $toUid = $article->user_id;
                    $extra = [
                        'article_title' => Html::a($article->title, ['/article/view', 'id' => $article->id])
                    ];
                    break;
                default:
                    return;
                    break;
            }
            \Yii::$app->notify->category($category)
                ->from($fromUid)
                ->to($toUid)
                ->extra($extra)
                ->link(url(['/article/view', 'id' => $article->id]))
                ->send();
        }
    }
}