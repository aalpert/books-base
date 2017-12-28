<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user  = new \App\User();
        $user->username = 'andrey.alpert@gmail.com';
        $user->email = 'andrey.alpert@gmail.com';
        $user->name = 'Andrey Alpert';
        $user->role = 'admin';
        $user->password = '$2y$10$GpBkqII4DXpo275Dvhw8ie3aVN/YwS4TpnTcIK5r.8Fp29GyPeNx6';
        $user->save();

        $user  = new \App\User();
        $user->username = 'ealpert';
        $user->email = 'booksnook59@gmail.com';
        $user->name = 'Евгений Альперт';
        $user->role = 'admin';
        $user->password = '$2y$10$zxMUNPO/KKKLlRQzeER/7uDfV7aUYea8aBncRs/sH0r8LPajai5L.';
        $user->save();

        $user  = new \App\User();
        $user->username = 'lalpert';
        $user->email = '';
        $user->name = 'Людмила Альперт';
        $user->role = 'user';
        $user->password = '$2y$10$cPfHrXBTI7HVjj1qxepRKOJXWFFNksIaD6sxMDrmawzoHFqBBHY6G';
        $user->save();

        $user  = new \App\User();
        $user->username = 'booksnook.com.ua';
        $user->email = 'booksnook59@gmail.com';
        $user->name = 'BooksNook';
        $user->role = 'client';
        $user->password = '$2y$10$je3ofBW6LKLxeCPAhEPvPexQyA6bJfWM75NANX2ArbB4J.6sOkvfe';
        $user->save();
    }
}
