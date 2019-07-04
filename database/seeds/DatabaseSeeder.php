<?php

use Illuminate\Database\Seeder;
use App\Criteria;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $criterias = [
            [
                    'label' => 'Originality',
                    'explanation' => 'Does the paper produce new knowledge instead of summarizing what is already known in a new form.',
                    'weight' => 10,
                ],
                [
                    'label' => 'Quality',
                    'explanation' => 'Is the paper well written (English or French, structure,...)?	',
                    'weight' => 10,
                ],
                [
                    'label' => 'Overall evaluation',
                    'explanation' => 'Please give your final Overall evaluation	',
                    'weight' => 80,
                ],
        ];
        
         if(\DB::table('criterias')->count() == 0){

            foreach($criterias as $criteria){
                Criteria::create($criteria);
            }

     

        }
    }
}
