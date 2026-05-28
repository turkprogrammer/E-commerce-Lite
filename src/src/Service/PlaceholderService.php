<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Сервис для генерации SVG placeholder изображений
 */
class PlaceholderService
{
    /**
     * Генерирует SVG placeholder как data URI
     *
     * @param int $width Ширина изображения
     * @param int $height Высота изображения
     * @param string $text Текст для отображения
     * @param string $bgColor Цвет фона (hex)
     * @param string $textColor Цвет текста (hex)
     */
    public static function generate(
        int $width = 400,
        int $height = 400,
        string $text = 'Image',
        string $bgColor = '#e5e7eb',
        string $textColor = '#6b7280'
    ): string {
        $svg = <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg width="{$width}" height="{$height}" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:{$bgColor};stop-opacity:1" />
            <stop offset="100%" style="stop-color:{$textColor};stop-opacity:0.3" />
        </linearGradient>
    </defs>
    <rect width="{$width}" height="{$height}" fill="url(#grad)"/>
    <text x="50%" y="50%" font-family="system-ui, -apple-system, sans-serif" 
          font-size="24" fill="{$textColor}" text-anchor="middle" 
          dominant-baseline="middle" dy="0.3em">{$text}</text>
</svg>
SVG;

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Генерирует placeholder URL для товара
     *
     * @param string $text Текст для отображения
     * @return string Data URI с SVG placeholder
     */
    public static function forProduct(string $text): string
    {
        return self::generate(400, 400, $text, '#e5e7eb', '#6b7280');
    }
}
