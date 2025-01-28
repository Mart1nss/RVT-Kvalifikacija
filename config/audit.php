<?php

return [
  /*
  |--------------------------------------------------------------------------
  | Audit Log Settings
  |--------------------------------------------------------------------------
  |
  | This file contains the configuration for the audit log system.
  |
  */

  // Number of days to keep audit logs before they are automatically deleted
  'retention_days' => env('AUDIT_LOG_RETENTION_DAYS', 7),

  // Valid action types for audit logs
  'action_types' => [
    'book',
    'user',
    'category',
    'notification'
  ],

  // Whether to log failed audit log attempts
  'log_failed_attempts' => env('AUDIT_LOG_FAILED_ATTEMPTS', true),

  // Path for the scheduler log file
  'scheduler_log_path' => storage_path('logs/scheduler.log'),
];