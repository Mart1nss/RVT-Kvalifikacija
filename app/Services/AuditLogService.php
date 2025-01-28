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
   * @param string $action The action performed (e.g. "Updated book", "Deleted user")
   * @param string $actionType The type of action (e.g. "book", "user", "category")
   * @param string $description Detailed description of changes
   * @param string|int|null $itemId ID of the affected item
   * @param string|null $itemName Name of the affected item
   * @return AuditLog|null
   */
  public static function log($action, $actionType, $description, $itemId = null, $itemName = null)
  {
    try {
      // Validate admin is logged in
      if (!Auth::check() || Auth::user()->usertype !== 'admin') {
        Log::warning('Attempted to create audit log without admin privileges', [
          'user_id' => Auth::id(),
          'action' => $action
        ]);
        return null;
      }

      // Validate action type
      $validActionTypes = ['book', 'user', 'category', 'notification'];
      if (!in_array($actionType, $validActionTypes)) {
        Log::error('Invalid action type in audit log', [
          'action_type' => $actionType,
          'valid_types' => $validActionTypes
        ]);
        return null;
      }

      return AuditLog::create([
        'admin_id' => Auth::id(),
        'action' => $action,
        'action_type' => $actionType,
        'description' => $description,
        'affected_item_id' => $itemId,
        'affected_item_name' => $itemName
      ]);

    } catch (\Exception $e) {
      Log::error('Failed to create audit log', [
        'error' => $e->getMessage(),
        'action' => $action,
        'action_type' => $actionType
      ]);
      return null;
    }
  }
}