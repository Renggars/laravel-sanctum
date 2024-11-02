<?php

namespace Database\Seeders;

use App\Models\Todolist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TodolistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Todolist::create(
            [
                'title' => 'Belajar Laravel',
                'description' => 'Laravel is a web application framework with expressive, elegant syntax.',
                'is_completed' => false
            ]
        );

        Todolist::create(
            [
                'title' => 'Belajar PHP',
                'description' => 'PHP is a server scripting language, and a powerful tool for making dynamic and interactive websites.',
                'is_completed' => true
            ]
        );
    }
}
