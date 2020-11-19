<?php

namespace App\Console\Commands;

use App\Exceptions\ApiException;
use App\Models\Admin\User;
use Illuminate\Console\Command;

/**
 * 创建Admin账户
 *
 * Class createAdmin
 * @package App\Console\Commands
 */
class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin account';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $login_name = $this->ask('login name?');
        $pass = $this->secret('password?');

        $validator = app('validator')->make([
            'login_id' => $login_name,
            'password' => $pass,
        ], [
            'login_id' => 'required|email|unique:admin',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $item) {
                $this->error($item);
            }
            return;
        }

        $user = new User();

        $user->fill([
            'login_id' => $login_name,
            'username' => $login_name,
        ]);
        $user->createPwd($pass);
        if (!$user->save()) {
            throw new ApiException('Create User Error!');
        }

        $this->info('create user success!');
    }
}
