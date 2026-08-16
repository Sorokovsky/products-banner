<?php
namespace ProductsBanner\Parsers;

use ProductsBanner\Models\BannerModel;
use ProductsBanner\Repositories\SettingsRepository;

class BannersParser
{
    public function __construct()
    {
    }

    /**
     * @param array $settings
     * @return array<BannerModel>
     */
    public function parse(array $settings): array
    {
        if (!isset($settings[SettingsRepository::BANNERS]) || !is_array($settings[SettingsRepository::BANNERS])) {
            return [];
        }

        $raw_banners = $settings[SettingsRepository::BANNERS];
        $result = [];

        foreach ($raw_banners as $banner) {
            if (!isset($banner[SettingsRepository::IMAGE]) || !isset($banner[SettingsRepository::URL])) {
                continue;
            }

            $result[] = new BannerModel(
                $banner[SettingsRepository::IMAGE],
                $banner[SettingsRepository::URL]
            );
        }

        return $result;
    }
}