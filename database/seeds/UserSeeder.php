<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usersCount = max((int)$this->command->ask('Kaç adet kullanıcı yüklemek istersiniz?', 20), 1);
        factory(User::class)->states('duygu-kesgin')->create();
        factory(User::class,$usersCount)->create();
    }
}
