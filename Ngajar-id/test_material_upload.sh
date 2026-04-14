#!/bash
# Material Upload Endpoints - cURL Test Script

BASE_URL="http://localhost:8000"
TEACHER_TOKEN="your_teacher_token"
STUDENT_TOKEN="your_student_token"

echo "=============================================="
echo "Material Upload Endpoints Test Suite"
echo "=============================================="
echo ""

echo "1. POST /api/v1/materials/upload - Upload material file"
curl -X POST "$BASE_URL/api/v1/materials/upload" \
  -H "Authorization: Bearer $TEACHER_TOKEN" \
  -F "materi_id=1" \
  -F "file=@/path/to/file.pdf" \
  -F "file_type=pdf"
echo ""
echo ""

echo "2. GET /api/v1/materials/{id}/download - Download material"
curl -X GET "$BASE_URL/api/v1/materials/1/download" \
  -H "Authorization: Bearer $STUDENT_TOKEN" \
  -H "Accept: application/json"
echo ""
echo ""

echo "3. POST /api/v1/materials/{id}/stream - Stream video"
curl -X POST "$BASE_URL/api/v1/materials/5/stream" \
  -H "Authorization: Bearer $STUDENT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"timestamp": 120}'
echo ""
echo ""

echo "4. DELETE /api/v1/materials/{id}/file - Delete file"
curl -X DELETE "$BASE_URL/api/v1/materials/1/file" \
  -H "Authorization: Bearer $TEACHER_TOKEN"
echo ""
echo ""

echo "5. GET /api/v1/materials/stats - Upload statistics"
curl -X GET "$BASE_URL/api/v1/materials/stats" \
  -H "Authorization: Bearer $TEACHER_TOKEN"
echo ""
echo ""

echo "=============================================="
echo "Test Complete!"
echo "=============================================="
