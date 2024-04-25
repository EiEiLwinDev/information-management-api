<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Http\Controllers\BaseController;
use App\Http\Requests\DocumentRequest;
use App\Http\Resources\DocumentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DocumentController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = Document::all();
        return $this->sendResponse(DocumentResource::collection($documents), 'Documents  retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DocumentRequest $request)
    {
        try{
            $path = $request->file('document')->store('public/documents');
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $inputs = $request->all();            
            $inputs['file_path'] = ucfirst($inputs['type']) .'.'.$extension;
            $inputs['content_url'] = $path;
            $document = Document::create($inputs);
            return $this->sendResponse(new DocumentResource($document), 'Document created successfully.');
        }catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        if(is_null($document)){
            return $this->sendError('Document not found.');
        }
        return $this->sendResponse(new DocumentResource($document), 'Document retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DocumentRequest $request, Document $document)
    {
        $document->update($request->validated());
        return $this->sendResponse(new DocumentResource($document), 'Document updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        $document->delete();
        return $this->sendResponse([], 'Document deleted successfully.');
    }

    public function download($id)
    {
        $document = Document::find($id);
        $path = storage_path('app/documents/' . $document->file_path);

        if (!Storage::exists($path)) {
            abort(404);
        }
        return $this->sendResponse([
            "file" => base64_encode(Storage::get($path))
        ], 'Document deleted successfully.');
    }
}
