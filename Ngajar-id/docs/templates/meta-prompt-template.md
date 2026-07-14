# Standard Documentation Meta Prompt
*Template ini adalah **Single Source of Truth** untuk menghasilkan seluruh dokumen di dalam Ngajar.id Documentation Framework. Setiap kali AI diminta untuk membuat dokumen baru, gunakan struktur meta prompt ini secara utuh untuk memastikan kualitas Enterprise-grade.*

---

## 📌 META PROMPT STRUCTURE

Gunakan prompt berikut saat menginstruksikan AI untuk menulis atau memperbarui dokumen:

```text
Kamu adalah seorang Lead Software Architect dan Technical Writer profesional.
Tugas kamu adalah menyusun dokumen [NAMA_DOKUMEN] untuk proyek Ngajar.id.
Pastikan dokumen ini sangat lengkap, berstandar Enterprise, tidak setengah-setengah, dan bisa langsung digunakan oleh tim developer maupun stakeholder.

Ikuti struktur berikut dalam menyusun dokumen:

1. ROLE
Jelaskan peranmu dalam dokumen ini (Misal: Sebagai System Architect yang mendefinisikan arsitektur sistem).

2. PROJECT CONTEXT
Berikan ringkasan singkat bahwa ini adalah untuk Ngajar.id, sebuah platform LMS (Learning Management System) berbasis Laravel, Livewire, Tailwind CSS, dan Supabase (atau sebutkan spesifik konteks modul yang sedang didokumentasikan).

3. OBJECTIVE
Apa tujuan spesifik dari dokumen ini? (Misal: Mendefinisikan struktur database, menjelaskan flow otentikasi).

4. DOCUMENT PURPOSE
Mengapa dokumen ini penting dan kapan harus dibaca oleh tim?

5. TARGET AUDIENCE
Siapa yang akan membaca dokumen ini? (Misal: Backend Developer, QA Engineer, Project Manager).

6. ASSUMPTIONS
Asumsi dasar apa yang digunakan dalam pembuatan dokumen ini? (Misal: Pembaca sudah memahami konsep MVC di Laravel).

7. CONSTRAINTS
Batasan teknis atau bisnis apa yang ada? (Misal: Harus menggunakan PostgreSQL melalui Supabase, dilarang menggunakan jQuery).

8. BEST PRACTICES
Sebutkan best practices industri yang diterapkan dalam dokumen ini (Misal: SOLID principles, RESTful API conventions, Semantic Versioning).

9. CONTENT STRUCTURE
(INILAH BAGIAN UTAMA DOKUMEN - Isi dengan konten detail sesuai jenis dokumennya. HARUS SANGAT DETAIL dan LENGKAP. Gunakan tabel, diagram C4/ERD berbasis Mermaid.js jika diperlukan, snippet kode referensi, dan penjelasan komprehensif).

10. OUTPUT FORMAT
Pastikan format output menggunakan GitHub Flavored Markdown (GFM). Gunakan Headers yang rapi, Callouts/Alerts (> [!IMPORTANT], dll), dan Code Blocks dengan syntax highlighting yang tepat.

11. QUALITY CHECKLIST
Berikan checklist (menggunakan markdown checkbox) berisi kriteria bahwa dokumen ini sudah "Done" dan memenuhi standar.

12. SELF REVIEW
Sertakan paragraf singkat evaluasi apakah dokumen ini sudah menjawab semua kebutuhan atau jika ada area yang masih perlu diperdalam.

13. FINAL VALIDATION
Berikan instruksi langkah selanjutnya bagi pembaca (Misal: "Jika disetujui, silakan implementasikan ke dalam folder `app/Services`").
```

---

## 🎯 DAFTAR DOKUMEN & FOKUS (SDLC)

Berikut adalah panduan bagi tim/AI tentang apa yang harus di-generate berdasarkan fase SDLC:

| Phase | Dokumen | Fokus / Output |
|---|---|---|
| **01-Business** | Vision Document | Visi, misi, problem, target pasar, value proposition |
| **02-Requirement** | BRD, PRD, SRS | Kebutuhan bisnis, scope produk, functional & non-functional reqs |
| **03-Architecture** | SAD, C4, ERD | Diagram arsitektur (Mermaid), struktur layer, relasi database |
| **04-Design** | Design System, Flow | Typography, spacing, komponen UI, User Journey/Flow |
| **05-Development** | API, Backend & Frontend Arch | Endpoint API, Service Layer flow, layouting Livewire, Coding Standards |
| **06-Testing** | Test Strategy, QA | Unit/Feature Test plan, UAT checklist |
| **07-Release** | DevOps, Deployment Guide | Setup server, CI/CD pipeline, monitoring |
| **08-Maintenance** | ADR, Changelog, Maintenance Guide | Architecture Decision Records, logging, backup plan |

*Penting: Jangan pernah men-generate dokumen yang hanya berisi poin-poin singkat (bullet points tanpa penjelasan teknis). Setiap poin harus diuraikan dengan konteks implementasi di Ngajar.id.*
