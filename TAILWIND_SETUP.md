# 🎨 Tailwind CSS Setup - Ngajar.id

## ✅ Tailwind CSS Sudah Terkonfigurasi!

Tailwind CSS v4 sudah diintegrasikan dengan Laravel 11 dan Filament 3 di proyek Ngajar.id.

---

## 📁 File Yang Dibuat

### 1. **tailwind.config.js**

File konfigurasi Tailwind dengan:

- ✅ Custom color palette (primary, secondary, success)
- ✅ Extended spacing & border radius
- ✅ Custom font family (Inter)
- ✅ Content paths untuk Laravel & Filament

### 2. **resources/css/app.css**

CSS utama dengan:

- ✅ Base styles (typography, body)
- ✅ Component classes (cards, buttons, badges, inputs)
- ✅ Utility classes (gradients, shadows)
- ✅ Animations & scrollbar styling

### 3. **resources/css/filament.css**

CSS khusus untuk Filament admin panel:

- ✅ Sidebar customization
- ✅ Widget enhancements
- ✅ Stats card styling

---

## 🎨 Custom Color Palette

```js
// Primary (Blue)
primary-50  → #f0f9ff (lightest)
primary-500 → #0ea5e9 (main)
primary-900 → #0c4a6e (darkest)

// Secondary (Purple)
secondary-50  → #fdf4ff
secondary-500 → #d946ef
secondary-900 → #701a75

// Success (Green)
success-50  → #f0fdf4
success-500 → #22c55e
success-900 → #14532d
```

---

## 🧩 Component Classes

### Cards

```html
<!-- Basic Card -->
<div class="card">
  <h3>Card Title</h3>
  <p>Card content</p>
</div>

<!-- Hoverable Card -->
<div class="card card-hover">Hover me!</div>
```

### Buttons

```html
<!-- Primary Button -->
<button class="btn btn-primary">Primary Action</button>

<!-- Secondary Button -->
<button class="btn btn-secondary">Secondary Action</button>

<!-- Outline Button -->
<button class="btn btn-outline">Outline Button</button>
```

### Badges

```html
<span class="badge badge-primary">New</span>
<span class="badge badge-success">Active</span>
<span class="badge badge-warning">Pending</span>
<span class="badge badge-danger">Error</span>
```

### Inputs

```html
<input type="text" class="input" placeholder="Enter text..." />
```

### Container

```html
<div class="container-custom">Your content here</div>
```

---

## 🎭 Utility Classes

### Text Gradient

```html
<h1 class="text-gradient">Beautiful Gradient Text</h1>
```

### Custom Shadow

```html
<div class="shadow-custom">Element with custom shadow</div>
```

### Fade In Animation

```html
<div class="animate-fade-in">This will fade in</div>
```

---

## 📦 Dependencies

Di `package.json`:

```json
{
  "devDependencies": {
    "@tailwindcss/forms": "^0.5.9", // Form styling
    "@tailwindcss/typography": "^0.5.15", // Typography plugin
    "@tailwindcss/vite": "^4.0.0", // Vite integration
    "tailwindcss": "^4.0.0", // Tailwind CSS v4
    "vite": "^7.0.7" // Build tool
  }
}
```

---

## 🚀 Cara Menggunakan

### 1. Install Dependencies

```bash
cd Ngajar-id
npm install
```

### 2. Build CSS (Development)

```bash
npm run dev
```

### 3. Build CSS (Production)

```bash
npm run build
```

### 4. Watch Mode (Auto-rebuild)

```bash
npm run dev
```

Ini akan watch perubahan file dan auto-compile CSS.

---

## 💡 Contoh Penggunaan di Blade

### Landing Page Example

```blade
{{-- resources/views/welcome.blade.php --}}
@php
    $viteAssets = true; // Enable if Vite is installed
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ngajar.id - Platform Belajar Mengajar</title>

    @if($viteAssets ?? false)
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <!-- Hero Section -->
    <div class="min-h-screen bg-gradient-to-br from-primary-50 to-secondary-50">
        <div class="container-custom py-20">
            <h1 class="text-5xl font-bold text-gradient mb-6 animate-fade-in">
                Selamat Datang di Ngajar.id
            </h1>
            <p class="text-xl text-gray-600 mb-8">
                Platform pembelajaran terbaik untuk Indonesia
            </p>
            <button class="btn btn-primary">
                Mulai Belajar
            </button>
        </div>
    </div>

    <!-- Feature Cards -->
    <div class="container-custom py-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card card-hover">
                <h3 class="text-xl font-bold mb-3">Kelas Berkualitas</h3>
                <p class="text-gray-600">Akses ribuan kelas dari pengajar terbaik</p>
                <span class="badge badge-primary mt-4">Popular</span>
            </div>

            <div class="card card-hover">
                <h3 class="text-xl font-bold mb-3">Harga Terjangkau</h3>
                <p class="text-gray-600">Sistem token yang fleksibel</p>
                <span class="badge badge-success mt-4">New</span>
            </div>

            <div class="card card-hover">
                <h3 class="text-xl font-bold mb-3">Belajar Kapan Saja</h3>
                <p class="text-gray-600">Akses 24/7 dari mana saja</p>
                <span class="badge badge-warning mt-4">Trending</span>
            </div>
        </div>
    </div>
</body>
</html>
```

