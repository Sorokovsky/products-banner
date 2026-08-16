<?php

namespace ProductsBanner\Repositories;

use ProductsBanner\Models\SettingsModel;

class SettingsRepository
{
    public const BANNERS = "banners";

    public const IMAGE = "image";

    public const URL = "url";

    public function __construct()
    {

    }

    public function get_settings(): SettingsModel
    {
        return new SettingsModel([], 1, false);
    }

    public function sanitize(mixed $input): array
    {
        if (!is_array($input)) {
            return [];
        }
        $sanitized = [];
        if (isset($input[self::BANNERS]) && is_array($input[self::BANNERS])) {
            foreach ($input[self::BANNERS] as $_ => $banner) {
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

                $sanitized[self::BANNERS][] = [self::IMAGE => $sanitized_image, self::URL => $sanitized_url];
            }
        }
        return $sanitized;
    }
}