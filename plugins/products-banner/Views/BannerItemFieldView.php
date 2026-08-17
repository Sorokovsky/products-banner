<?php
namespace ProductsBanner\Views;

use ProductsBanner\Models\BannerModel;
use ProductsBanner\Repositories\SettingsRepository;
use ProductsBanner\Services\SettingsService;

class BannerItemFieldView
{
    private const NAME = SettingsService::OPTION_NAME;
    private const KEY = SettingsRepository::BANNERS;
    private const IMAGE_KEY = SettingsRepository::IMAGE;
    private const URL_KEY = SettingsRepository::URL;

    public function render(int $index, ?BannerModel $banner): string
    {
        $html = $this->render_html($index, $banner);
        return $html;
    }

    private function render_html(int $index, ?BannerModel $banner): string {
        if ($banner === null) {
            return $this->render_new_banner($index);
        } else {
            return $this->render_old_banner($index, $banner);
        }
    }

    private function render_old_banner(int $index, BannerModel $banner): string
    {
        $image = $banner->get_image_url();
        $url = $banner->get_url();
        $name = self::NAME;
        $key = self::KEY;
        $url_key = self::URL_KEY;
        $image_key = self::IMAGE_KEY;
        $html = <<<HTML
            <tr class="banner-row">
                <td>
                    <div class="banner-image-wrapper">
                        <input type="hidden" class="banner-image-url"
                            name="{$name}[{$key}][{$index}][{$image_key}]"
                            value="{$image}" />
                    </div>
                </td>
                <td>
                    <input type="text" class="regular-text banner-url"
                        name="{$name}[{$key}][{$index}][{$url_key}]"
                        value="{$url}" placeholder="https://example.com" />
                </td>
            </tr>
        HTML;
        return $html;
    }

    private function render_new_banner(int $index): string
    {
        $domain = SettingsService::DOMAIN;
        $choose_image = __('Вибрати зображення', $domain);
        $delete = __('Видалити', $domain);
        $no_image = __('Зображення не вибрано', $domain);
        $name = self::NAME;
        $key = self::KEY;
        $url_key = self::URL_KEY;
        $image_key = self::IMAGE_KEY;
        $html = <<<HTML
        <tr class="banner-row">
            <td>
                <div class="banner-image-wrapper">
                    <input type="hidden"
                           class="banner-image-url"
                           name="{$name}[{$key}][{$index}][{$image_key}]"
                           value="" />
                    <div class="banner-image-preview">
                        <span class="no-image">{$no_image}</span>
                    </div>
                    <div class="banner-image-actions">
                        <button type="button" class="button button-small select-banner-image">{$choose_image}</button>
                        <button type="button" class="button button-small remove-banner-image" style="display:none;">{$delete}</button>
                    </div>
                </div>
            </td>
            <td>
                <input type="text" class="regular-text banner-url"
                    name="{$name}[{$key}][{$index}][{$url_key}]" placeholder="https://example.com" />
            </td>
            <td>
                <button type="button" class="button remove-banner">{$delete}</button>
            </td>
        </tr>
        HTML;
        return $html;
    }
}
