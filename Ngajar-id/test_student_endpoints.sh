#!/bin/bash

# Student Endpoints Test Suite

BASEURL="http://127.0.0.1:8000/api/v1"

echo "🧪 TESTING STUDENT ENDPOINTS"
echo "════════════════════════════════════════════════════════════"
echo ""

# Test 1: Get courses list (public endpoint)
echo "1️⃣ GET /api/v1/landing/courses (Public)"
echo "─────────────────────────────────────────────────────────"
curl -s "$BASEURL/landing/courses" | jq '.' | head -30
echo ""
echo ""

# Test 2: Get courses with search (protected)
# First need to get a token
echo "2️⃣ Logging in to get auth token..."
echo "─────────────────────────────────────────────────────────"
RESPONSE=$(curl -s -X POST "$BASEURL/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "ahmad@student.id",
    "password": "password"
  }')

TOKEN=$(echo $RESPONSE | jq -r '.data.token // empty')

if [ -z "$TOKEN" ]; then
    echo "❌ Failed to get token"
    echo $RESPONSE | jq '.'
    exit 1
fi

echo "✅ Got token: ${TOKEN:0:30}..."
echo ""

# Test 3: Get courses list with auth
echo "3️⃣ GET /api/v1/kelas (Authenticated - Browse courses)"
echo "─────────────────────────────────────────────────────────"
curl -s -H "Authorization: Bearer $TOKEN" "$BASEURL/kelas" | jq '.data | length'
echo "Courses available"
echo ""

# Test 4: Get course detail
echo "4️⃣ GET /api/v1/kelas/1 (Course Detail)"
echo "─────────────────────────────────────────────────────────"
curl -s -H "Authorization: Bearer $TOKEN" "$BASEURL/kelas/1" | jq '.data.course'
echo ""

# Test 5: Enroll to course
echo "5️⃣ POST /api/v1/kelas/1/enroll (Enroll to Course)"
echo "─────────────────────────────────────────────────────────"
curl -s -X POST -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  "$BASEURL/kelas/1/enroll" \
  -d '{}' | jq '.data'
echo ""

# Test 6: Get my courses
echo "6️⃣ GET /api/v1/my-courses (My Courses)"
echo "─────────────────────────────────────────────────────────"
curl -s -H "Authorization: Bearer $TOKEN" "$BASEURL/my-courses" | jq '.data[] | {kelas_id, judul, progress, status}'
echo ""

# Test 7: Complete material
echo "7️⃣ POST /api/v1/my-progress/materi/1/complete (Complete Material)"
echo "─────────────────────────────────────────────────────────"
curl -s -X POST -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  "$BASEURL/my-progress/materi/1/complete" \
  -d '{}' | jq '.data'
echo ""

# Test 8: Get progress
echo "8️⃣ GET /api/v1/my-progress (View All Progress)"
echo "─────────────────────────────────────────────────────────"
curl -s -H "Authorization: Bearer $TOKEN" "$BASEURL/my-progress" | jq '.'
echo ""

echo "════════════════════════════════════════════════════════════"
echo "✅ STUDENT ENDPOINTS TEST COMPLETE"
