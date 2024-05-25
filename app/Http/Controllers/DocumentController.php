<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Http\Controllers\BaseController;
use App\Http\Requests\DocumentRequest;
use App\Http\Resources\DocumentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            $fileName = pathinfo($path, PATHINFO_FILENAME);
            $inputs = $request->all();            
            $inputs['file_path'] = $fileName.'.'.$extension;
            $inputs['content_url'] = Storage::url($path);
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
        $document = Document::with('customer')->find($id);
        if(empty($document)){
            return $this->sendError('File not found', [], 404);
        }
        $user = auth()->user();
        $filePath = 'public/documents/'.$document->file_path;
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $fileName = Str::replace(' ', '-', strtolower($document->customer->name)).'-'.Str::replace(' ','-', $document->type).'.'.$extension;
               
        if (!Storage::exists($filePath)) {
            return $this->sendError('File not found', [], 404);
        }

        return $this->sendResponse([
            "file" => base64_encode(Storage::get($filePath)),
            "name" => $fileName
        ], 'Document has been downloaded successfully.');
    }
}
