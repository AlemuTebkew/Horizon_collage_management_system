<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $per_page=request()->filled('per_page') ? request('per_page') : 10;
        return response()->json(Event::orderByDesc('posted_date')->paginate($per_page),200);

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
            'event_start_time'=>'required',
            'event_end_time'=>'required'
        ]);
          $event=new Event();
          $event->title=$request->title;
          $event->description=$request->description;
          $event->posted_date= date('Y-m-d',strtotime($request->posted_date));
          $event->event_start_time=$request->event_start_time;
          $event->event_end_time=$request->event_end_time;

          //     $event->event_start_time= date('h-i-s',strtotime($request->event_start_time));
        //   $event->event_end_time= date('h-i-s',strtotime($request->event_end_time));
          $event->save();
        return response()->json($event,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        return $event;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title'=>'required',
            'posted_date'=>'required',
            'description'=>'required',
            'event_start_time'=>'required',
            'event_end_time'=>'required'
        ]);

          $event->title=$request->title;
          $event->description=$request->description;
          $event->posted_date= date('Y-m-d',strtotime($request->posted_date));
          $event->event_start_time= $request->event_start_time;
          $event->event_end_time= $request->event_end_time;
          $event->save();
        return response()->json($event,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
      $event->delete();
    }
}
