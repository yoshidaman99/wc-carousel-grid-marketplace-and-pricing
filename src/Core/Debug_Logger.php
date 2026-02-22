<?php

namespace WC_CGMP\Core;

defined('ABSPATH') || exit;

class Debug_Logger
{
    private static ?Debug_Logger $instance = null;
    private bool $enabled;
    private array $timers = [];

    public static function get_instance(): Debug_Logger
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->enabled = defined('WP_DEBUG') && WP_DEBUG;
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    public function exception(\Throwable $e, array $context = []): void
    {
        $this->log('EXCEPTION', $e->getMessage(), array_merge([
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ], $context));
    }

    public function db_error(string $operation, string $error, array $context = []): void
    {
        $this->log('DB_ERROR', "{$operation} failed: {$error}", $context);
    }

    public function trace(string $message, int $depth = 3): void
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $depth);
        $this->log('TRACE', $message, ['backtrace' => $backtrace]);
    }

    public function start_timer(string $key): void
    {
        $this->timers[$key] = microtime(true);
    }

    public function stop_timer(string $key, string $message = ''): float
    {
        if (!isset($this->timers[$key])) {
            return 0.0;
        }

        $elapsed = microtime(true) - $this->timers[$key];
        unset($this->timers[$key]);

        if ($message) {
            $this->log('TIMER', "{$message}: " . number_format($elapsed * 1000, 2) . 'ms');
        }

        return $elapsed;
    }

    public function is_enabled(): bool
    {
        return $this->enabled;
    }

    private function log(string $level, string $message, array $context = []): void
    {
        if (!$this->enabled) {
            return;
        }

        $context_str = !empty($context) ? ' | ' . wp_json_encode($context) : '';
        $log_message = "[{$level}] {$message}{$context_str}";

        if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            error_log('[WC_CGMP] ' . $log_message);
        }
    }
}
