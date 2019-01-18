<?php

namespace App\Blog\Db\seeds;

use Phinx\Seed\AbstractSeed;

class CommentSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [];
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i += 1) {
            $date = date('Y-m-d H:i:s', $faker->unixTime('now'));

            $data[] = [
                'id' => $faker->uuid,
                'post_id' => mt_rand(0, 9),
                'content' => $faker->text(3000),
                'created_at' => $date,
                'updated_at' => $date
            ];
        }

        $this->table('comments')
            ->insert($data)
            ->save();
    }
}
