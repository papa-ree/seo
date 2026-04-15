# Bale SEO

[![Latest Version on Packagist](https://img.shields.io/packagist/v/papa-ree/seo.svg?style=flat-square)](https://packagist.org/packages/papa-ree/seo)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/papa-ree/seo/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/papa-ree/seo/actions?query=workflow%3Arun-tests+branch%3Amain)
[![License](https://img.shields.io/github/license/papa-ree/seo?style=flat-square)](LICENSE.md)

Bale SEO adalah package Laravel untuk mengelola metadata SEO, Open Graph, Twitter Cards, dan sitemap secara dinamis. Package ini dirancang untuk bekerja dengan ekosistem Bale (Emperan & CMS) namun tetap fleksibel untuk aplikasi Laravel lainnya.

## Fitur Utama

- **Polymorphic Meta**: Hubungkan metadata SEO ke model apa pun (Post, Page, dsb).
- **Automatic Fallbacks**: Secara otomatis menghasilkan deskripsi dari konten jika tidak diatur manual.
- **Dynamic Sitemap**: Mendukung sitemap index dan sub-sitemap untuk Post dan Page.
- **Dynamic Robots.txt**: Menghasilkan file robots.txt yang dapat dikonfigurasi.
- **Structured Data**: Mendukung JSON-LD untuk SEO yang lebih baik.
- **Blade Component**: Komponen siap pakai untuk integrasi head HTML yang mudah.

## Instalasi

Anda dapat menginstal package ini via composer:

```bash
composer require papa-ree/seo
```

Setelah instalasi, jalankan perintah instalasi untuk mempublish migrasi dan melakukan konfigurasi awal:

```bash
php artisan seo:install
```

Perintah ini akan:
1. Mempublish file migrasi.
2. Menanyakan apakah Anda ingin mengaktifkan route SEO (sitemap.xml dan robots.txt).
3. Memperbarui file `.env` Anda dengan `SEO_USE_ROUTES`.

Jangan lupa jalankan migrasi:

```bash
php artisan migrate
```

## Konfigurasi

Anda dapat mempublish file konfigurasi dengan:

```bash
php artisan vendor:publish --tag="seo-config"
```

Isi dari file `config/seo.php`:

```php
return [
    'site_name' => env('SEO_SITE_NAME', config('app.name', 'Bale')),

    'use_routes' => env('SEO_USE_ROUTES', false),

    'defaults' => [
        'title' => env('SEO_DEFAULT_TITLE', config('app.name', 'Bale')),
        'description' => env('SEO_DEFAULT_DESCRIPTION', ''),
        'image' => env('SEO_DEFAULT_IMAGE', '/img/og-image.jpg'),
        'keywords' => env('SEO_DEFAULT_KEYWORDS', ''),
    ],

    'sitemap' => [
        'models' => [
            'post' => \Bale\Emperan\Models\Post::class,
            'page' => \Bale\Emperan\Models\Page::class,
        ],
    ],
];
```

## Penggunaan

### 1. Menyiapkan Model

Tambahkan trait `HasSeoMeta` ke model yang ingin Anda beri dukungan SEO:

```php
namespace App\Models;

use Bale\Seo\Traits\HasSeoMeta;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasSeoMeta;
    
    // Opsional: tentukan sumber fallback untuk deskripsi
    public function getExcerpt($limit = 160)
    {
        return Str::limit(strip_tags($this->content), $limit);
    }
}
```

### 2. Integrasi ke Layout

Gunakan komponen `<x-seo::seo-meta />` di dalam tag `<head>` layout Anda:

```html
<head>
    <!-- Meta tags lainnya -->
    
    <x-seo::seo-meta :model="$post" />
    
    <!-- Atau dengan default values -->
    <x-seo::seo-meta :defaults="['title' => 'Custom Title']" />
</head>
```

### 3. Mengelola Data SEO

Anda dapat membuat atau memperbarui data SEO untuk sebuah model dengan mudah:

```php
$post->updateSeoMeta([
    'title' => 'Judul SEO Keren',
    'description' => 'Deskripsi yang menarik untuk Google.',
    'og_title' => 'Judul untuk Facebook',
    'no_index' => true, // Opsional
]);
```

## Sitemap & Robots.txt

Jika `use_routes` diaktifkan di konfigurasi, package akan menyediakan route berikut:

- `/sitemap.xml`: Index sitemap.
- `/sitemap-posts.xml`: Sitemap khusus post.
- `/sitemap-pages.xml`: Sitemap khusus page.
- `/robots.txt`: File robots.txt dinamis.

Anda dapat menentukan model mana yang masuk ke sitemap melalui `config/seo.php`.

## Testing

```bash
composer test
```

## Changelog

Silakan lihat [CHANGELOG](CHANGELOG.md) untuk informasi terbaru mengenai perubahan.

## Lisensi

Lisensi MIT. Silakan lihat [File Lisensi](LICENSE.md) untuk informasi lebih lanjut.
