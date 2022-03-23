<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProjectType;
use App\Models\ProjectCategory;
use App\Models\Company;
use App\Helpers\HtmlHelper;
use App\Helpers\FunctionHelper;
use Validator;
use DataTables;

class ProjectController extends Controller
{
    protected $title    = 'Projects';
    protected $viewPath = 'projects';
    protected $route    = 'projects';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

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
        return view($this->viewPath . '.list', compact('page', 'variants', 'user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page                   = collect();
        $variants               = collect();
        $page->title            = $this->title;
        $page->link             = url($this->route);
        $page->route            = $this->route; 
        $variants->companies    = Company::where('status', 1)->pluck('name','id'); 
        $variants->categories   = ProjectCategory::where('status', 1)->pluck('name','id'); 
        $variants->projectTypes = ProjectType::where('status', 1)->pluck('name','id'); 
        return view($this->viewPath . '.create', compact('page', 'variants'));
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
                            [ 'name' => 'required|unique:projects', 'project_code_id' => 'required|unique:projects'],
                            [ 'name.required' => 'Please enter Project Title', 'project_code_id.required' => 'Please enter Project ID', 'project_code_id.unique' => 'Project ID is already used.']);

        if ($validator->passes()) {
            $project                    = new Project();
            $project->name              = $request->name;
            $project->date_of_creation  = FunctionHelper::dateToUTC($request->dateOfCreated, 'Y-m-d H:i:s');
            $project->company_id        = $request->companyId;
            $project->category_id       = $request->categoryId;
            $project->project_type      = $request->projectTypeId;
            $project->total_budget      = $request->totalBudget;
            $project->project_code_id   = $request->project_code_id;
            $project->project_code      = FunctionHelper::projectCode();
            $project->location_name     = $request->locationName;
            $project->map_link          = $request->mapLink;
            $project->save();
            return ['flagError' => false, 'message' => Str::singular($this->title). " added successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check !",  'error' => $validator->errors()->all()];
    }
                                               
    /**                       
     * Display a listing of the resource in data table.      
     * @throws \Exception
     */
    public function lists(Request $request)
    {
        $detail     =  Project::with(['category', 'company'])->select(['project_code_id', 'name', 'date_of_creation', 'category_id', 'company_id', 'project_type', 'total_budget', 'location_name', 'deleted_at', 'id']);
        if (isset($request->form)) {
            foreach ($request->form as $search) {
                if ($search['value'] != NULL && $search['name'] == 'searchTitle') {
                    $name       = strtolower($search['value']);

                    $detail     = $detail->where(function($query)use($name) {
                        $query->where('name', 'LIKE', "{$name}%");
                        $query->orWhere('location_name', 'LIKE', "{$name}%");
                    });

                    $detail     = $detail->orWhereHas('company', function ($query) use($name){
                        $query->where('name', 'like', '%'.$name.'%');
                    });

                    $detail     = $detail->orWhereHas('projectType', function ($query) use($name){
                        $query->where('name', 'like', '%'.$name.'%');
                    });

                    $detail     = $detail->orWhereHas('projectType', function ($query) use($name){
                        $query->where('name', 'like', '%'.$name.'%');
                    });


                }
                if ($search['value'] != NULL && $search['value'] == 'inactive') {
                    $detail         = $detail->onlyTrashed();
                }

                if ($search['value'] != NULL && $search['name'] == 'sortBy') {
                    $orderBy        = $search['value'];
                    $detail         = $detail->orderBy($orderBy, 'ASC');
                }
            }
        } else {
            $detail         = $detail->orderBy('id', 'DESC');
        }
        return Datatables::eloquent($detail)
            ->addIndexColumn()
            ->editColumn('company_id', function($detail) {
                return $detail->company->name;
            })
            ->editColumn('category_id', function($detail) {
                return ($detail->category_id != null) ? $detail->category->name : '';
            })
            ->editColumn('project_type', function($detail) {
                return ($detail->project_type != null) ?  $detail->projectType->name : '';
            })
            ->editColumn('name', function($detail) {
                $name   = '';
                $link   = '';
                $name   = Str::limit(strip_tags($detail->name), 40);
                // if (strlen(strip_tags($detail->name)) > 50) {
                //     $name .= "<a href='javascript:void(0);' onclick='showFullName(\"".$detail->name."\")'>View</a>";
                // }
                $link   .= '<a href="'.$this->route.'/'.$detail->id.'">'.$name.'</a>';
                return $link ;
            })
            ->editColumn('date_of_creation', function($detail) {
                return FunctionHelper::dateToTimeZone($detail->date_of_creation, 'd/m/Y h:i'); 
            })
            ->editColumn('total_budget', function($detail) {
                return number_format($detail->total_budget, 2); 
            })
            ->editColumn('location_name', function($detail) {
                return '<a href="javascript:">'.$detail->location_name.'</a>';  
            })
            ->addColumn('total_eias', function($detail) {
                return count($detail->eias) ;
            })
            ->addColumn('action', function($detail) {
                $action = '';
                if ($detail->deleted_at == null) { 
                    $action .= HtmlHelper::editButton(url($this->route.'/'.$detail->id.'/edit'), $detail->id);
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
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        if($project) {
            $page                   = collect();
            $variants               = collect();
            $page->title            = $this->title;
            $page->link             = url($this->route);
            $page->route            = $this->route; 
            return view($this->viewPath . '.show', compact('page', 'variants', 'project'));
        }
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        if($project) {
            $page                   = collect();
            $variants               = collect();
            $page->title            = $this->title;
            $page->route            = url($this->route);
            $variants->companies    = Company::where('status', 1)->pluck('name','id'); 
            $variants->categories   = ProjectCategory::where('status', 1)->pluck('name','id'); 
            $variants->projectTypes = ProjectType::where('status', 1)->pluck('name','id'); 
            return view($this->viewPath . '.create', compact('page', 'variants', 'project'));
        }
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $validator = \Validator::make($request->all(), 
                            [ 'name' => 'required|unique:projects,name,'.$project->id, 'project_code_id' => 'required|unique:projects,project_code_id,'.$project->id,],
                            [ 'name.required' => 'Please enter Project name', 'project_code_id.unique' => 'Project ID is already used.']);

        if ($validator->passes()) {
            if($project) {
                $project->name              = $request->name;
                $project->date_of_creation  = FunctionHelper::dateToUTC($request->dateOfCreated, 'Y-m-d H:i:s');
                $project->company_id        = $request->companyId;
                $project->category_id       = $request->categoryId;
                $project->project_type      = $request->projectTypeId;
                $project->total_budget      = $request->totalBudget;
                $project->project_code_id   = $request->project_code_id;
                $project->location_name     = $request->locationName;
                $project->map_link          = $request->mapLink;
                $project->save();
                return ['flagError' => false, 'message' =>  Str::singular($this->title). " details updated successfully"];
            }
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check !",  'error' => $validator->errors()->all()];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return ['flagError' => false, 'message' =>  Str::singular($this->title). " disabled successfully"];
    }

    /**
     * Restore the specified resource to storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function restore($id, Request $request)
    {
        $project   = Project::where('id', $id)->withTrashed()->first();
        $project->restore();
        return ['flagError' => false, 'message' => Str::singular($this->title). " enabled successfully"];
    }
}
