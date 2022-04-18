<?php

namespace App\Http\Controllers;

use App\Models\Eia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\DocumentStatus;
use App\Models\EiaStage;
use App\Models\Project;
use App\Helpers\HtmlHelper;
use App\Helpers\FunctionHelper;
use Validator;
use DataTables;

class EiaController extends Controller
{
    protected $title    = 'EIA';
    protected $viewPath = 'eias';
    protected $route    = 'eias';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->lists($request); 
        }
        $page                   = collect();
        $variants               = collect();
        $user                   = auth()->user();
        $page->title            = $this->title;
        $page->link             = url($this->route);
        $page->route            = $this->route;  
        $variants->projects     = Project::pluck('name','id'); 
        return view($this->viewPath . '.list', compact('page', 'variants', 'user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $projectId)
    {
        $project                = Project::find($projectId); 
        if($project) {
            $page                       = collect();
            $variants                   = collect();
            $user                       = auth()->user();
            $page->title                = $this->title;
            $page->route                = url($this->route); 
            $page->projectRoute         = url('projects/'.$projectId); 
            $variants->documentStatuses = DocumentStatus::pluck('name','id'); 
            $variants->stages           = EiaStage::pluck('name','id'); 
            return view($this->viewPath . '.create', compact('page', 'variants', 'project', 'user'));
        }
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), 
                            [ 'codeId' => 'required|unique:eias,code_id'],
                            [ 'codeId.unique' => 'EIA ID is already used', 'codeId.required' => 'Please enter EIA ID ']);

        if ($validator->passes()) {
            $eia                        = new Eia();
            $eia->project_id            = $request->projectId;
            $eia->code_id               = str_replace(' ', '', $request->codeId);
            $eia->date_of_entry         = FunctionHelper::dateToUTC($request->dateOfEntry, 'Y-m-d H:i:s');
            $eia->code                  = FunctionHelper::EIACode();; 
            $eia->project_team_leader   = $request->projectTeamLeader;
            $eia->cost_of_develop       = $request->costOfDevelop;
            $eia->stage_id              = $request->stage;
            $eia->status                = $request->status;
            $eia->created_by            = auth()->user()->id;
            $eia->address               = $request->address;
            $eia->latitude              = $request->latitude;
            $eia->longitude             = $request->longitude;
            $eia->save();
            return ['flagError' => false, 'message' => $this->title. " added successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check !",  'error' => $validator->errors()->all()];
    }

    /**
     * Display a listing of the resource in datatable.
     * @throws \Exception
     */
    public function lists(Request $request, $projectId = null)
    {
        $detail     =  Eia::with('project')->select(['code_id', 'status', 'date_of_entry', 'project_team_leader', 'cost_of_develop', 'stage_id', 'deleted_at', 'project_id', 'id']);
        
        if ($projectId!= null) {
            $detail     = $detail->where('project_id', $projectId);
        }
        
        if (isset($request->form)) {
            
            foreach ($request->form as $search) {
                if ($search['value'] != NULL && $search['name'] == 'searchTitle') {
                    
                    $name       = strtolower($search['value']);
                    $detail     = $detail->where(function($query)use($name) {
                        $query->where('code_id', 'LIKE', "{$name}%");
                    });

                }
                if ($search['value'] != NULL && $search['value'] == 'inactive') {
                    $detail         = $detail->onlyTrashed();
                }

                if ($search['value'] != NULL && $search['name'] == 'project_id') {
                    $project_id     = strtolower($search['value']);
                    $detail         = $detail->where('project_id',  $project_id);
                }
            }
        }
        else {
            $detail         = $detail->orderBy('id', 'DESC');
        }
        return Datatables::eloquent($detail)
            ->addIndexColumn()
            ->addColumn('project_id', function($detail) {
                $link   = '';
                $link   .= '<a href="projects/'. $detail->project->id.'">'.$detail->project->project_code_id.'</a>';
                return $link ;
            })
            ->editColumn('code_id', function($detail) {
                $link   = '';
                $link   .= '<a href="'.$this->route .'/'. $detail->id.'">'.$detail->code_id.'</a>';
                return $link ;
            })
            ->editColumn('gps_coordinates', function($detail) {
                return '<a href="javascript:void(0);">Location..</a>';
            })
            ->editColumn('date_of_entry', function($detail) {
                return $detail->formatted_date_of_entry; 
            })
            ->editColumn('status', function($detail) {
                $status = '';
                $status .= HtmlHelper::statusText($detail->stage_id, $detail->status);
                return $status;
            })
            ->editColumn('cost_of_develop', function($detail) {
                return FunctionHelper::currency(). ' ' . number_format($detail->cost_of_develop);
            })
            ->addColumn('action', function($detail) use ($projectId) {
                $action = '';
                if ($detail->deleted_at == null) { 
                    $action .= HtmlHelper::editButton(url('projects/'.$detail->project_id.'/eias/'.$detail->id.'/edit'), $detail->id);
                    $action .= HtmlHelper::disableButton(url($this->route), $detail->id, 'Inactive');
                } else {
                    $action .= HtmlHelper::restoreButton(url($this->route.'/restore'), $detail->id);
                }
                return $action;
            })
            ->removeColumn('id')
            ->escapeColumns([])
            ->make(true);                    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Eia  $eia
     * @return \Illuminate\Http\Response
     */
    public function show(Eia $eia)
    {
        if($eia) {
            $page                       = collect();
            $variants                   = collect();
            $page->title                = $this->title;
            $page->link                 = url($this->route);
            $page->route                = $this->route; 
            $page->projectRoute         = url('projects/'.$eia->project_id); 
            $page->eiaRoute             = url('eias/'.$eia->id); 
            return view($this->viewPath . '.show', compact('page', 'variants', 'eia'));
        }
        abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Eia  $eia
     * @return \Illuminate\Http\Response
     */
    public function details(Request $request, $id)
    {
        $eia        = Eia::find($id);
        if($eia) {
            $page                       = collect();
            $variants                   = collect();
            $page->title                = $this->title;
            $page->link                 = url($this->route);
            $page->route                = $this->route; 
            $page->projectRoute         = url('projects/'.$eia->project_id); 
            $page->eiaRoute             = url('projects/'.$eia->project_id); 
            return view($this->viewPath . '.details', compact('page', 'variants', 'eia'));
        }
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Eia  $eia
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $projectId, $id)
    {
        $project        = Project::find($projectId);
        if($project) {
            $eia            = Eia::find($id);
            if($eia) {
                $page                       = collect();
                $variants                   = collect();
                $user                       = auth()->user();
                $page->title                = $this->title;
                $page->route                = url($this->route);
                $page->projectRoute         = url('projects/'.$projectId); 
                $variants->documentStatuses = DocumentStatus::pluck('name','id'); 
                $variants->stages           = EiaStage::pluck('name','id'); 
                return view($this->viewPath . '.create', compact('page', 'variants', 'eia', 'project', 'user'));
            }
            abort(404);
        }
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Eia  $eia
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Eia $eia)
    {
        $validator = \Validator::make($request->all(), 
                            [ 'codeId' => 'required|unique:eias,code_id,'.$eia->id],
                            [ 'codeId.unique' => 'EIA ID is already used', 'codeId.required' => 'Please enter EIA ID']);

        if ($validator->passes()) {            
            $eia->project_id            = $request->projectId;
            $eia->code_id               = str_replace(' ', '', $request->codeId);
            $eia->date_of_entry         = FunctionHelper::dateToUTC($request->dateOfEntry, 'Y-m-d H:i:s');
            $eia->project_team_leader   = $request->projectTeamLeader;
            $eia->cost_of_develop       = $request->costOfDevelop;
            $eia->stage_id              = $request->stage;
            $eia->status                = $request->status;
            $eia->address               = $request->address;
            $eia->latitude              = $request->latitude;
            $eia->longitude             = $request->longitude;
            $eia->save();
            return ['flagError' => false, 'message' => $this->title. " updated successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check !", 'error' => $validator->errors()->all()];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Eia  $eia
     * @return \Illuminate\Http\Response
     */
    public function destroy(Eia $eia)
    {
        $eia->delete();
        return ['flagError' => false, 'message' =>  $this->title. " disabled successfully"];
    }
}
