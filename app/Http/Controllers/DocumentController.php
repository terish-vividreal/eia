<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Helpers\HtmlHelper;
use App\Helpers\FunctionHelper;
use App\Models\DocumentStatus;
use App\Models\DocumentFile;
use App\Models\EiaStage;
use App\Models\File;
use App\Models\Eia;
use Validator;
use DataTables;
use Carbon;

class DocumentController extends Controller
{
    protected $title        = 'Documents';
    protected $viewPath     = 'documents';
    protected $route        = 'documents';
    protected $uploadPath   = 'documents';

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
    public function create($eiaId)
    {
        $eia                = Eia::find($eiaId); 
        if($eia) {
            $page                       = collect();
            $variants                   = collect();
            $user                       = auth()->user();
            $page->title                = $this->title;
            $page->route                = url($this->route); 
            $page->projectRoute         = url('projects/'.$eia->project_id); 
            $page->eiaRoute             = url('eias/'.$eia->id); 
            $variants->documentStatuses = DocumentStatus::pluck('name','id'); 
            $variants->stages           = EiaStage::pluck('name','id'); 
            return view($this->viewPath . '.create', compact('page', 'variants', 'eia', 'user'));
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
                            [ 'documentNumber' => 'required|unique:documents,document_number'],
                            [ 'documentNumber.unique' => 'Document Number is already used', 'codeId.required' => 'Please enter Document Number ']);

        if ($validator->passes()) {
            $document                       = new Document();
            $document->eia_id               = $request->eiaId;
            $document->document_number      = $request->documentNumber;
            $document->date_of_entry        = FunctionHelper::dateToUTC($request->dateOfEntry, 'Y-m-d H:i:s');
            $document->code                 = FunctionHelper::documentCode();
            $document->title                = $request->title;
            $document->brief_description    = $request->briefDescription;
            $document->uploaded_by          = auth()->user()->id;
            $document->created_by           = auth()->user()->id;
            $document->document_type        = $request->documentType;
            $document->comment              = $request->comment;
            $document->stage_id             = $request->stage;
            $document->status               = $request->status;
            $document->save();

            if($document) {
                foreach($request->documents as $key => $documentFile) {
                    $docFile                   = new DocumentFile();
                    $docFile->document_id      = $document->id;
                    $docFile->name             = $documentFile;
                    $docFile->original_name    = $request->documentOrg[$key];
                    $docFile->path             = '/app/public/'.$this->uploadPath.'/'.$documentFile;
                    $docFile->uploaded_by      = auth()->user()->id;
                    $docFile->save();
                }
            }
            
            return ['flagError' => false, 'message' => $this->title. " added successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check !",  'error' => $validator->errors()->all()];
    }

    /**
     * Display a listing of the resource in datatable.
     * @throws \Exception
     */
    public function lists(Request $request, $eiaId)
    {
        $detail     =  Document::select(['documents.*'])->where('eia_id', $eiaId);
        return Datatables::eloquent($detail)
            ->addIndexColumn()
            ->editColumn('date_of_entry', function($detail) {
                return $detail->formatted_date_of_entry; 
            })
            ->editColumn('title', function($detail) {
                $title   = '';
                $title   = Str::limit(strip_tags($detail->title), 40);
                return $title ;
            })
            ->editColumn('status', function($detail) {
                $status = '';
                $status .= HtmlHelper::statusText($detail->stage_id, $detail->status);
                return $status;
            })
            ->editColumn('brief_description', function($detail) {
                $description    = '';
                $description    = Str::limit(strip_tags($detail->brief_description), 40);

                if (strlen(strip_tags($detail->brief_description)) > 50) {
                    $description .= '<a href="javascript:void(0);" class="view-more-details" data-column="brief_description" data-url="'.url($this->route.'/'.$detail->id).'" data-id="'.$detail->id.'" >View</a>';
                }
                return $description ;
            })
            ->editColumn('document_type', function($detail) {
                $documentType   = '';
                $documentType = ($detail->document_type == 1) ? 'Hard Copy' : 'Soft Copy';
                return $documentType;
            })
            ->editColumn('comment', function($detail) {
                $comment    = '';
                $comment    = Str::limit(strip_tags($detail->comment), 40);

                if (strlen(strip_tags($detail->comment)) > 50) {
                    // onclick='viewMoreDetails(\"".$detail->comment."\")'
                    $comment .= '<a href="javascript:void(0);" class="view-more-details" data-column="comment" data-url="'.url($this->route.'/'.$detail->id).'" data-id="'.$detail->id.'" >View</a>';
                }
                return $comment ;
            })
            ->addColumn('action', function($detail) use ($eiaId) {
                $action = '';
                if ($detail->deleted_at == null) { 
                    $action .= HtmlHelper::editButton(url('eias/'.$eiaId.'/documents/'.$detail->id.'/edit'), $detail->id);
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
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Document $document)
    {
        if ($request->ajax()) {

            if ($document) {
                return ['flagError' => false, 'document' => $document];
            } else {
                return ['flagError' => true, 'message' => "Data not found, Try again!"];
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $eiaId, $id)
    {
        $eia        = Eia::find($eiaId);
        if($eia) {
            $document            = Document::find($id);
            if($document) {
                $page                       = collect();
                $variants                   = collect();
                $user                       = auth()->user();
                $page->title                = $this->title;
                $page->route                = url($this->route);
                $page->projectRoute         = url('projects/'.$eia->project_id); 
                $page->eiaRoute             = url('eias/'.$eia->id); 
                $variants->documentStatuses = DocumentStatus::pluck('name','id'); 
                $variants->stages           = EiaStage::pluck('name','id'); 
                return view($this->viewPath . '.create', compact('page', 'variants', 'eia', 'document', 'user'));
            }
            abort(404);
        }
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
    {
        $validator = \Validator::make($request->all(), 
                            [ 'documentNumber' => 'required|unique:documents,document_number,'.$document->id],
                            [ 'documentNumber.unique' => 'Document Number is already used', 'codeId.required' => 'Please enter Document Number ']);

        if ($validator->passes()) {
            $document->eia_id               = $request->eiaId;
            $document->document_number      = $request->documentNumber;
            $document->date_of_entry        = FunctionHelper::dateToUTC($request->dateOfEntry, 'Y-m-d H:i:s');
            $document->title                = $request->title;
            $document->brief_description    = $request->briefDescription;
            $document->document_type        = $request->documentType;
            $document->comment              = $request->comment;
            $document->stage_id             = $request->stage;
            $document->status               = $request->status;
            $document->save();
            return ['flagError' => false, 'message' => $this->title. " updated successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check !",  'error' => $validator->errors()->all()];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        //
    }

    /**
     * Upload Document file.
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadDocument(Request $request)
    {  
        if ($request->file('file')) {

            $image      = $request->file('file');
            $name       = $image->getClientOriginalName();
            $fileName   = FunctionHelper::documentName($name);

            if(env('APP_ENV') == 'local') {

                $path = storage_path().'/app/public/'.$this->uploadPath;

                if (!file_exists($path)) {
                    Storage::makeDirectory($path, 0755);
                }

                $path       = $image->storeAs($this->uploadPath, $fileName, 'public');

            } else {
                // Store Image in S3
                // $request->image->storeAs('images', $fileName, 's3');
            }
            return response()->json([ 'filename' => $fileName, 'name' => $name ]);
        }
    }

    /**
     * Remove Document file.
     *
     * @return \Illuminate\Http\Response
     */
    public function fileRemove(Request $request)
    {
        $name =  $request->get('name');
        File::where(['name' => $name])->delete();
        
        if ($name != '') {
            \Illuminate\Support\Facades\Storage::delete('public/documents/' . $name);
        }
        return $name;
    }
}
