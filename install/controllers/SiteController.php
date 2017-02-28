<?php
namespace install\controllers;


use common\modules\user\models\User;
use yii\web\Controller;
use Yii;
use install\models\DatabaseForm;
use install\models\SiteForm;
use install\models\AdminForm;

class SiteController extends Controller
{
    protected function renderJson($status = true, $message = '')
    {
        Yii::$app->response->format = 'json';
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    public $migrationPath = '@database/migrations';

    public $migrationTable = '{{%migration}}';

    public $envPath = '@root/.env';

    public function init()
    {
        parent::init();
        $this->migrationPath = Yii::getAlias($this->migrationPath);
    }

    /**
     * Lists all Menu models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLanguage()
    {
        return $this->render('index');
    }

    public function actionLicenseAgreement()
    {
        if (\Yii::$app->getRequest()->isPost) {
            if (\Yii::$app->getRequest()->post("license") == "on") {
                return $this->renderJson(true);
            } else {
                return $this->renderJson(false, "同意安装协议才能继续安装!");
            }
        }
        
        return $this->render('license');
    }

    public function actionCheckEnv()
    {
        $checkResult = include Yii::getAlias('@install/requirements.php');
        // Render template
        return $this->render('checkenv', [
            "data" => $checkResult
        ]);
    }

    public function actionSelectDb()
    {
        return $this->render('index');
    }

    public function actionSetDb()
    {
        $model = new DatabaseForm();
        
        $model->loadDefaultValues();
        
        if ($model->load(Yii::$app->request->post())) {
            
            if ($model->validate() && $model->save()) {
                return $this->renderJson(true);
            } else {
                return $this->renderJson(false, current($model->getFirstErrors()));
            }
        }
        
        return $this->render('setdb', [
            "model" => $model
        ]);
    }

    public function actionSetSite()
    {
        $model = new SiteForm();
        
        $model->loadDefaultValues();
        
        if ($model->load(Yii::$app->request->post())) {
            
            if ($model->validate() && $model->save()) {
                return $this->renderJson(true);
            } else {
                return $this->renderJson(false, current($model->getFirstErrors()));
            }
        }
        
        return $this->render('setsite', [
            "model" => $model
        ]);
    }

    public function actionSetAdmin()
    {
        $model = new AdminForm();
        
        $model->loadDefaultValues();
        if ($model->load(Yii::$app->request->post())) {

            if (!$model->validate() || !$model->save()) {
                return $this->renderJson(false, current($model->getFirstErrors()));
            }

                $error = $this->installDb();
                if ($error != null) {
                    return $this->renderJson(false, $error);
                }
                $this->installConfig();
                // 创建用户
                $error = $this->createAdminUser();
                if ($error != null) {
                    return $this->renderJson(false, $error);
                }

                \Yii::$app->getCache()->flush();
                //安装完成
                Yii::$app->setInstalled();
                return $this->renderJson(true);
           
        } 
        
        return $this->render('setadmin', [
            "model" => $model
        ]);
    }
    /**
     * 安装数据库
     */
    public function installDb()
    {
        $handle = opendir($this->migrationPath);
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $this->migrationPath . DIRECTORY_SEPARATOR . $file;
            if (preg_match('/^(m(\d{6}_\d{6})_.*?)\.php$/', $file, $matches) && !isset($applied[$matches[2]]) && is_file($path)) {
                $migrations[] = $matches[1];
            }
        }
        closedir($handle);
        sort($migrations);

        $error = "";

        ob_start();
        if (Yii::$app->db->schema->getTableSchema($this->migrationTable, true) === null) {
            $this->createMigrationHistoryTable();
        }
        foreach ($migrations as $migration) {
            $migrationClass = $this->createMigration($migration);
            try {
                if ($migrationClass->up() === false) {
                    $error = "数据库迁移失败";
                }
                $this->addMigrationHistory($migration);
            } catch (\Exception $e) {
                $error = "数据表已经存在，或者其他错误！";
            }
        }
        ob_end_clean();
        
        if (! empty($error)) {
            return $error;
        }
        return null;
    }
    //写入配置文件
    public function installConfig()
    {
        \Yii::$app->setKeys($this->envPath);
        $data = \Yii::$app->getCache()->get(SiteForm::CACHE_KEY);
        foreach ($data as $name => $value) {
            Yii::$app->setEnv($name, $value);
        }
        return true;
    }

    public function createAdminUser()
    {
        $data = \Yii::$app->getCache()->get(AdminForm::CACHE_KEY);
        $user = new User();
        $user->setScenario("create");
        $user->email = $data["email"];
        $user->username = $data["username"];
        $user->password = $data["password"];

        if($user->create() == false) {
            return current($user->getFirstErrors());
        }
        return null;
    }

    protected function createMigrationHistoryTable()
    {
        Yii::$app->db->createCommand()->createTable($this->migrationTable, [
            'version' => 'varchar(180) NOT NULL PRIMARY KEY',
            'apply_time' => 'integer',
        ])->execute();
        Yii::$app->db->createCommand()->insert($this->migrationTable, [
            'version' => 'm000000_000000_base',
            'apply_time' => time(),
        ])->execute();
    }

    protected function createMigration($class)
    {
        $file = $this->migrationPath . DIRECTORY_SEPARATOR . $class . '.php';
        require_once($file);

        return new $class();
    }

    protected function addMigrationHistory($version)
    {
        $command = Yii::$app->db->createCommand();
        $command->insert($this->migrationTable, [
            'version' => $version,
            'apply_time' => time(),
        ])->execute();
    }
}
