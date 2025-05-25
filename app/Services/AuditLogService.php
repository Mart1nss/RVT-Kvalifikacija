<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditLogService
{
  /**
   * Create an audit log entry
   *
   * @param string The action performed (e.g. "Updated book", "Deleted user")
   * @param string The type of action (e.g. "book", "user", "category")
   * @param string Detailed description of changes
   * @param string|int|null ID of the affected item
   * @param string|null Name of the affected item
   * @return AuditLog|null
   */
  public static function log($action, $actionType, $description, $itemId = null, $itemName = null)
  {
    try {
      // Check if user is logged in and is an admin
      if (!Auth::check() || Auth::user()->usertype !== 'admin') {
        Log::warning('Attempted to create audit log without admin privileges');
        return null;
      }

      // Create the audit log
      return AuditLog::create([
        'admin_id' => Auth::id(),
        'action' => $action,
        'action_type' => $actionType,
        'description' => $description,
        'affected_item_id' => $itemId,
        'affected_item_name' => $itemName
      ]);

    } catch (\Exception $e) {
      Log::error('Failed to create audit log: ' . $e->getMessage());
      return null;
    }
  }
}