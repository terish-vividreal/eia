<?php

namespace App\Http\Controllers;

use App\Models\TaskAssign;
use App\Events\TaskAssigned;
use App\Jobs\SendTaskAssignedEmailJob;
use Illuminate\Http\Request;
use App\Models\Document;
use Event;
use Carbon;


class TaskAssignController extends Controller
{
    /**
     * Assign task to User.
     *
     * @return \Illuminate\Http\Response
     */
    function assign(Request $request)
    {
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $assign                 = new TaskAssign();
        $assign->document_id    = $request->documentId;
        $assign->assigned_to    = $request->assigned_to;
        $assign->details        = $request->description;
        $assign->assigned_by    = auth()->user()->id;
        $assign->save();
        $document               = Document::where('id', $request->documentId)->update(['is_assigned' => 1]);
        //Call Queue and Job
        // dispatch(new SendTaskAssignedEmailJob($assign));
        //Call events
        event(new TaskAssigned($assign));
        return ['flagError' => false, 'message' => "Task assigned successfully"];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TaskAssign  $taskAssign
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, TaskAssign $taskAssign)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TaskAssign  $taskAssign
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, TaskAssign $taskAssign)
    {
        if ($request->ajax()) {
            return ['flagError' => false, 'data' => $taskAssign];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TaskAssign  $taskAssign
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TaskAssign $taskAssign)
    {
        if ($request->has('taskCompleted') ) {
            $taskAssign->status         = 3 ;
            $taskAssign->completed_note = $request->completedNote;
            $taskAssign->completed_by   = auth()->user()->id;
            $taskAssign->completed_at   = Carbon\Carbon::now();
            $taskAssign->save();
            $document                   = Document::where('id', $request->documentId)->update(['is_assigned' => 0]);
            return ['flagError' => false, 'message' => "Task completed successfully"];
        } else {
            $assign                 = new TaskAssign();
            $assign->document_id    = $request->documentId;
            $assign->assigned_to    = $request->assigned_to;
            $assign->details        = $request->description;
            $assign->assigned_by    = auth()->user()->id;
            $assign->save();
            $taskAssign->status     = 2 ;
            $taskAssign->save();
            return ['flagError' => false, 'message' => "Task Reassigned successfully"];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TaskAssign  $taskAssign
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaskAssign $taskAssign)
    {
        //
    }
}