<?php
/**
 * Created by PhpStorm.
 * User: xxx
 * Date: 16/7/4
 * Time: 下午12:31
 */

namespace plugins\danmu;

class Danmu
{
    public static function handle($event)
    {
        echo DanmuWidget::widget([
            'type' => $event->type,
            'typeId' => $event->typeId
        ]);
    }
}