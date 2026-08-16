<?php
namespace ProductsBanner\Controllers;

use ProductsBanner\Repositories\SettingsRepository;
use ProductsBanner\Services\SettingsService;
use ProductsBanner\Views\SettingsPageView;

class SettingsController
{
    private const SECTION = SettingsService::OPTION_NAME . "_section";

    private const BANNERS = self::SECTION . '_banners';

    private readonly SettingsPageView $page_view;

    private readonly SettingsService $service;

    public function __construct(SettingsPageView $settings_page_view, SettingsService $settings_service)
    {
        $this->page_view = $settings_page_view;
        $this->service = $settings_service;
    }

    public function register_editing_page(): void
    {
        add_submenu_page(
            "woocommerce",
            __(SettingsService::TITLE, SettingsService::DOMAIN),
            __(SettingsService::TITLE, SettingsService::DOMAIN),
            'manage_options',
            SettingsService::OPTION_NAME,
            [$this, 'render_editor_page']
        );
    }

    public function render_editor_page(): void
    {
        echo $this->page_view->render();
    }

    public function register_settings(): void
    {
        register_setting(
            SettingsService::OPTION_NAME,
            SettingsService::OPTION_NAME,
            [
                'type' => 'array',
                'default' => [],
                'sanitize_callback' => [$this, 'sanitize']
            ]
        );
        add_settings_section(
            self::SECTION,
            __("Налаштування", SettingsService::DOMAIN),
            null,
            SettingsService::OPTION_NAME
        );
        add_settings_field(
            self::BANNERS,
            __("Банери", SettingsService::OPTION_NAME),
            [$this, 'render_banners_fields'],
            SettingsService::OPTION_NAME,
            self::SECTION,
            ['settings' => $this->service->get_settings()]
        );
    }
}