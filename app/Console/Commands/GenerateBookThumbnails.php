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
   * Generate a thumbnail for a book
   *
   * @param \App\Models\Product $book
   * @param bool $forceRegenerate
   * @return bool
   */
  private function generateThumbnail($book, $forceRegenerate = false)
  {
    try {
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
        return $this->generateEnhancedThumbnail($book, $thumbnailPath);
      }

      // Try to generate thumbnail using Ghostscript
      $gsExecutable = 'C:\\Program Files\\gs\\gs10.05.0\\bin\\gswin64c.exe';

      if (file_exists($gsExecutable)) {
        $command = '"' . $gsExecutable . '" -sDEVICE=jpeg -dNOPAUSE -dBATCH -dSAFER '
          . '-dFirstPage=1 -dLastPage=1 -r150 '
          . '-dTextAlphaBits=4 -dGraphicsAlphaBits=4 '
          . '-o "' . $thumbnailPath . '" '
          . '"' . $pdfPath . '"';

        exec($command, $output, $return_var);

        if ($return_var === 0 && file_exists($thumbnailPath)) {
          // Optimize the thumbnail size if needed (using Imagick)
          if (extension_loaded('imagick')) {
            $this->resizeThumbnail($thumbnailPath);
          }
          return true; // Ghostscript succeeded
        }
        // If Ghostscript failed ($return_var !== 0), fall through to placeholder
      }

      // Fallback: If Ghostscript is not found or failed, use enhanced placeholder
      return $this->generateEnhancedThumbnail($book, $thumbnailPath);
    } catch (\Exception $e) {
      Log::error('Failed to generate thumbnail for book ' . $book->title . ': ' . $e->getMessage());
      // Attempt placeholder generation even on general error
      try {
        return $this->generateEnhancedThumbnail($book, $thumbnailPath);
      } catch (\Exception $placeholderEx) {
        Log::error('Failed to generate placeholder thumbnail after main error for book ' . $book->title . ': ' . $placeholderEx->getMessage());
        return false; // Both generation and placeholder failed
      }
    }
  }

  /**
   * Generate an enhanced thumbnail with book title and author
   * 
   * @param \App\Models\Product $book
   * @param string $thumbnailPath
   * @return bool
   */
  private function generateEnhancedThumbnail($book, $thumbnailPath)
  {
    try {
      // Create image with standard book dimensions
      $width = 400;
      $height = 566;
      $img = imagecreatetruecolor($width, $height);

      // Define colors
      $bgColor = imagecolorallocate($img, 35, 45, 75);      // Deep blue
      $accentColor = imagecolorallocate($img, 80, 120, 180); // Bright blue
      $textColor = imagecolorallocate($img, 240, 240, 240);  // White
      $overlayColor = imagecolorallocate($img, 25, 35, 65);  // Dark overlay
      $highlightColor = imagecolorallocate($img, 255, 140, 0); // Orange

      // Fill background
      imagefill($img, 0, 0, $bgColor);

      // Create gradient-like background
      for ($i = 0; $i < $height; $i += 30) {
        $shade = min(180, 80 + $i / 4);
        $patternColor = imagecolorallocate($img, 30, 40, $shade);
        imagefilledrectangle($img, 0, $i, $width, $i + 15, $patternColor);
      }

      // Create central text area
      $centerX1 = $width / 4;
      $centerY1 = $height / 3 - 30;
      $centerX2 = $width * 3 / 4;
      $centerY2 = $height * 2 / 3 + 30;

      imagefilledrectangle($img, $centerX1, $centerY1, $centerX2, $centerY2, $overlayColor);
      imagerectangle($img, $centerX1, $centerY1, $centerX2, $centerY2, $accentColor);
      imagerectangle($img, $centerX1 + 3, $centerY1 + 3, $centerX2 - 3, $centerY2 - 3, $highlightColor);

      // Draw PDF badge
      $badgeSize = 60;
      $badgeY = $height / 3 - 15;
      imagefilledellipse($img, $width / 2, $badgeY, $badgeSize, $badgeSize, $highlightColor);

      // Add PDF text to badge
      $pdfText = 'PDF';
      $pdfFont = 5;
      $pdfTextWidth = imagefontwidth($pdfFont) * strlen($pdfText);
      $pdfX = ($width - $pdfTextWidth) / 2;
      $pdfY = $badgeY - 10;
      imagestring($img, $pdfFont, $pdfX, $pdfY, $pdfText, $textColor);

      // Add book title
      $titleFont = 4;
      $bookTitle = $book->title;
      if (strlen($bookTitle) > 25) {
        $bookTitle = substr($bookTitle, 0, 22) . '...';
      }

      $titleWidth = imagefontwidth($titleFont) * strlen($bookTitle);
      $titleX = ($width - $titleWidth) / 2;
      $titleY = $height / 2 - 10;

      // Title with shadow
      imagestring($img, $titleFont, $titleX + 1, $titleY + 1, $bookTitle, imagecolorallocate($img, 20, 20, 20));
      imagestring($img, $titleFont, $titleX, $titleY, $bookTitle, $highlightColor);

      // Add author if available
      if ($book->author) {
        $authorFont = 2;
        $author = $book->author;

        if (strlen($author) > 30) {
          $author = substr($author, 0, 27) . '...';
        }

        $authorWidth = imagefontwidth($authorFont) * strlen($author);
        $authorX = ($width - $authorWidth) / 2;
        $authorY = $titleY + 30;

        imagestring($img, $authorFont, $authorX, $authorY, $author, $textColor);
      }

      // Save the image
      imagejpeg($img, $thumbnailPath, 90);
      imagedestroy($img);

      return true;
    } catch (\Exception $e) {
      Log::error('Enhanced thumbnail generation failed: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Resize a thumbnail image to standard dimensions using Imagick
   *
   * @param string $thumbnailPath
   * @return bool
   */
  private function resizeThumbnail($thumbnailPath)
  {
    if (!extension_loaded('imagick')) {
      return false;
    }

    try {
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
    } catch (\Exception $e) {
      Log::error('Failed to resize thumbnail: ' . $e->getMessage());
      return false;
    }
  }
}