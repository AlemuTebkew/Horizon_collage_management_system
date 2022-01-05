<?php

namespace App\Http\Controllers\Dean;
use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $per_page=request()->filled('per_page') ? request('per_page') : 10;
        return response()->json(News::orderByDesc('posted_date')->paginate($per_page),200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required',
            'posted_date'=>'required',
            'description'=>'required',

        ]);
          $news=new News();
          $news->title=$request->title;
          $news->description=$request->description;
          $news->posted_date= date('Y-m-d',strtotime($request->posted_date));
          $news->save();
        return response()->json($news,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function show(News $news)
    {
        return $news;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $news=News::findOrFail($id);

        $request->validate([
            'title'=>'required',
            'posted_date'=>'required',
            'description'=>'required',

        ]);
          $news->title=$request->title;
          $news->description=$request->description;
          $news->posted_date= date('Y-m-d',strtotime($request->posted_date));
          $news->save();
        return response()->json($news,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
        $news=News::findOrFail($id);
        $news->delete();
    }
}
