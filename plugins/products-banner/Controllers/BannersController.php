<?php
namespace ProductsBanner\Controllers;

use ProductsBanner\Models\BannerModel;
use ProductsBanner\Services\SettingsService;
use ProductsBanner\Views\BannerView;

class BannersController
{
    private readonly SettingsService $settings_service;
    private readonly BannerView $banner_view;
    private int $counter;
    private int $banner_index;

    public function __construct(SettingsService $settings_service, BannerView $banner_view)
    {
        $this->settings_service = $settings_service;
        $this->banner_view = $banner_view;
        $this->counter = 0;
        $this->banner_index = 0;
    }

    public function maybe_insert_banner(): void
    {
        $settings = $this->settings_service->get_settings();
        $banners = $settings->get_banners();
        if (empty($banners)) {
            return;
        }
        $skip = $settings->get_skip_count();
        $repeat = $settings->is_repeat();
        $this->counter++;
        if ($this->counter % $skip === 0) {
            $banner = $this->get_next_banner($banners, $repeat);;
            if ($banner) {
                echo $this->banner_view->render($banner);
            }
        }
    }

    private function get_next_banner(array $banners, bool $repeat): ?BannerModel
    {
        if (empty($banners)) {
            return null;
        }
        if ($repeat) {
            $index = $this->banner_index % count($banners);
            $this->banner_index++;
            return $banners[$index];
        }

        if ($this->banner_index < count($banners)) {
            $banner = $banners[$this->banner_index];
            $this->banner_index++;
            return $banner;
        }
        return null;
    }
}
