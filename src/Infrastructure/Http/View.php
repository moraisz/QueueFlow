<?php

namespace Src\Infrastructure\Http;

class View {
    private static string $viewsPath = __DIR__ . '/../Views';
    private static ?string $layout = null;
    private static array $sections = [];
    private static ?string $currentSection = null;

    /**
     * @param array<int,mixed> $data
     */
    public static function render(string $viewName, array $data = []): string {
        self::startEarlyHints();

        // Reset state
        self::$sections = [];
        self::$currentSection = null;
        self::$layout = null;

        // Extract data to variables
        extract($data, EXTR_SKIP);
        
        // Include the view file
        ob_start();
        include self::$viewsPath . '/' . $viewName . '.php';
        $viewContent = ob_get_clean();
        
        // If view extends a layout, render layout with sections
        if (self::$layout) {
            ob_start();
            include self::$viewsPath . '/' . self::$layout . '.php';
            return ob_get_clean();
        }
        
        // No layout, return view content directly
        return $viewContent;
    }

    private static function startEarlyHints(): void {
        if (!headers_sent()) {
            header('Link: </assets/css/style.css>; rel=preload; as=style', false, 103);
            header('Link: </assets/js/app.js>; rel=preload; as=script', false, 103);
            \headers_send(103);
        }
    }

    public static function extends(string $layout): void {
        self::$layout = $layout;
    }

    public static function section(string $name): void {
        self::$currentSection = $name;
        ob_start();
    }

    public static function endSection(): void {
        if (self::$currentSection) {
            self::$sections[self::$currentSection] = ob_get_clean();
            self::$currentSection = null;
        }
    }

    public static function yield(string $section, string $default = ''): string {
        return self::$sections[$section] ?? $default;
    }

    public static function getLayout(): string {
        return self::$layout;
    }
}
