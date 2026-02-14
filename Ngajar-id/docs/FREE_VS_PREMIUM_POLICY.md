# Ngajar.ID - Free vs Premium Policy

## 🎯 PRINSIP UTAMA: "Education First, Premium as Enhancement"

### **70% FREE - 30% PREMIUM RULE**

---

## ✅ YANG HARUS GRATIS (FREE TIER)

### **1. Kelas Dasar (Fundamental)**

- ✅ Semua kelas dengan kategori "Beginner"
- ✅ Materi fundamental (HTML, CSS, Design Basics, dll)
- ✅ Akses ke komunitas & forum
- ✅ Progress tracking dasar

**Implementasi:**

```php
// Kelas model - tambahkan scope
public function scopeFree($query) {
    return $query->where('is_premium', false);
}

// Default: Kelas baru = GRATIS
// Pengajar bisa upgrade ke premium jika mau
```

### **2. Learning Paths Dasar**

- ✅ Minimal 1 learning path gratis per kategori
- ✅ Contoh: "Web Development Fundamentals" (gratis)
- ✅ "Advanced Web Development" (premium)

**Implementasi:**

```php
// LearningPath model
protected $fillable = [
    'is_free', // NEW FIELD
    // ... existing fields
];

// Policy: Setiap kategori HARUS punya minimal 1 path gratis
```

### **3. Sertifikat Partisipasi**

- ✅ Sertifikat "Completion" untuk kelas gratis
- ⭐ Sertifikat "Certified" untuk premium paths (dengan logo & validasi)

### **4. Beasiswa Full Access**

- ✅ User dengan beasiswa = akses SEMUA konten (gratis + premium)
- ✅ Sudah implemented ✅

---

## 💎 YANG BOLEH PREMIUM

### **1. Learning Paths Advanced**

- 💎 Paths dengan level "Advanced"
- 💎 Paths dengan mentoring 1-on-1
- 💎 Paths dengan project review

### **2. Materi Eksklusif**

- 💎 Source code lengkap project
- 💎 Template & boilerplate
- 💎 Video tutorial HD
- 💎 E-book comprehensive

### **3. Sertifikasi Resmi**

- 💎 Sertifikat dengan validasi
- 💎 Badge LinkedIn-ready
- 💎 Portfolio showcase

### **4. Mentoring & Support**

- 💎 1-on-1 mentoring session
- 💎 Code review dari pengajar
- 💎 Priority support

---

## 🚫 YANG TIDAK BOLEH DI-PAYWALL

### **NEVER Premium:**

1. ❌ Akses ke platform
2. ❌ Browsing kelas
3. ❌ Join kelas gratis
4. ❌ Komunitas & forum
5. ❌ Progress tracking
6. ❌ Kelas fundamental/beginner

### **Alasan:**

> "Pendidikan dasar adalah hak, bukan privilege"

---

## 📊 PRICING GUIDELINE

### **Token Pricing:**

- Kelas Premium: 50-200 token
- Learning Path Premium: 300-500 token
- Materi Eksklusif: 20-100 token
- Mentoring 1-on-1: 100-300 token/session

### **Free Alternative:**

- Setiap konten premium HARUS punya alternatif gratis
- Contoh: "React Advanced" (premium) vs "React Basics" (gratis)

---

## 🎁 SCHOLARSHIP PROGRAM

### **Auto-Scholarship Criteria:**

1. ✅ Pelajar/Mahasiswa (verifikasi KTM)
2. ✅ Pengangguran (verifikasi)
3. ✅ Relawan aktif (kontribusi konten)
4. ✅ Top performer (leaderboard top 10%)

### **Benefit:**

- Full access ke semua konten
- Priority support
- Certificate gratis

---

## 📈 METRICS TO TRACK

### **Balance Indicators:**

- Free content usage: Target 60-70%
- Premium conversion: Target 10-15%
- Scholarship users: Target 20-30%
- Churn rate: < 20%

### **Red Flags:**

- ⚠️ Premium conversion > 30% = Terlalu banyak paywall
- ⚠️ Free usage < 50% = Konten gratis kurang menarik
- ⚠️ Scholarship < 10% = Program kurang agresif

---

## ✅ ACTION ITEMS

### **Immediate (Week 1):**

1. [ ] Add `is_free` field to `learning_paths` table
2. [ ] Set default: Beginner paths = FREE
3. [ ] Update UI: Clear "FREE" vs "PREMIUM" badges
4. [ ] Create scholarship application page

### **Short-term (Month 1):**

1. [ ] Ensure 70% kelas = gratis
2. [ ] Create 1 free learning path per kategori
3. [ ] Implement scholarship auto-approval
4. [ ] Add "Free Alternative" recommendations

### **Long-term (Quarter 1):**

1. [ ] Analytics dashboard for free/premium balance
2. [ ] A/B testing pricing
3. [ ] Community-driven free content
4. [ ] Partnership untuk scholarship funding

---

## 💡 POSITIONING STATEMENT

**Tagline:**

> "Belajar Gratis dari Relawan Expert. Upgrade untuk Sertifikasi & Mentoring."

**Value Prop:**

- 🎓 Kelas fundamental: GRATIS
- 📚 Learning paths dasar: GRATIS
- 🎁 Beasiswa untuk yang membutuhkan: GRATIS
- 💎 Advanced paths & mentoring: PREMIUM (affordable)

**Messaging:**

- "Mulai belajar gratis, upgrade saat siap"
- "Tidak mampu bayar? Apply beasiswa!"
- "Relawan mengajar, murid berkembang"
