<?php
/*
Plugin Name: Банери серед товарів
Description: Банери серед товарів для інтернет магазинів.
Version: 0.0.0
Requires at least: 5.8
Requires PHP: 8.1
Requires Plugins: woocommerce
Author: Sorokovskys
Text Domain: products-banner
*/

namespace ProductsBanner;

use ProductsBanner\Controllers\SettingsController;
use ProductsBanner\Parsers\BannersParser;
use ProductsBanner\Repositories\SettingsRepository;
use ProductsBanner\Services\SettingsService;
use ProductsBanner\Views\BannerItemFieldView;
use ProductsBanner\Views\BannersFieldsView;
use ProductsBanner\Views\RepeatFieldView;
use ProductsBanner\Views\SettingsPageView;
use ProductsBanner\Views\SkipFieldView;

if (!defined("ABSPATH")) {
    exit;
}

spl_autoload_register(function (string $class) {
    $prefix = 'ProductsBanner\\';
    $base_dir = __DIR__ . '/';
    $length = strlen($prefix);
    if (strncmp($prefix, $class, $length) !== 0) {
        return;
    }
    $relative_class = substr($class, $length);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        die("ProductsBanner: Файл не знайдено - " . $file);
    }
});

class ProductsBannerPlugin
{
    private readonly SettingsController $settings_controller;

    private readonly SettingsPageView $settings_page_view;

    private readonly RepeatFieldView $repeat_field_view;

    private readonly SkipFieldView $skip_field_view;

    private readonly BannersFieldsView $banners_fields_view;

    private readonly BannerItemFieldView $banner_item_field_view;

    private readonly SettingsRepository $settings_repository;

    private readonly SettingsService $settings_service;

    private readonly BannersParser $banners_parser;

    public function enqueue_admin_scripts(string $hook): void
    {
        if ($hook !== 'woocommerce_page_' . SettingsService::OPTION_NAME) {
            return;
        }

        wp_enqueue_media();

        wp_enqueue_script(
            'products-banner-admin',
            plugin_dir_url(__FILE__) . '../assets/admin.js',
            ['jquery', 'media-views', 'wp-i18n'],
            '1.0.0',
            true
        );

        wp_localize_script('products-banner-admin', 'productsBannerData', [
            'optionName' => SettingsService::OPTION_NAME,
            'domain' => SettingsService::DOMAIN,
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('products_banner_nonce'),
        ]);

        wp_enqueue_style(
            'products-banner-admin',
            plugin_dir_url(__FILE__) . '../assets/admin.css',
            [],
            '1.0.0'
        );
    }

    public function activate(): void
    {

    }

    public function deactivate(): void
    {

    }

    public function __construct()
    {
        $this->init_parsers();
        $this->init_repositories();
        $this->init_services();
        $this->init_views();
        $this->init_controllers();
        $this->register_hooks();
    }

    private function init_parsers(): void
    {
        $this->banners_parser = new BannersParser();
    }

    private function init_repositories(): void
    {
        $this->settings_repository = new SettingsRepository($this->banners_parser);
    }

    private function init_services(): void
    {
        $this->settings_service = new SettingsService($this->settings_repository);
    }

    private function init_views(): void
    {
        $this->banner_item_field_view = new BannerItemFieldView();
        $this->settings_page_view = new SettingsPageView();
        $this->skip_field_view = new SkipFieldView();
        $this->repeat_field_view = new RepeatFieldView();
        $this->banners_fields_view = new BannersFieldsView($this->banner_item_field_view);
    }

    private function init_controllers(): void
    {
        $this->settings_controller = new SettingsController(
            $this->settings_page_view,
            $this->settings_service,
            $this->skip_field_view,
            $this->repeat_field_view,
            $this->banners_fields_view
        );
    }

    private function register_hooks(): void
    {
        add_action('admin_init', [$this->settings_controller, 'register_settings']);
        add_action("admin_menu", [$this->settings_controller, "register_editing_page"], 100);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }
}

$plugin = new ProductsBannerPlugin();
register_activation_hook(__FILE__, [$plugin, 'activate']);
register_deactivation_hook(__FILE__, [$plugin, 'deactivate']);
