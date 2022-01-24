<?php

namespace Database\Seeders;

use App\Models\FeeType;
use App\Models\Month;
use App\Models\Program;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // $types=[
        //     ['name'=>'Registration Fee'],
        //     ['name'=>'CP Fee'],
        //     ['name'=>'Monthly Fee'],
        //     ['name'=>'ID Replacement Fee']
        // ];

        // foreach ($types as $type) {
        //     $t=new FeeType();
        //     $t->name=$type['name'];
        //     $t->save();
        // }
        // $months=   [
        // ['number'=>1,'name'=>'September'],
        // ['number'=>2,'name'=>'October'],
        // ['number'=>3,'name'=>'November'],
        // ['number'=>4,'name'=>'December'],
        // ['number'=>5,'name'=>'January'],
        // ['number'=>6,'name'=>'February'],
        // ['number'=>7,'name'=>'March'],
        // ['number'=>8,'name'=>'April'],
        // ['number'=>9,'name'=>'May'],
        // ['number'=>10,'name'=>'Jun'],
        // ['number'=>11,'name'=>'July'],
        // ['number'=>12,'name'=>'August'],
        // ];

        // foreach ($months as $month) {

        //     $mo=new Month();
        //     $mo->number=$month['number'];
        //     $mo->name=$month['name'];
        //     $mo->save();
        // }

        $programs=[
            ['name'=>'Regular','type'=>'degree'],
            ['name'=>'Extension','type'=>'tvet'],
            ['name'=>'Regular','type'=>'tvet'],
            ['name'=>'Extension','type'=>'degree'],
        ];

        foreach ($programs as $program) {
            $p=new Program();
            $p->name=$program['name'];
            $p->type=$program['type'];
            $p->save();
             }

        // \App\Models\Employee::factory(1)->create();
        // \App\Models\UserLogin::factory(1)->create();
    }
}
