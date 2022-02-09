<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\DegreeDepartment;
use App\Models\Event;
use App\Models\News;
use App\Models\SocialLink;
use App\Models\StudentGalary;
use App\Models\TvetDepartment;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function getDepartments(){

        $dd=DegreeDepartment::pluck('name');
        $td=TvetDepartment::pluck('name');
        return response()->json([
            'degree_departments'=>$dd,
            'tvet_departments'=>$td
        ],200);
    }


    public function getGalleries(){
        return response()->json(StudentGalary::orderByDesc('id')->paginate(4),200);

    }


    public function getNews(){

        $per_page=request()->filled('per_page') ? request('per_page') : 5;
        return response()->json(News::orderByDesc('posted_date')->paginate($per_page),200);

    }


    public function getEvents(){
        $per_page=request()->filled('per_page') ? request('per_page') : 5;
        return response()->json(Event::orderByDesc('posted_date')->paginate($per_page),200);

    }

    public function getSocialLinks(){
        return response()->json(SocialLink::first(),200);

    }




}
