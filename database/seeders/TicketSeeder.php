<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tickets')->insert([
            'ticket_number' => 'SQT66663',
            'category' => 'GGN',
            'subcategory' => 'NODE-B',
            'status' => 'assigned',
            'assigned_to' => '19930270'
        ]);
    }
}
