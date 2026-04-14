<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/certificates
     * Get all certificates for current user
     */
    public function index(Request $request)
    {
        try {
            $userId = auth()->id();
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);

            $certificates = DB::table('certificates')
                ->join('kelas', 'certificates.kelas_id', '=', 'kelas.kelas_id')
                ->join('users', 'kelas.pengajar_id', '=', 'users.user_id')
                ->where('certificates.user_id', $userId)
                ->select(
                    'certificates.id',
                    'certificates.certificate_number',
                    'kelas.judul',
                    'users.name as instructor_name',
                    'certificates.issued_at',
                    'certificates.certificate_url',
                    'certificates.grade'
                )
                ->orderBy('certificates.issued_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return $this->successWithPagination(
                $certificates->items(),
                'Certificates retrieved',
                $certificates->total(),
                $perPage,
                $page
            );

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve certificates: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/certificates/{id}
     * Get specific certificate
     */
    public function show($certificateId, Request $request)
    {
        try {
            $userId = auth()->id();

            $certificate = DB::table('certificates')
                ->join('kelas', 'certificates.kelas_id', '=', 'kelas.kelas_id')
                ->join('users', 'kelas.pengajar_id', '=', 'users.user_id')
                ->where('certificates.id', $certificateId)
                ->where('certificates.user_id', $userId)
                ->select(
                    'certificates.id',
                    'certificates.certificate_number',
                    'certificates.user_id',
                    'kelas.judul',
                    'kelas.deskripsi',
                    'users.name as instructor_name',
                    'certificates.issued_at',
                    'certificates.certificate_url',
                    'certificates.grade',
                    'certificates.completion_percentage'
                )
                ->first();

            if (!$certificate) {
                return $this->error('Certificate not found', 404);
            }

            return $this->success($certificate, 'Certificate retrieved');

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve certificate: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/certificates/generate/{kelasId}
     * Generate certificate for completed course
     */
    public function generate($kelasId, Request $request)
    {
        try {
            $userId = auth()->id();

            // Check if user completed the course
            $enrollment = DB::table('kelas_peserta')
                ->where('siswa_id', $userId)
                ->where('kelas_id', $kelasId)
                ->first();

            if (!$enrollment) {
                return $this->error('Not enrolled in this course', 403);
            }

            if ($enrollment->status !== 'completed') {
                return $this->error('Course not completed yet', 403);
            }

            // Check if certificate already exists
            $existing = DB::table('certificates')
                ->where('user_id', $userId)
                ->where('kelas_id', $kelasId)
                ->first();

            if ($existing) {
                return $this->success($existing, 'Certificate already exists', 200);
            }

            // Get course info
            $course = DB::table('kelas')
                ->where('kelas_id', $kelasId)
                ->first();

            // Get user info
            $user = DB::table('users')
                ->where('user_id', $userId)
                ->first();

            // Generate certificate number (unique)
            $certificateNumber = 'CERT-' . strtoupper(uniqid());

            // Calculate grade based on completion percentage
            $grade = 'A';
            if ($enrollment->progress < 100) {
                $grade = $enrollment->progress >= 90 ? 'A' : ($enrollment->progress >= 80 ? 'B' : ($enrollment->progress >= 70 ? 'C' : 'D'));
            }

            // Create certificate record
            $certificate = DB::table('certificates')->insertGetId([
                'user_id' => $userId,
                'kelas_id' => $kelasId,
                'certificate_number' => $certificateNumber,
                'issued_at' => now(),
                'grade' => $grade,
                'completion_percentage' => $enrollment->progress,
                'certificate_url' => '/certificates/' . $certificateNumber . '.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $info= DB::table('certificates')->where('id', $certificate)->first();

            return $this->success($info, 'Certificate generated successfully', 201);

        } catch (\Exception $e) {
            return $this->error('Failed to generate certificate: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/certificates/{id}/download
     * Download certificate as PDF
     */
    public function download($certificateId, Request $request)
    {
        try {
            $userId = auth()->id();

            $certificate = DB::table('certificates')
                ->join('kelas', 'certificates.kelas_id', '=', 'kelas.kelas_id')
                ->join('users', 'kelas.pengajar_id', '=', 'users.user_id')
                ->select(
                    'certificates.*',
                    'kelas.judul',
                    'users.name as instructor_name'
                )
                ->where('certificates.id', $certificateId)
                ->where('certificates.user_id', $userId)
                ->first();

            if (!$certificate) {
                return $this->error('Certificate not found', 404);
            }

            // Generate PDF here (requires a PDF library like TCPDF or mPDF)
            // For now, just return the certificate info with download URL
            $downloadUrl = url('/api/v1/certificates/' . $certificateId . '/pdf');

            return $this->success([
                'certificate' => $certificate,
                'download_url' => $downloadUrl,
                'filename' => 'Certificate_' . $certificate->certificate_number . '.pdf'
            ], 'Certificate download prepared');

        } catch (\Exception $e) {
            return $this->error('Failed to download certificate: ' . $e->getMessage(), 400);
        }
    }

    /**
     * DELETE /api/v1/certificates/{id}
     * Delete certificate
     */
    public function destroy($certificateId, Request $request)
    {
        try {
            $userId = auth()->id();

            $certificate = DB::table('certificates')
                ->where('id', $certificateId)
                ->where('user_id', $userId)
                ->first();

            if (!$certificate) {
                return $this->error('Certificate not found', 404);
            }

            DB::table('certificates')->where('id', $certificateId)->delete();

            return $this->success(null, 'Certificate deleted successfully');

        } catch (\Exception $e) {
            return $this->error('Failed to delete certificate: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/certificates/verify/{certificateNumber}
     * Verify certificate authenticity (public endpoint)
     */
    public function verify($certificateNumber, Request $request)
    {
        try {
            $certificate = DB::table('certificates')
                ->join('kelas', 'certificates.kelas_id', '=', 'kelas.kelas_id')
                ->join('users', 'certificates.user_id', '=', 'users.user_id')
                ->where('certificates.certificate_number', $certificateNumber)
                ->select(
                    'certificates.certificate_number',
                    'certificates.issued_at',
                    'users.name as student_name',
                    'kelas.judul as course_name',
                    'certificates.grade'
                )
                ->first();

            if (!$certificate) {
                return $this->error('Certificate not found', 404);
            }

            return $this->success($certificate, 'Certificate verified successfully');

        } catch (\Exception $e) {
            return $this->error('Failed to verify certificate: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/certificates/stats
     * Get certificate statistics for current user
     */
    public function stats(Request $request)
    {
        try {
            $userId = auth()->id();

            $stats = [
                'total_certificates' => DB::table('certificates')
                    ->where('user_id', $userId)
                    ->count(),
                'by_grade' => DB::table('certificates')
                    ->where('user_id', $userId)
                    ->selectRaw('grade, COUNT(*) as count')
                    ->groupBy('grade')
                    ->get(),
                'avg_completion' => DB::table('certificates')
                    ->where('user_id', $userId)
                    ->avg('completion_percentage'),
            ];

            return $this->success($stats, 'Certificate statistics retrieved');

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve statistics: ' . $e->getMessage(), 400);
        }
    }
}
