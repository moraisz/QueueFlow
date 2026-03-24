<?php

namespace Src\Core;

class View
{
    private static string $viewsPath = __DIR__ . '/../Infrastructure/Views';
    private static ?string $layout = null;
    private static array $sections = [];
    private static ?string $currentSection = null;
    private static array $data = [];

    /**
     * @param array<int,mixed> $data
     */
    public static function render(string $viewName, array $data = []): string
    {
        // Reset state
        self::reset();

        // Extract data to variables
        self::$data = $data;
        extract($data, EXTR_SKIP);

        // Include the view file
        ob_start();
        include self::$viewsPath . '/' . $viewName . '.php';
        $viewContent = ob_get_clean();

        // If view extends a layout, render layout with sections
        if (self::$layout) {
            extract(self::$data, EXTR_SKIP);
            ob_start();
            include self::$viewsPath . '/' . self::$layout . '.php';
            return ob_get_clean();
        }

        // No layout, return view content directly
        return $viewContent;
    }

    public static function extends(string $layout): void
    {
        self::$layout = $layout;
    }

    public static function section(string $name): void
    {
        self::$currentSection = $name;
        ob_start();
    }

    public static function endSection(): void
    {
        if (self::$currentSection) {
            self::$sections[self::$currentSection] = ob_get_clean();
            self::$currentSection = null;
        }
    }

    public static function yield(string $section, string $default = ''): string
    {
        return self::$sections[$section] ?? $default;
    }

    public static function getLayout(): string
    {
        return self::$layout;
    }

    /**
     * Escape HTML safely
     */
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Include sub-view/component
     */
    public static function include(string $component, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        include self::$viewsPath . '/' . $component . '.php';
    }

    private static function reset(): void
    {
        self::$sections = [];
        self::$currentSection = null;
        self::$layout = null;
        self::$data = [];
    }
}