### Component Example

```blade
{{-- resources/views/components/kelas-card.blade.php --}}
<div class="card card-hover">
    <div class="flex items-start justify-between mb-4">
        <h3 class="text-lg font-bold">{{ $kelas->nama_kelas }}</h3>
        @if($kelas->is_premium)
            <span class="badge badge-primary">Premium</span>
        @else
            <span class="badge badge-success">Gratis</span>
        @endif
    </div>

    <p class="text-gray-600 mb-4">{{ $kelas->deskripsi }}</p>

    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-500">
            {{ $kelas->peserta_count }} peserta
        </span>
        <button class="btn btn-primary">
            Lihat Detail
        </button>
    </div>
</div>
```

---

## 🎯 Integrasi dengan Filament

Filament otomatis menggunakan Tailwind CSS. Untuk customize:

### 1. Publish Filament Config

```bash
php artisan vendor:publish --tag=filament-config
```

### 2. Edit `config/filament.php`

```php
return [
    'dark_mode' => true, // Enable dark mode
    'brand' => 'Ngajar.id',

    // Custom colors
    'colors' => [
        'primary' => '#0ea5e9',
        'success' => '#22c55e',
        'warning' => '#f59e0b',
        'danger' => '#ef4444',
    ],
];
```

### 3. Custom Filament Theme

Buat file `resources/css/filament.css` (sudah dibuat ✅):

```css
@import "tailwindcss";

@layer base {
  .fi-sidebar-nav {
    @apply bg-gradient-to-b from-primary-950 to-primary-900;
  }
}
```

---

## 📱 Responsive Design

Gunakan Tailwind responsive utilities:

```html
<!-- Mobile first approach -->
<div class="w-full md:w-1/2 lg:w-1/3">
  <!-- Full width on mobile -->
  <!-- Half width on tablet (md) -->
  <!-- One-third on desktop (lg) -->
</div>

<!-- Grid responsive -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
  <!-- 1 column on mobile -->
  <!-- 2 columns on tablet -->
  <!-- 3 columns on desktop -->
</div>
```

---

## 🔧 Troubleshooting

### Styles Tidak Muncul?

```bash
# Clear cache dan rebuild
npm run build
php artisan optimize:clear
```

### Hot Reload Tidak Jalan?

```bash
# Restart Vite dev server
# Press Ctrl+C to stop
npm run dev
```

### Class Tidak Terdeteksi?

Pastikan path file sudah ada di `tailwind.config.js`:

```js
content: [
  "./resources/views/**/*.blade.php", // ✅ Include all Blade files
  "./app/Filament/**/*.php", // ✅ Include Filament
];
```

---

## 📚 Resources

- **Tailwind CSS**: https://tailwindcss.com/docs
- **Tailwind v4**: https://tailwindcss.com/blog/tailwindcss-v4
- **Filament**: https://filamentphp.com/docs/3.x/panels/themes
- **Laravel Vite**: https://laravel.com/docs/11.x/vite

---

## ✨ Tips & Best Practices

1. **Gunakan Component Classes**

   - Lebih maintainable daripada inline utilities
   - Konsisten di seluruh aplikasi

2. **Mobile First**

   - Design untuk mobile terlebih dahulu
   - Tambahkan breakpoints untuk tablet/desktop

3. **Custom Colors**

   - Gunakan color palette yang sudah didefinisikan
   - primary, secondary, success untuk consistency

4. **Performance**

   - Build production dengan `npm run build`
   - CSS akan di-minify otomatis

5. **Dark Mode** (Optional)
   ```html
   <div class="bg-white dark:bg-gray-800">Auto dark mode!</div>
   ```

---

**Status**: ✅ Tailwind CSS Ready!  
**Version**: Tailwind CSS v4  
**Last Updated**: 2026-01-11
