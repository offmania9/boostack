<?php

namespace My\Controllers\Exceptions;

use Boostack\Exceptions\Exception_Misconfiguration;
use Boostack\Models\Config;
use Boostack\Models\Log\Log_Driver;
use Boostack\Models\Log\Log_Level;
use Boostack\Models\Log\Logger;
use Boostack\Models\Request;

class My_Exception
{
    public static function handle(\Throwable $throwable): void
    {
        $message = trim((string) $throwable->getMessage());
        if ($message === '') {
            $message = 'Exception: ' . get_class($throwable);
        }

        if (self::isExpectedUnauthenticatedError($message) || self::isExpectedPermissionError($message)) {
            return;
        }

        $level = ($throwable instanceof Exception_Misconfiguration)
            ? Log_Level::WARNING
            : Log_Level::ERROR;
        $actionTag = ($level === Log_Level::WARNING) ? 'warning' : 'error';

        $payload = [
            'message' => $message,
            'context' => [
                'action_tag' => $actionTag,
                'exception' => self::serializeThrowable($throwable),
            ],
        ];

        $requestSnapshot = self::buildRequestSnapshot();
        if ($requestSnapshot !== []) {
            $payload['context']['request'] = $requestSnapshot;
        }

        Logger::write($payload, $level, Log_Driver::BOTH);

        if (defined('CURRENT_ENVIRONMENT') && CURRENT_ENVIRONMENT === "staging") {
            print_r($message);
        }

        if (Config::get("developmentMode")) {
            d($message);
            d($throwable);
        }
    }

    private static function isExpectedUnauthenticatedError(string $message): bool
    {
        $normalized = strtolower(trim($message));
        return str_contains($normalized, 'current user must to be logged in');
    }

    private static function isExpectedPermissionError(string $message): bool
    {
        $normalized = strtolower(trim($message));
        return str_contains($normalized, 'permessi insufficienti');
    }

    /**
     * @return array<string, mixed>
     */
    private static function serializeThrowable(\Throwable $throwable): array
    {
        $chain = [];
        $current = $throwable;
        $depth = 0;

        while ($current !== null && $depth < 8) {
            $chain[] = [
                'class' => get_class($current),
                'message' => (string) $current->getMessage(),
                'code' => (int) $current->getCode(),
                'file' => (string) $current->getFile(),
                'line' => (int) $current->getLine(),
            ];

            $depth++;
            $current = $current->getPrevious();
        }

        $trace = '';
        try {
            $trace = (string) $throwable->getTraceAsString();
        } catch (\Throwable) {
            $trace = '';
        }

        return [
            'primary' => $chain[0] ?? [
                'class' => get_class($throwable),
                'message' => (string) $throwable->getMessage(),
                'code' => (int) $throwable->getCode(),
                'file' => (string) $throwable->getFile(),
                'line' => (int) $throwable->getLine(),
            ],
            'chain' => $chain,
            'trace' => $trace,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function buildRequestSnapshot(): array
    {
        $snapshot = [];

        try {
            $context = self::buildRequestContext(['action_tag' => 'error']);
            if (is_array($context) && $context !== []) {
                $snapshot['context'] = self::compactData($context);
            }
        } catch (\Throwable) {
            // noop
        }

        try {
            $query = Request::getQueryArray();
            if (is_array($query) && $query !== []) {
                $snapshot['query'] = self::compactData(self::maskSensitiveData($query));
            }
        } catch (\Throwable) {
            // noop
        }

        try {
            $post = Request::getPostArray();
            if (is_array($post) && $post !== []) {
                $snapshot['post'] = self::compactData(self::maskSensitiveData($post));
            }
        } catch (\Throwable) {
            // noop
        }

        try {
            $files = Request::getFilesArray();
            if (is_array($files) && $files !== []) {
                $snapshot['files'] = self::compactData(self::maskSensitiveData($files));
            }
        } catch (\Throwable) {
            // noop
        }

        return $snapshot;
    }

    /**
     * @param array<string, mixed> $extraContext
     * @return array<string, mixed>
     */
    private static function buildRequestContext(array $extraContext = []): array
    {
        $context = $extraContext;

        try {
            $context['method'] = Request::getMethod()->value;
        } catch (\Throwable) {
            // noop
        }

        try {
            $server = Request::getServerArray();
            if (is_array($server)) {
                $server = self::maskSensitiveData($server);
                foreach (['REQUEST_URI', 'HTTP_HOST', 'REMOTE_ADDR', 'HTTPS', 'REQUEST_SCHEME'] as $key) {
                    if (isset($server[$key]) && $server[$key] !== '') {
                        $context[strtolower($key)] = $server[$key];
                    }
                }
            }
        } catch (\Throwable) {
            // noop
        }

        try {
            $headers = Request::getHeaderArray();
            if (is_array($headers) && $headers !== []) {
                $context['headers'] = self::pickRequestHeaders(self::maskSensitiveData($headers));
            }
        } catch (\Throwable) {
            // noop
        }

        try {
            $userAgent = Request::getUserAgent();
            if ($userAgent !== null && $userAgent !== '') {
                $context['user_agent'] = $userAgent;
            }
        } catch (\Throwable) {
            // noop
        }

        return $context;
    }

    /**
     * @param array<string, mixed> $headers
     * @return array<string, mixed>
     */
    private static function pickRequestHeaders(array $headers): array
    {
        $selected = [];
        foreach (['host', 'origin', 'referer', 'content-type', 'x-forwarded-for', 'x-requested-with'] as $key) {
            if (isset($headers[$key]) && $headers[$key] !== '') {
                $selected[$key] = $headers[$key];
            }
        }

        return $selected;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private static function maskSensitiveData($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        $masked = [];
        foreach ($value as $key => $item) {
            if (self::isSensitiveKey((string) $key)) {
                $masked[$key] = '[redacted]';
                continue;
            }

            $masked[$key] = is_array($item)
                ? self::maskSensitiveData($item)
                : $item;
        }

        return $masked;
    }

    private static function isSensitiveKey(string $key): bool
    {
        $normalized = strtolower($key);
        foreach ([
            'password',
            'passwd',
            'pwd',
            'token',
            'authorization',
            'cookie',
            'secret',
            'api_key',
            'apikey',
            'csrf',
            'jwt',
        ] as $needle) {
            if (str_contains($normalized, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private static function compactData($value, int $depth = 0)
    {
        $maxDepth = 4;
        $maxItems = 80;
        $maxStringLength = 2000;

        if ($depth >= $maxDepth) {
            return '[truncated-depth]';
        }

        if (is_array($value)) {
            $output = [];
            $count = 0;
            foreach ($value as $key => $item) {
                if ($count >= $maxItems) {
                    $output['__truncated_items'] = count($value) - $maxItems;
                    break;
                }

                $output[$key] = self::compactData($item, $depth + 1);
                $count++;
            }

            return $output;
        }

        if (is_string($value) && strlen($value) > $maxStringLength) {
            return substr($value, 0, $maxStringLength) . '...';
        }

        if (is_object($value)) {
            return '[object]';
        }

        return $value;
    }
}
