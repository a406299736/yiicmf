<?php

use yii\db\Migration;

/**
 * Handles the creation for table `{{%module}}`.
 */
class m160702_101410_create_module extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%module}}', [
            'id' => $this->string(50)->notNull()->unique()->comment('标识'),
            'name' => $this->string(50)->notNull(),
            'bootstrap' => $this->string(128)->comment('模块初始化应用ID'),
            'status' => $this->smallInteger(1)->notNull(),
            'type' => $this->smallInteger(1)->notNull()->comment('模块类型1core2plugin'),
            'config' => $this->text()->comment('配置'),
            'created_at' => $this->integer(10)->notNull(),
            'updated_at' => $this->integer(10)->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey('id', '{{%module}}', 'id');
        $this->insert('{{%module}}', [
            'id' => 'user',
            'name' => '用户模块',
            'bootstrap' => 'app-frontend|app-backend',
            'status' => 1,
            'type' => 1,
            'created_at' => time(),
            'updated_at' => time()
        ]);
        $this->insert('{{%module}}', [
            'id' => 'message',
            'name' => '站内信模块',
            'bootstrap' => 'app-frontend|app-backend',
            'status' => 1,
            'type' => 1,
            'created_at' => time(),
            'updated_at' => time()
        ]);
        $this->insert('{{%module}}', [
            'id' => 'city',
            'name' => '城市模块',
            'status' => 1,
            'type' => 1,
            'created_at' => time(),
            'updated_at' => time()
        ]);
        $this->insert('{{%module}}', [
            'id' => 'config',
            'name' => '动态配置',
            'bootstrap' => 'app-frontend|app-backend|app-console',
            'status' => 1,
            'type' => 1,
            'created_at' => time(),
            'updated_at' => time()
        ]);
        $this->insert('{{%module}}', [
            'id' => 'area',
            'name' => '前台动态区域',
            'status' => 1,
            'type' => 1,
            'created_at' => time(),
            'updated_at' => time()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%module}}');
    }
}
