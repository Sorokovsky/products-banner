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
use ProductsBanner\Repositories\SettingsRepository;
use ProductsBanner\Services\SettingsService;
use ProductsBanner\Views\SettingsPageView;

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
    private static ?ProductsBannerPlugin $instance = null;

    private readonly SettingsController $settings_controller;
    private readonly SettingsPageView $settings_page_view;

    private readonly SettingsRepository $settings_repository;

    private readonly SettingsService $settings_service;

    public static function get_instance(): ProductsBannerPlugin
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function activate(): void
    {

    }

    public function deactivate(): void
    {

    }

    private function __construct()
    {
        $this->init_repositories();
        $this->init_services();
        $this->init_views();
        $this->init_controllers();
        $this->register_hooks();
    }

    private function init_repositories(): void
    {
        $this->settings_repository = new SettingsRepository();
    }

    private function init_services(): void
    {
        $this->settings_service = new SettingsService($this->settings_repository);
    }

    private function init_views(): void
    {
        $this->settings_page_view = new SettingsPageView();
    }

    private function init_controllers(): void
    {
        $this->settings_controller = new SettingsController($this->settings_page_view, $this->settings_service);
    }

    private function register_hooks(): void
    {
        add_action("admin_menu", [$this->settings_controller, "register_editing_page"], 100);
    }
}

$plugin = ProductsBannerPlugin::get_instance();
register_activation_hook(__FILE__, [$plugin, 'activate']);
register_deactivation_hook(__FILE__, [$plugin, 'deactivate']);