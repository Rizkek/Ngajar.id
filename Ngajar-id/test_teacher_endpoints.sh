#!/bin/bash
# Teacher Endpoints Testing - cURL bash script
# Run: bash test_teacher_endpoints.sh

BASE_URL="http://localhost:8000"
TEACHER_TOKEN="your_teacher_token_here"

echo "=================================="
echo "Teacher Endpoints Test Suite"
echo "=================================="
echo ""

# Note: Update TEACHER_TOKEN with actual token from login
echo "📝 Before running these tests:"
echo "1. Get a teacher token by logging in as a teacher user"
echo "2. Replace 'your_teacher_token_here' with the actual token"
echo "3. Run with: bash test_teacher_endpoints.sh"
echo ""

# Test 1: Get all teacher's courses
echo "1. GET /api/v1/teacher/kelas - Get all teacher's courses"
curl -X GET "$BASE_URL/api/v1/teacher/kelas" \
  -H "Authorization: Bearer $TEACHER_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
echo ""
echo ""

# Test 2: Create a new course
echo "2. POST /api/v1/teacher/kelas - Create new course"
curl -X POST "$BASE_URL/api/v1/teacher/kelas" \
  -H "Authorization: Bearer $TEACHER_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "judul": "Advanced React.js",
    "deskripsi": "Learn advanced React concepts and patterns",
    "kategori": "Programming",
    "status": "draft"
  }'
echo ""
echo ""

# Test 3: Get course details
echo "3. GET /api/v1/teacher/kelas/{id} - Get course details"
curl -X GET "$BASE_URL/api/v1/teacher/kelas/1" \
  -H "Authorization: Bearer $TEACHER_TOKEN" \
  -H "Accept: application/json"
echo ""
echo ""

# Test 4: Update course
echo "4. PUT /api/v1/teacher/kelas/{id} - Update course"
curl -X PUT "$BASE_URL/api/v1/teacher/kelas/1" \
  -H "Authorization: Bearer $TEACHER_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "judul": "Updated Course Title",
    "status": "aktif"
  }'
echo ""
echo ""

# Test 5: Get students in course
echo "5. GET /api/v1/teacher/kelas/{id}/students - Get enrolled students"
curl -X GET "$BASE_URL/api/v1/teacher/kelas/1/students" \
  -H "Authorization: Bearer $TEACHER_TOKEN" \
  -H "Accept: application/json"
echo ""
echo ""

# Test 6: Get materials in course
echo "6. GET /api/v1/teacher/kelas/{id}/materi - Get course materials"
curl -X GET "$BASE_URL/api/v1/teacher/kelas/1/materi" \
  -H "Authorization: Bearer $TEACHER_TOKEN" \
  -H "Accept: application/json"
echo ""
echo ""

# Test 7: Add material to course
echo "7. POST /api/v1/teacher/kelas/{id}/materi - Add material"
curl -X POST "$BASE_URL/api/v1/teacher/kelas/1/materi" \
  -H "Authorization: Bearer $TEACHER_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "judul": "Introduction to Hooks",
    "deskripsi": "Learn about React Hooks and how to use them",
    "tipe": "video"
  }'
echo ""
echo ""

# Test 8: Teacher dashboard
echo "8. GET /api/v1/teacher/dashboard-api - Get teacher dashboard stats"
curl -X GET "$BASE_URL/api/v1/teacher/dashboard-api" \
  -H "Authorization: Bearer $TEACHER_TOKEN" \
  -H "Accept: application/json"
echo ""
echo ""

echo "=================================="
echo "Tests Complete!"
echo "=================================="
