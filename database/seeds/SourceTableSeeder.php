<?php

use Illuminate\Database\Seeder;

class SourceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $source  = new \App\Source();
        $source->title = 'Третий Рим';
        $source->driver = 'galina';
        $source->save();

        $source  = new \App\Source();
        $source->title = 'Ассортимент';
        $source->driver = 'galina';
        $source->save();

        $source  = new \App\Source();
        $source->title = 'КСД';
        $source->driver = 'ksd';
        $source->save();
    }
}
