<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Str;
use App\Helpers\HtmlHelper;

class CompanyController extends Controller
{
    protected $title    = 'Companies';
    protected $viewPath = 'companies';
    protected $route    = 'companies';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page                   = collect();
        $variants               = collect();
        $page->title            = $this->title;
        $page->link             = url($this->route);
        $page->route            = $this->route;  
        return view($this->viewPath . '.list', compact('page', 'variants'));
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
                            [ 'name' => 'required|unique:companies'],
                            [ 'name.required' => 'Please enter Company name']);

        if ($validator->passes()) {
            $company                = new Company();
            $company->name          = $request->name;
            $company->details       = $request->details;
            $company->contactName   = $request->contact_name;
            $company->email         = $request->email;
            $company->contact       = $request->contact;
            $company->address       = $request->address;
            $company->save();
            return ['flagError' => false, 'message' => $this->title. " added successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check !",  'error' => $validator->errors()->all()];
    }

    /**
     * Display a listing of the resource in datatable.
     * @throws \Exception
     */
    public function lists(Request $request)
    {
        $detail     =  Company::select(['name', 'contact_name', 'email', 'contact', 'status', 'deleted_at', 'id']);
        if (isset($request->form)) {
            foreach ($request->form as $search) {
                if ($search['value'] != NULL && $search['name'] == 'name') {
                    $name       = strtolower($search['value']);
                    $detail     = $detail->where(function($query)use($name) {
                        $query->where('name', 'LIKE', "{$name}%");
                    });
                }
                if ($search['value'] != NULL && $search['value'] == 'inactive') {
                    $detail         = $detail->onlyTrashed();
                }
            }
        }
        
        return Datatables::eloquent($detail)
            ->addIndexColumn()
            ->editColumn('status', function($detail) {
                if($detail->deleted_at == null) {
                    $checked    = ($detail->status == 1) ? 'checked' : '';
                    $html       = '<div class="switch"><label> <input type="checkbox" '.$checked.' id="' . $detail->id . '" data-url="'.url($this->route.'/update-status').'" class="manage-status" data-id="'.$detail->id.'"> <span class="lever"></span> </label> </div>';
                    return $html;
                }  
            })
            ->addColumn('action', function($detail) {
                $action = '';
                if ($detail->deleted_at == null) { 
                    $action .= HtmlHelper::editButton(url($this->route.'/'.$detail->id.'/edit'), $detail->id);
                    $action .= HtmlHelper::disableButton(url($this->route), $detail->id);
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
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        if($company) {
            $page                   = collect();
            $variants               = collect();
            $page->title            = $this->title;
            $page->link             = url($this->route);
            $page->route            = $this->route; 
            return view($this->viewPath . '.create', compact('page', 'variants', 'company'));
        }
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        $validator = \Validator::make($request->all(), 
                            [ 'name' => 'required|unique:companies,name,'.$company->id],
                            [ 'name.required' => 'Please enter Company name']);

        if ($validator->passes()) {
            if($company) {
                $company->name          = $request->name;
                $company->details       = $request->details;
                $company->contact_name  = $request->contactName;
                $company->email         = $request->email;
                $company->contact       = $request->contact;
                $company->address       = $request->address;
                $company->save();
                return ['flagError' => false, 'message' =>  Str::singular($this->title). " details updated successfully"];
            }
            return ['flagError' => true, 'message' => "Errors Occurred. Please check !",  'error' => $validator->errors()->all()];
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check !",  'error' => $validator->errors()->all()];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $company->delete();
        return ['flagError' => false, 'message' =>  Str::singular($this->title). " disabled successfully"];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function restore($id, Request $request)
    {
        $company   = Company::where('id', $id)->withTrashed()->first();
        $company->restore();
        return ['flagError' => false, 'message' => Str::singular($this->title). " enabled successfully"];
    }

    /**
     * Update status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        $company       = Company::findOrFail($request->post_id);
        if ($company) {
            $status             = ($company->status == 0)?1:0;
            $company->status    = $status;
            $company->save();
            return ['flagError' => false, 'message' =>  Str::singular($this->title). " status updated successfully"];
        }
        return ['flagError' => true, 'message' => "Errors occurred Please check !",  'error'=>$validator->errors()->all()];
    }
}