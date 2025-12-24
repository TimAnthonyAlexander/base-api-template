<?php

namespace App\Services;

use BaseApi\Config;

/**
 * TranslationService - Handle internationalization and translation retrieval.
 */
class TranslationService
{
    /** @var array<string, array<string, string>> */
    private array $cache = [];

    public function __construct(
        private readonly Config $config
    ) {}

    /**
     * Get a specific translation key with parameter replacement.
     * Automatically loads the corresponding translation file based on the key prefix.
     * e.g., 'email.welcome' will try to load 'email.json'.
     *
     * @param string $key The translation key (e.g., 'email.welcome')
     * @param array<string, string> $params Parameters to replace in the string
     * @param string $lang The language code (default: 'en')
     * @return string The translated string or the key if not found
     */
    public function translate(string $key, array $params = [], string $lang = 'en'): string
    {
        // 1. Identify namespace from key (first segment)
        $parts = explode('.', $key);
        $namespace = $parts[0] ?? 'common';

        // 2. Ensure loaded
        $this->loadNamespace($lang, $namespace);

        // 3. Look up
        $text = $this->cache[$lang][$namespace][$key] ?? null;

        // 4. Fallback to English
        if ($text === null && $lang !== 'en') {
            $this->loadNamespace('en', $namespace);
            $text = $this->cache['en'][$namespace][$key] ?? null;
        }

        $text = $text ?? $key;

        // 5. Replace params
        return $this->replaceParams($text, $params);
    }

    /**
     * Get all translations for a language, optionally filtering by admin access.
     * Used used primarily by the frontend to load all strings.
     *
     * @param string $lang
     * @param bool $includeAdmin
     * @return array<string, string> Merged translations
     */
    public function getAll(string $lang, bool $includeAdmin = false): array
    {
        $translationsPath = $this->getTranslationsPath($lang);

        if (!is_dir($translationsPath)) {
            return [];
        }

        $merged = [];
        $files = glob($translationsPath . '/*.json');

        if ($files === false) {
            return [];
        }

        foreach ($files as $file) {
            $namespace = basename($file, '.json');

            // Skip admin namespace if not requested
            if ($namespace === 'admin' && !$includeAdmin) {
                continue;
            }

            $content = file_get_contents($file);
            if ($content === false) {
                continue;
            }

            $translations = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($translations)) {
                $merged = array_merge($merged, $translations);

                // Populate cache while we are at it
                $this->cache[$lang][$namespace] = $translations;
            }
        }

        return $merged;
    }

    /**
     * Validate if a language is supported.
     */
    public function isSupported(string $lang): bool
    {
        $supported = $this->config->get('i18n.locales') ?? ['en'];
        return in_array($lang, $supported, true);
    }

    private function loadNamespace(string $lang, string $namespace): void
    {
        if (isset($this->cache[$lang][$namespace])) {
            return;
        }

        $path = $this->getTranslationsPath($lang) . '/' . $namespace . '.json';

        if (file_exists($path)) {
            $content = file_get_contents($path);
            $data = json_decode($content ?: '{}', true);
            $this->cache[$lang][$namespace] = is_array($data) ? $data : [];
        } else {
            $this->cache[$lang][$namespace] = [];
        }
    }

    private function replaceParams(string $text, array $params): string
    {
        foreach ($params as $k => $v) {
            $text = str_replace('{' . $k . '}', (string)$v, $text);
            // Support :param style as well used in some Laravel-style translations
            $text = str_replace(':' . $k, (string)$v, $text);
        }
        return $text;
    }

    private function getTranslationsPath(string $lang): string
    {
        return __DIR__ . '/../../translations/' . $lang;
    }
}
