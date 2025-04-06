<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class GenerateBookThumbnails extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'books:generate-thumbnails {--force : Force regenerate all thumbnails}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generate thumbnails for all books in the library';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $books = Product::all();
    $count = $books->count();
    $this->info("Generating thumbnails for {$count} books...");

    $forceRegenerate = $this->option('force');
    if ($forceRegenerate) {
      $this->info("Force regeneration enabled. All thumbnails will be recreated.");
    }

    $bar = $this->output->createProgressBar($count);
    $bar->start();

    $successCount = 0;
    $failCount = 0;

    foreach ($books as $book) {
      if ($this->generateThumbnail($book, $forceRegenerate)) {
        $successCount++;
      } else {
        $failCount++;
        $this->error("Failed to generate thumbnail for: {$book->title} ({$book->file})");
      }
      $bar->advance();
    }

    $bar->finish();
    $this->newLine(2);
    $this->info("Thumbnail generation complete. Success: {$successCount}, Failed: {$failCount}");

    return 0;
  }

  /**
   * Generate a thumbnail for a book using Ghostscript
   *
   * @param \App\Models\Product $book
   * @param bool $forceRegenerate
   * @return bool
   */
  private function generateThumbnail($book, $forceRegenerate = false)
  {
    $filename = $book->file;
    $thumbnailDir = public_path('book-thumbnails');
    $thumbnailFilename = str_replace('.pdf', '.jpg', $filename);
    $thumbnailPath = $thumbnailDir . '/' . $thumbnailFilename;
    $pdfPath = storage_path('app/books/' . $filename);

    // Create thumbnails directory if it doesn't exist
    if (!file_exists($thumbnailDir)) {
      mkdir($thumbnailDir, 0755, true);
    }

    // Check if thumbnail exists and if we should regenerate
    if (file_exists($thumbnailPath) && !$forceRegenerate) {
      return true;
    } elseif (file_exists($thumbnailPath)) {
      unlink($thumbnailPath);
    }

    // Check if PDF file exists
    if (!file_exists($pdfPath)) {
      return false;
    }

    // Generate thumbnail using Ghostscript
    $gsExecutable = 'C:\\Program Files\\gs\\gs10.05.0\\bin\\gswin64c.exe';

    if (file_exists($gsExecutable)) {
      $command = '"' . $gsExecutable . '" -sDEVICE=jpeg -dNOPAUSE -dBATCH -dSAFER '
        . '-dFirstPage=1 -dLastPage=1 -r150 '
        . '-dTextAlphaBits=4 -dGraphicsAlphaBits=4 '
        . '-o "' . $thumbnailPath . '" '
        . '"' . $pdfPath . '"';

      exec($command, $output, $return_var);

      if ($return_var === 0 && file_exists($thumbnailPath)) {
        // Optimize the thumbnail size if needed
        $this->resizeThumbnail($thumbnailPath);
        return true;
      }
    }

    return false;
  }

  /**
   * Resize a thumbnail image to standard dimensions
   *
   * @param string $thumbnailPath
   * @return bool
   */
  private function resizeThumbnail($thumbnailPath)
  {
    if (!extension_loaded('imagick')) {
      return false;
    }

    $maxWidth = 400;
    $maxHeight = 566;

    $imagick = new \Imagick($thumbnailPath);
    $imagick->setImageFormat('jpg');
    $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
    $imagick->setImageCompressionQuality(85);

    $width = $imagick->getImageWidth();
    $height = $imagick->getImageHeight();

    if ($width > $maxWidth || $height > $maxHeight) {
      // Resize while maintaining aspect ratio
      $ratioWidth = $maxWidth / $width;
      $ratioHeight = $maxHeight / $height;
      $ratio = min($ratioWidth, $ratioHeight);

      $newWidth = $width * $ratio;
      $newHeight = $height * $ratio;

      $imagick->resizeImage($newWidth, $newHeight, \Imagick::FILTER_LANCZOS, 1);
    }

    $imagick->writeImage($thumbnailPath);
    $imagick->clear();
    $imagick->destroy();

    return true;
  }
}