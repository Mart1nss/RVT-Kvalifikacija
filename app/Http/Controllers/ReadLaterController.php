<?php

namespace App\Http\Controllers;

use App\Models\ReadLater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReadLaterController extends Controller
{
  public function add($id)
  {
    $user = Auth::user();

    $readLater = new ReadLater();
    $readLater->user_id = $user->id;
    $readLater->product_id = $id;
    $readLater->save();

    return redirect()->back()->with('success', 'Book added to read later list!');
  }

  public function delete($id)
  {
    $readLater = ReadLater::where('user_id', Auth::user()->id)
      ->where('product_id', $id)
      ->firstOrFail();

    $readLater->delete();

    return redirect()->back()->with('success', 'Book removed from read later list!')
      ->withInput(['tab' => request('current_tab', 'readlater')]);
  }

  public function getReadLater()
  {
    $user = Auth::user();
    $readLater = $user->readLater()->with('product')->get();

    return $readLater;
  }
}