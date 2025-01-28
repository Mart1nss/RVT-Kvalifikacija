<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
  public function index()
  {
    $logs = AuditLog::with('admin')
      ->orderBy('created_at', 'desc')
      ->paginate(15);

    $admins = User::where('usertype', 'admin')->get();

    return view('audit-logs', compact('logs', 'admins'));
  }
}