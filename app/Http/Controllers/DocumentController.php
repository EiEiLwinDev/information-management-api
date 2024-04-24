<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Http\Controllers\BaseController;
use App\Http\Requests\DocumentRequest;
use App\Http\Resources\DocumentResource;
use Illuminate\Http\JsonResponse;

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
            $document = Document::create($request->validated());
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
}
