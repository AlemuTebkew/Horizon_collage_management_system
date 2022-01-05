<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\DegreeDepartment;
use App\Models\Event;
use App\Models\News;
use App\Models\StudentGalary;
use App\Models\TvetDepartment;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function getDepartments(){

        $dd=DegreeDepartment::all('name');
        $td=TvetDepartment::all('name');
        return response()->json([
            'degree_departments'=>$dd,
            'tvet_departments'=>$td
        ],200);
    }


    public function getGalleries(){
        return response()->json(StudentGalary::paginate()->paginate(10),200);

    }


    public function getNews(){
        return response()->json(News::orderByDesc('posted_date')->paginate(10),200);

    }


    public function getEvents(){
        return response()->json(Event::orderByDesc('posted_date')->paginate(10),200);

    }



}
