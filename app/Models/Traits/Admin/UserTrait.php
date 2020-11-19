<?php
namespace App\Models\Traits\Admin;

trait UserTrait {


    protected static
        $mapping = [
    'id' => [
        'name' => 'id',
        'label' => '索引ID',
    ],
    'login_id' => [
        'name' => 'login_id',
        'label' => '登录账户',
    ],
    'username' => [
        'name' => 'username',
        'label' => '账户名',
    ],
    'pwd' => [
        'name' => 'pwd',
        'label' => '密码',
    ],
    'group' => [
        'name' => 'group',
        'label' => '用户组',
    ],
    'created_at' => [
        'name' => 'created_at',
        'label' => 'Created_at',
    ],
    'updated_at' => [
        'name' => 'updated_at',
        'label' => 'Updated_at',
    ],
];

    protected
        $formfields = [
    'login_id' => [
        'name' => 'login_id',
        'type' => 'input',
        'title' => '登录账户',
    ],
    'username' => [
        'name' => 'username',
        'type' => 'input',
        'title' => '账户名',
    ],
    'pwd' => [
        'name' => 'pwd',
        'type' => 'input',
        'title' => '密码',
    ],
    'group' => [
        'name' => 'group',
        'type' => 'input',
        'title' => '用户组',
    ],
];

    protected
        $default_rules = [
            'login_id' => 'required|alpha_dash|max:255',
            'username' => 'required|alpha_dash|max:255',
            'pwd' => 'required|alpha_dash|max:255',
            'group' => 'alpha_dash|max:255',
            'created_at' => 'date',
            'updated_at' => 'date',
        ];

    protected
        $default_messages = [];

    public static function map() {
        $mapping = self::$mapping;

        unset($mapping['pwd']);

        return $mapping;
    }


    public function getFormFields() {
        $fields = $this->formfields;

        return $fields;
    }

    /**
     * 验证规则
     * @return array
     */
    public function rules()
    {
        return $this->default_rules;
    }

    /**
     * 验证规则错误提示
     * @return array
     */
    public function messages()
    {
        return [];
    }
}