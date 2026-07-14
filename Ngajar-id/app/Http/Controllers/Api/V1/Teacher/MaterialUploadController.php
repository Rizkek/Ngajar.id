<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\Teacher\MaterialManagementService;

class MaterialUploadController extends Controller
{
    use ApiResponse;

    protected $materialService;

    public function __construct(MaterialManagementService $materialService)
    {
        $this->materialService = $materialService;
    }

    /**
     * POST /api/v1/materials/upload
     * Upload material file to cloud storage
     */
    public function upload(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'materi_id' => 'required|exists:materi,materi_id',
                'file' => 'required|file|max:500000',
                'file_type' => 'required|in:video,pdf,image,document',
            ]);

            if ($validator->fails()) {
                return $this->error('Validation failed: ' . implode(', ', $validator->errors()->all()), 400);
            }

            $user = $request->user() ?? Auth::user();
            $fileData = $this->materialService->uploadFile($user, $request->materi_id, $request->file('file'));

            return $this->success($fileData, 'File uploaded successfully', 201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Material or course not found', 404);
        } catch (\Exception $e) {
            return $this->error('Failed to upload file: ' . $e->getMessage(), 400);
        }
    }

    /**
     * DELETE /api/v1/materials/{id}/file
     * Delete uploaded file
     */
    public function deleteFile($id, Request $request)
    {
        try {
            $user = $request->user() ?? Auth::user();
            $this->materialService->deleteUploadedFile($user, $id);

            return $this->success([], 'File deleted successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Material not found', 404);
        } catch (\Exception $e) {
            return $this->error('Failed to delete file: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/materials/{id}/download
     * Download or stream material file
     */
    public function download($id, Request $request)
    {
        try {
            $user = $request->user() ?? Auth::user();
            $downloadData = $this->materialService->processDownload($user, $id);

            return $this->success($downloadData, 'Download URL generated');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Material not found', 404);
        } catch (\Exception $e) {
            return $this->error('Failed to process download: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/materials/{id}/stream
     * Stream video material (for video players)
     */
    public function stream($id, Request $request)
    {
        try {
            $user = $request->user() ?? Auth::user();
            $streamData = $this->materialService->processStream($user, $id, $request->input('timestamp', 0));

            return $this->success($streamData, 'Video stream ready');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Material not found', 404);
        } catch (\Exception $e) {
            return $this->error('Failed to stream video: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/materials/stats
     * Get upload statistics
     */
    public function stats(Request $request)
    {
        try {
            $user = $request->user() ?? Auth::user();
            $stats = $this->materialService->getUploadStats($user);

            return $this->success($stats, 'Upload statistics retrieved successfully');

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve statistics: ' . $e->getMessage(), 400);
        }
    }
}

