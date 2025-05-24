<?php

namespace App\Http\Controllers;

use App\Models\ReadLater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * "Lasīt vēlāk" saraksta kontrolieris
 * 
 * Šis kontrolieris apstrādā grāmatu pievienošanu un noņemšanu no lietotāja 
 * "Lasīt vēlāk" saraksta, izmantojot tradicionālo Laravel pieeju.
 */
class ReadLaterController extends Controller
{
  /**
   * Pievieno grāmatu lietotāja "Lasīt vēlāk" sarakstam
   *
   * @param int
   * @return \Illuminate\Http\RedirectResponse
   */
  public function add($id)
  {
    $user = Auth::user();

    $readLater = new ReadLater();
    $readLater->user_id = $user->id;
    $readLater->product_id = $id;
    $readLater->save();

    return redirect()->back()->with('success', 'Book added to read later list!');
  }

  /**
   * Noņem grāmatu no lietotāja "Lasīt vēlāk" saraksta
   *
   * @param int
   * @return \Illuminate\Http\RedirectResponse
   */
  public function delete($id)
  {
    $readLater = ReadLater::where('user_id', Auth::user()->id)
      ->where('product_id', $id)
      ->firstOrFail();

    $readLater->delete();

    return redirect()->back()->with('success', 'Book removed from read later list!')
      ->withInput(['tab' => request('current_tab', 'readlater')]);
  }

  /**
   * Iegūst visas lietotāja "Lasīt vēlāk" sarakstā esošās grāmatas
   *
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function getReadLater()
  {
    $user = Auth::user();
    $readLater = $user->readLater()->with('product')->get();

    return $readLater;
  }
}