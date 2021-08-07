<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class OrangSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create('id_ID');
        for ($i = 0; $i < 50; $i++){
            $data = [
                'nama' => $faker->name,
                'alamat'    => $faker->address,
                'created_at' => Time::createFromTimestamp($faker->unixTime()),
                'updated_at' => Time::now(),
            ];
            $this->db->table('orang')->insert($data);
        }

        // Simple Queries
        // $this->db->query("INSERT INTO orang (nama, alamat) VALUES(:nama:, :alamat:)", $data);

        // Using Query Builder
        // $this->db->table('orang')->insertBatch($data);
    }
}
