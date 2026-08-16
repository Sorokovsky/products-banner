<?php
namespace ProductsBanner\Controllers;

use ProductsBanner\Repositories\SettingsRepository;
use ProductsBanner\Services\SettingsService;
use ProductsBanner\Views\RepeatFieldView;
use ProductsBanner\Views\SettingsPageView;
use ProductsBanner\Views\SkipFieldView;

class SettingsController
{
    private const SECTION = SettingsService::OPTION_NAME . "_section";

    private readonly SettingsPageView $page_view;

    private readonly SkipFieldView $skip_field_view;

    private readonly RepeatFieldView $repeat_field_view;

    private readonly SettingsService $service;

    public function __construct(
        SettingsPageView $settings_page_view,
        SettingsService $settings_service,
        SkipFieldView $skip_field_view,
        RepeatFieldView $repeat_field_view
    ) {
        $this->page_view = $settings_page_view;
        $this->service = $settings_service;
        $this->skip_field_view = $skip_field_view;
        $this->repeat_field_view = $repeat_field_view;
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
                'sanitize_callback' => [$this->service, 'sanitize']
            ]
        );
        add_settings_section(
            self::SECTION,
            __("Налаштування", SettingsService::DOMAIN),
            null,
            SettingsService::OPTION_NAME
        );
        add_settings_field(
            SettingsRepository::BANNERS,
            __("Банери", SettingsService::OPTION_NAME),
            [$this, 'render_banners_fields'],
            SettingsService::OPTION_NAME,
            self::SECTION,
            ['settings' => $this->service->get_settings()]
        );
        add_settings_field(
            SettingsRepository::SKIP,
            __("Через число товарів", SettingsService::DOMAIN),
            [$this, 'render_skip_field'],
            SettingsService::OPTION_NAME,
            self::SECTION,
            [
                'label_for' => SettingsRepository::SKIP,
                'settings' => $this->service->get_settings()
            ]
        );

        add_settings_field(
            SettingsRepository::REPEAT,
            __("Чи можуть банери повторюватися", SettingsService::DOMAIN),
            [$this, 'render_repeat_field'],
            SettingsService::OPTION_NAME,
            self::SECTION,
            [
                'label_for' => SettingsRepository::REPEAT,
                'settings' => $this->service->get_settings()
            ]
        );
    }

    public function render_banners_fields(array $args): void
    {
        $settings = $args['settings'] ?? null;
        $banners = $settings ? $settings->get_banners() : [];
        $option_name = SettingsService::OPTION_NAME;
        $banners_key = SettingsRepository::BANNERS;
        $image_key = SettingsRepository::IMAGE;
        $url_key = SettingsRepository::URL;
        $domain = SettingsService::DOMAIN;

        ?>
        <div id="banners-repeater">
            <table class="widefat" id="banners-table">
                <thead>
                    <tr>
                        <th style="width: 40%;"><?php _e('Зображення', $domain); ?></th>
                        <th style="width: 40%;"><?php _e('Посилання (URL)', $domain); ?></th>
                        <th style="width: 20%;"><?php _e('Дія', $domain); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($banners) && is_array($banners)): ?>
                        <?php foreach ($banners as $banner): ?>
                            <tr class="banner-row">
                                <td>
                                    <div class="banner-image-wrapper">
                                        <input type="hidden" class="banner-image-url"
                                            name="<?php echo esc_attr($option_name); ?>[<?php echo esc_attr($banners_key); ?>][][<?php echo esc_attr($image_key); ?>]"
                                            value="<?php echo esc_attr($banner->get_image() ?? ''); ?>" />
                                    </div>
                                </td>
                                <td>
                                    <input type="url" class="regular-text banner-url"
                                        name="<?php echo esc_attr($option_name); ?>[<?php echo esc_attr($banners_key); ?>][][<?php echo esc_attr($url_key); ?>]"
                                        value="<?php echo esc_attr($banner->get_url() ?? ''); ?>" placeholder="https://example.com" />
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="no-banners-message">
                            <td colspan="3" style="text-align: center; padding: 30px; color: #888;">
                                <?php _e('Банери ще не додані. Натисніть "Додати банер" щоб почати.', $domain); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <p>
                <button type="button" class="button button-primary" id="add-banner">
                    <?php _e('Додати банер', $domain); ?>
                </button>
            </p>
        </div>

        <style>
            .banner-image-wrapper {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            .banner-image-preview {
                min-width: 80px;
                min-height: 60px;
                border: 1px solid #ddd;
                border-radius: 4px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #f9f9f9;
                padding: 5px;
            }

            .banner-image-preview img {
                display: block;
                max-width: 150px;
                max-height: 80px;
                object-fit: contain;
            }

            .banner-image-preview .no-image {
                color: #aaa;
                font-size: 12px;
                padding: 10px;
            }

            .banner-image-actions {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            .banner-image-actions .button {
                white-space: nowrap;
            }

            #banners-table .banner-row td {
                vertical-align: middle;
                padding: 10px;
            }

            #banners-table .regular-text {
                width: 100%;
                max-width: 300px;
            }
        </style>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var frame = null;

                // Функція для відкриття медіа-завантажувача
                function openMediaUploader(button) {
                    var $button = $(button);
                    var $row = $button.closest('.banner-row');
                    var $imageInput = $row.find('.banner-image-url');
                    var $preview = $row.find('.banner-image-preview');
                    var $removeBtn = $row.find('.remove-banner-image');

                    if (frame) {
                        frame.open();
                        return;
                    }

                    frame = wp.media({
                        title: '<?php _e('Виберіть зображення для банера', $domain); ?>',
                        button: {
                            text: '<?php _e('Вибрати', $domain); ?>'
                        },
                        multiple: false
                    });

                    frame.on('select', function () {
                        var attachment = frame.state().get('selection').first().toJSON();
                        $imageInput.val(attachment.url);
                        $preview.html('<img src="' + attachment.url + '" style="max-width: 150px; max-height: 80px;" />');
                        $removeBtn.show();
                    });

                    frame.open();
                }

                // Функція для видалення зображення
                function removeImage(button) {
                    var $button = $(button);
                    var $row = $button.closest('.banner-row');
                    var $imageInput = $row.find('.banner-image-url');
                    var $preview = $row.find('.banner-image-preview');
                    var $removeBtn = $row.find('.remove-banner-image');

                    $imageInput.val('');
                    $preview.html('<span class="no-image"><?php _e('Зображення не вибрано', $domain); ?></span>');
                    $removeBtn.hide();
                }

                // Функція для додавання нового рядка
                function addNewRow() {
                    var $tableBody = $('#banners-table tbody');
                    $tableBody.find('.no-banners-message').remove();

                    var newRow = `
            <tr class="banner-row">
                <td>
                    <div class="banner-image-wrapper">
                        <input type="hidden" 
                               class="banner-image-url" 
                               name="<?php echo esc_attr($option_name); ?>[<?php echo esc_attr($banners_key); ?>][][<?php echo esc_attr($image_key); ?>]" 
                               value="" />
                        <div class="banner-image-preview">
                            <span class="no-image"><?php _e('Зображення не вибрано', $domain); ?></span>
                        </div>
                        <div class="banner-image-actions">
                            <button type="button" class="button button-small select-banner-image">
                                <?php _e('Вибрати зображення', $domain); ?>
                            </button>
                            <button type="button" class="button button-small remove-banner-image" style="display:none;">
                                <?php _e('Видалити', $domain); ?>
                            </button>
                        </div>
                    </div>
                </td>
                <td>
                    <input type="url" class="regular-text banner-url"
                           name="<?php echo esc_attr($option_name); ?>[<?php echo esc_attr($banners_key); ?>][][<?php echo esc_attr($url_key); ?>]" 
                           placeholder="https://example.com" />
                </td>
                <td>
                    <button type="button" class="button remove-banner"><?php _e('Видалити банер', $domain); ?></button>
                </td>
            </tr>
            `;

                    $tableBody.append(newRow);
                }

                // Додавання банера
                $('#add-banner').on('click', function () {
                    addNewRow();
                });

                // Вибір зображення (делегування для динамічних елементів)
                $(document).on('click', '.select-banner-image', function () {
                    openMediaUploader(this);
                });

                // Видалення зображення
                $(document).on('click', '.remove-banner-image', function () {
                    removeImage(this);
                });

                // Видалення банера
                $(document).on('click', '.remove-banner', function () {
                    if (confirm('<?php _e('Ви впевнені, що хочете видалити цей банер?', $domain); ?>')) {
                        var $row = $(this).closest('tr');
                        $row.remove();

                        if ($('#banners-table tbody tr').length === 0) {
                            $('#banners-table tbody').append(`
                    <tr class="no-banners-message">
                        <td colspan="3" style="text-align: center; padding: 30px; color: #888;">
                            <?php _e('Банери ще не додані. Натисніть "Додати банер" щоб почати.', $domain); ?>
                        </td>
                    </tr>
                    `);
                        }
                    }
                });
            });
        </script>
        <?php
    }

    public function render_repeat_field(): void
    {
        echo $this->repeat_field_view->render($this->service->get_settings()->is_repeat());
    }

    public function render_skip_field(): void
    {
        echo $this->skip_field_view->render($this->service->get_settings()->get_skip_count());
    }
}