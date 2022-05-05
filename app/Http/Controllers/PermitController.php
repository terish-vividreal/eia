<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\MoveToPermitFormRequest​;
use App\Helpers\HtmlHelper;
use App\Models\Permit;
use App\Models\Eia;
use Validator;
use DataTables;

class PermitController extends Controller
{

    protected $title    = 'Permit';
    protected $viewPath = 'permits';
    protected $route    = 'permits';


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
        $page->title            = $this->title;
        $page->route            = url($this->route); 
        return view($this->viewPath . '.list', compact('page', 'variants'));
    }

    /**
     * Display a listing of the resource in datatable.
     * @throws \Exception
     */
    public function lists(Request $request, $projectId = null)
    {
        $detail     =  Permit::select(['permits.*'])->whereActive(1);
        
        if (isset($request->form)) {

        }
        else {
            $detail         = $detail->orderBy('id', 'DESC');
        }
        return Datatables::eloquent($detail)
            ->addIndexColumn()
            ->addColumn('project_id', function($detail) {
                $link   = '';
                $link   .= '<a href="projects/'. $detail->eia->id.'">'.$detail->eia->project->project_code_id.'</a>';
                return $link ;
            })
            ->editColumn('permit_code', function($detail) {
                $link   = '';
                $link   .= '<a href="'.$this->route .'/'. $detail->id.'">'.$detail->permit_code.'</a>';
                return $link ;
            })
            ->editColumn('status', function($detail) {
                $status = '';
                $status .= HtmlHelper::statusText($detail->stage_id, $detail->status);
                return $status;
            })
            ->editColumn('date_of_approval', function($detail) {
                return date('d/m/Y', strtotime( $detail->date_of_approval)); 
            })
            // ->removeColumn('id')
            // ->escapeColumns([])
            ->make(true);                    
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
    public function store(MoveToPermitFormRequest​ $request)
    {     
        // Update EIA Status to Permit
        Eia::where('id', $request->eia_id)->update(['is_permit' => 1]);

        $input                      = $request->all();
        $input['created_by']        = auth()->user()->id;
        $permit                     = Permit::create($input);
        return redirect('permits')->with('success', 'Permit created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Permit $permit)
    {
        echo "<pre>"; print_r($permit); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
