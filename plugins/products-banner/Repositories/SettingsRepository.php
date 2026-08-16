<?php

namespace ProductsBanner\Repositories;

use ProductsBanner\Models\SettingsModel;
use ProductsBanner\Parsers\BannersParser;
use ProductsBanner\Services\SettingsService;

class SettingsRepository
{
    public const BANNERS = "banners";

    public const SKIP = "skip";

    public const REPEAT = "repeat";

    public const IMAGE = "image";

    public const URL = "url";

    private readonly BannersParser $banners_parser;

    public function __construct(BannersParser $banners_parser)
    {
        $this->banners_parser = $banners_parser;
    }

    public function get_settings(): SettingsModel
    {
        $settings = get_option(SettingsService::OPTION_NAME, []);
        $banners = $this->banners_parser->parse($settings);
        $skip = isset($settings[self::SKIP]) ? (int) $settings[self::SKIP] : 1;
        $repeat = isset($settings[self::REPEAT]) ? (bool) $settings[self::REPEAT] : false;
        return new SettingsModel($banners, $skip, $repeat);
    }

    public function sanitize(mixed $input): array
    {
        if (!is_array($input)) {
            return [];
        }

        $sanitized = [];
        if (isset($input[self::BANNERS]) && is_array($input[self::BANNERS])) {
            $banners = array_values($input[self::BANNERS]);
            foreach ($banners as $banner) {
                if (!is_array($banner) || !isset($banner[self::IMAGE]) || !isset($banner[self::URL])) {
                    continue;
                }

                $image = trim($banner[self::IMAGE]);
                $url = trim($banner[self::URL]);

                if (empty($image) || empty($url)) {
                    continue;
                }

                $sanitized_image = sanitize_url($image, ['http', 'https']);
                $sanitized_url = sanitize_url($url, ['http', 'https']);

                if (empty($sanitized_image) || empty($sanitized_url)) {
                    continue;
                }

                // Використовуємо [] для автоматичної переіндексації
                $sanitized[self::BANNERS][] = [
                    self::IMAGE => $sanitized_image,
                    self::URL => $sanitized_url
                ];
            }
        }

        $sanitized[self::BANNERS] = $sanitized[self::BANNERS] ?? [];
        if (isset($input[self::SKIP])) {
            $value = (int) $input[self::SKIP];
            $sanitized[self::SKIP] = max(1, min(100, $value));
        } else {
            $sanitized[self::SKIP] = 1;
        }
        $sanitized[self::REPEAT] = isset($input[self::REPEAT])
            ? filter_var($input[self::REPEAT], FILTER_VALIDATE_BOOLEAN)
            : false;

        return $sanitized;
    }
}