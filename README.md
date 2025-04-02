projekts

Kā palaist programmu

`composer install`

`npm install`

`npm run dev`

`database.sql` failu ieliec datubaze

`DB_DATABASE=laravel` uz `DB_DATABASE=database`

`php artisan key:generate`

`php artisan migrate`

`php artisan serve`

Izmantotās tehnoloģijas

PDF.js - pdf failu modificēšana;
Laravel (PHP) + Breeze + livewire;
MySQL (laragon);
HMTL, CSS, JS (alpine.js);
Visual Studio Code;

## PDF Thumbnail Generation

The application includes a robust PDF thumbnail generation system for book previews. Key features:

-   Extracts actual first pages from PDF files using Ghostscript
-   Multiple fallback methods ensure thumbnails are always generated
-   Optimized for performance and visual appeal

For detailed documentation on how the thumbnail system works, see [README-thumbnails.md](README-thumbnails.md).
