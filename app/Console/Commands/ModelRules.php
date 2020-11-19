<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 根据数据表定义 生成 Model Trait
 *
 * Class ModelRules
 * @package App\Console\Commands
 */
class ModelRules extends Command
{
    /**
     * 模版文件名
     */
    const TPL_TRAIT = 'trait';
    /**
     * model 默认命名空间
     */
    const NS_MODEL = 'App\\Models\\';

    protected $template_file;

    protected $mapping = [];

    protected $form_fields = [];

    protected $validation_rules = [];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:rule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create rule trait for a model Class';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->template_file = app_path('Views/template/'.self::TPL_TRAIT);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $model = $this->ask('enter model name ?');
        if (stripos($model, self::NS_MODEL) === false) {
            $model = self::NS_MODEL . ltrim($model, '\\');
        }

        $reflectionClass = new \ReflectionClass($model);
        if (!$reflectionClass->isSubclassOf('Illuminate\Database\Eloquent\Model')) {
            $this->error('model is not a Eloquent model, eg:"App\Models\User"');
            return;
        }
        $namespace = $reflectionClass->getNamespaceName();
        $classname = $reflectionClass->getShortName();
        $class_file = $reflectionClass->getFileName();

        $namespace = str_replace('App\\Models', 'App\\Models\\Traits', $namespace);

        $models_file_base_path = app_path('Models'.DIRECTORY_SEPARATOR);
        $class_base_path = str_replace($models_file_base_path, '', $class_file);

        $class_file = $models_file_base_path . 'Traits/' . dirname($class_base_path). '/' . $classname . 'Trait.php';

        /**
         * @var $model \Illuminate\Database\Eloquent\Model
         */
        $model = $this->laravel->make($model);

        $table = $model->getConnection()->getTablePrefix() . $model->getTable();

        $manager = $model->getConnection()->getDoctrineSchemaManager();
        $structure = $manager->listTableColumns($table);

        foreach($structure as $column) {
            $this->handleColumn($column);
        }

        $content = file_get_contents($this->template_file);
        $content = str_replace([
            '{namespace}',
            '{classname}',
            '{mapping}',
            '{form_fields}',
            '{default_rules}',
        ], [
            $namespace,
            $classname,
            $this->exportArray($this->mapping),
            $this->exportArray($this->form_fields),
            $this->exportArray($this->validation_rules),

        ], $content);

        $base_dir = dirname($class_file);
        if (!file_exists($base_dir)) {
            mkdir($base_dir, 0777, true);
        }

        file_put_contents($class_file, $content);

        $this->info('new file has been created at: '.$class_file);
    }

    protected function getArguments() {
        return array(
            array('model', InputArgument::REQUIRED, 'Which models to include'),
        );
    }

    public function handleColumn(\Doctrine\DBAL\Schema\Column $column) {
        $name = $column->getName();
        $type = $column->getType()->getName();
        $label = addslashes($column->getComment());
        $require = $column->getNotnull();
        $langth = $column->getLength();

        /**
         * 处理 grid mapping
         */
        $this->mapping[$name] = ['name' => $name, 'label' => (empty($label) ? ucwords($name) : $label)];
        /**
         * 处理 form fields
         */
        $form_field = [
            'name' => $name,
            'type' => 'text',
            'title' => (empty($label) ? ucwords($name) : $label)
        ];
        if ($type == 'string') {
            $form_field['type'] = 'input';
        }
        if (stripos($name, 'password') !== false) {
            $form_field['type'] = 'password';
        }
        if (stripos($name, 'date') !== false) {
            $form_field['type'] = 'date';
        }
        if (stripos($name, 'time') !== false) {
            $form_field['type'] = 'datetime';
        }
        if (in_array($name, ['updated_at', 'created_at'])) {
            $form_field['type'] = 'datetime';
        }
        if (!in_array($name, ['id', 'updated_at', 'created_at', 'deleted_at'])) {
            $this->form_fields[$name] = $form_field;
        }

        /**
         * 处理 验证rule
         */
        /**
         * 自增字段一般是索引，跳过不引入验证规则
         */
        if ($column->getAutoincrement()) {
            return;
        }
        $rule = [];
        if ($require) {
            $rule[] = 'required';
        }
        $rule[] = $this->getTypeRule($type);
        if (!empty($langth)) {
            $rule[] = 'max:'.$langth;
        }

        $this->validation_rules[$name] = implode('|', $rule);

    }

    public function getTypeRule($type) {
        $rule = 'alpha_dash';
        switch ($type) {
            case 'string':
            case 'text':
            case 'guid':
                return 'alpha_dash';
            case 'time':
            case 'date':
            case 'datetimetz':
            case 'datetime':
                return 'date';
            case 'integer':
            case 'bigint':
            case 'smallint':
                return 'integer';
            case 'decimal':
            case 'float':
                return 'numeric';
            case 'boolean':
                return 'boolean';
        }

        return $rule;
    }

    /**
     * 将 array 输出为 php code
     * @param array $src
     * @param int $depth
     * @return string
     */
    public function exportArray(array $src, $depth = 0) {
        $tab = '    ';
        $prefix = str_repeat($tab, $depth);
        $out = '['.PHP_EOL;
        foreach ($src as $key => $val) {
            if (is_string($key)) {
                $out .= $prefix . "{$tab}'{$key}' => ";
            } else {
                $out .= $prefix . $tab;
            }
            if (!is_scalar($val)) {
                $out .= $this->exportArray((array)$val, ($depth+1));
            } elseif (is_string($val)) {
                $out .= "'{$val}'";
            } else {
                $out .= $val;
            }
            $out .= ','.PHP_EOL;
        }
        $out .= $prefix .  ']';

        return $out;
    }
}
