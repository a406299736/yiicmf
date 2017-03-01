<?php
/**
 * Created by PhpStorm.
 * User: xxx
 * Date: 16/6/16
 * Time: 下午9:57
 */

/**
 * 任务调度
 * crontab -e * * * * * php /path/to/yii schedule/run  1>> /dev/null 2>&1
 * @see
 */

// $schedule->command('migrate')->cron('* * * * *');

// $schedule->exec('composer self-update')->daily();

/*
linux

crontab -e 添加如下一条定时任务

* * * * * php /path/to/yii schedule/run 1>> /dev/null 2>&1

console/schedule.php 里添加一条需要定时执行的Yii命令

$schedule->exec('migrate/dump')->daily(); // 每天执行一次数据库导出迁移*/
