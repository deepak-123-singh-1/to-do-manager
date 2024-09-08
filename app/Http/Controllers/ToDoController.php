<?php

namespace App\Http\Controllers;

use App\Models\ToDo;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class ToDoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ToDo::orderBy('created_at', "DESC")->get();
        return view('to_do/index')->with(['work_data'=> $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'work_title' => 'required|max:255|unique:to_dos'
        ]);
 
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'validation' => $validator->errors()
            ]);
        }else{
            $to_do_save = toDo::create([
                'work_title'=> $request->work_title,
                'status'=> $request->status,
            ]);
            $data = ToDo::orderBy('created_at', "DESC")->get();
            return response()->json([
                'status' => true,
                "message" => "Work created successfully!",
                'data' => $data
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ToDo  $toDo
     * @return \Illuminate\Http\Response
     */
    public function show(ToDo $toDo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ToDo  $toDo
     * @return \Illuminate\Http\Response
     */
    public function edit(ToDo $toDo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ToDo  $toDo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $work = ToDo::where("id", $request['row']['id'])->first();
        $message = '';
        if($request->action == "delete"){
            $work->delete();
            $message = "Work Deleted successfully";
        }else if($request->action == "status"){
            $work['status'] = $request->status;
            $work->save();
            $message = "Work status updated successfully";
        }
        $data = ToDo::orderBy('created_at', "DESC")->get();
        return response()->json([
            'status' => true,
            "message" => $message,
            'data' => $data
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ToDo  $toDo
     * @return \Illuminate\Http\Response
     */
    public function destroy(ToDo $toDo)
    {
        //
    }
}
