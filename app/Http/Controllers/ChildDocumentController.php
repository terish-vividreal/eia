<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FunctionHelper;
use App\Helpers\HtmlHelper;
use App\Models\DocumentFile;
use App\Models\Document;
use App\Models\Project;
use App\Models\DocumentStatus;
use App\Models\EiaStage;

class ChildDocumentController extends Controller
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
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // $posts = Post::with(['author' => function ($q){
            //     $q->orderBy('name', 'DESC');
            // }])
            



            $document   = Document::find($request->documentId);
            
            
            // ->with(['children' => function ($q){
            //                     $q->orderBy('id', 'DESC');
            //                 }])->get(); 



            if ($document) {
                $subDocumentsHTML       =  $this->loadSubDocumentsHTML($document);
                // $subDocumentsHTML    = view($this->viewPath . '.list_child', compact('document'))->render();  
                return ['flagError' => false, 'document' => $document, 'html' => $subDocumentsHTML];
            } else {
                return ['flagError' => true, 'message' => "Data not found, Try again! "];
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($documentId)
    {
        $document                       = Document::find($documentId); 
        if($document) {
            $page                       = collect();
            $variants                   = collect();
            $user                       = auth()->user();
            $eia                        = $document->eia;
            $page->title                = $this->title;
            $page->route                = url($this->route.'/'.$documentId.'/store'); 
            $page->projectRoute         = url('projects/'.$document->eia->project_id); 
            $page->documentRoute        = url($this->route.'/'.$document->id); 
            $page->eiaRoute             = url('eias/'.$document->eia->id); 
            $variants->documentStatuses = DocumentStatus::pluck('name','id'); 
            $variants->stages           = EiaStage::pluck('name','id'); 
            return view($this->viewPath . '.create_child', compact('page', 'variants', 'eia', 'document', 'user'));
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

        $document                       = new Document();
        $document->eia_id               = $request->eiaId;
        $document->document_number      = $request->documentNumber;
        $document->date_of_entry        = FunctionHelper::dateToUTC($request->dateOfEntry, 'Y-m-d H:i:s');
        $document->code                 = FunctionHelper::documentCode();
        $document->brief_description    = $request->briefDescription;
        $document->uploaded_by          = auth()->user()->id;
        $document->created_by           = auth()->user()->id;
        $document->stage_id             = $request->stage;
        $document->parent_id            = $request->documentId;
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
            return response()->json(['filename' => $fileName, 'name' => $name ]);
        }
    }

    /**
     * Return HTML data.
     *
     */
    public function loadSubDocumentsHTML($document) 
    {

        $html = '';
        foreach($document->children as $child) {
            $html .= '<div class="card animate fadeUp"><div class="card-content"><div class="row" id="product-four"><div class="col m4 s12">';
            $html .= '<h5>' . HtmlHelper::statusText($child->stage_id, $child->status) . '</h5>';
            $html .= '<img src="'.$child->latestFile->file_name.'" class="responsive-img" style="max-width: 75% !important" alt=""></div>';

            $html .= '<div class="col m4 s12"><p style="text-align: right;"></p><table class="striped"><tbody>';
            $html .= '<tr><td>Date of Entry:</td><td>'.$child->date_of_entry.'</td></tr>' ;    
            $html .= '<tr><td>Uploaded By:</td><td>'.$child->uploadedBy->name.'</td></tr>' ;    
            $html .= '<tr><td>Description:</td><td>'.$child->brief_description.'</td></tr>' ;    
            $html .= '</tbody></table></div>';

            $html .= '<div class="col m4 s12"><div class="row commentContainer" id="commentContainer'.$child->id.'">';
            $html .= '<div class="input-field col m10 s12 commentArea">';
            $html .= '<textarea id="comment" class="materialize-textarea commentField" name="comment" cols="50" rows="10" placeholder="Comments"></textarea>';
            // $html .= '<label for="comment" class="label-placeholder active">  </label>';
            $html .= '<div id="documentComment-error-'.$child->id.'" class="error documentComment-error" style="display:none;"></div></div>';
            $html .= '<div class="input-field col m2 s12" style="margin-top: 37px; ! important">';
            $html .= '<a href="javascript:" class="text-sub subDocument-save-comment-btn" data-id="'.$child->id.'"><i class="material-icons mr-2"> send </i></a></div> </div>';
            $html .= '<div class="app-email" id="latestComment'.$child->id.'"></div>';           
            
            if(count($child->comments) > 0) {
                foreach($child->comments as $comment) {
                    $html .= '<div class="app-email" id="docCommentsDiv'.$document->id.'"><div class="content-area"><div class="app-wrapper"><div class="card card card-default scrollspy border-radius-6 fixed-width">';
                    $html .= '<div class="card-content p-0 pb-2"><div class="collection email-collection"><div class="email-brief-info collection-item animate fadeUp ">';
                    $html .= '<a class="list-content" href="javascript:"><div class="list-title-area"><div class="user-media">';
                    $html .= '<img src="'.$comment->commentedBy->profile.'" alt="" class="circle z-depth-2 responsive-img avtar"><div class="list-title">'.$comment->commentedBy->name.'</div></div></div>';
                    $html .= '<div class="list-desc">'.$comment->comment.'</div></a><div class="list-right"><div class="list-date">'.$comment->created_at->format('M d, h:i A').'</div>';
                    $html .= '</div></div></div></div></div></div></div></div>';
                }
            }

            $html .= '</div></div></div></div>' ;    
        }
        return $html;
    }
}
