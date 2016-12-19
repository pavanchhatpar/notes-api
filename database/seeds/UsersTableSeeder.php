<?php

namespace database\seeds;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\DB;



class UsersTableSeeder extends Seeder
{
    protected $hasher;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->hasher = new BcryptHasher();
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'lumen.api.admin@gmail.com',
            'password' => $this->hasher->make('12345678'),
            'salt' => base64_encode(random_bytes(128)),
        ]);
    }
}
