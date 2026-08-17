<?php
namespace ProductsBanner\Views;

use ProductsBanner\Models\BannerModel;
use ProductsBanner\Repositories\SettingsRepository;
use ProductsBanner\Services\SettingsService;

class BannersFieldsView
{
/**
 * @var array<BannerModel>
 * @return string
 */
    public function render(array $banners): string
    {
        $html = $this->render_html($banners);
        $styles = $this->render_styles();
        $scripts = $this->render_scripts();
        return $html.$styles.$scripts;
    }

    /**
     * @var array<BannerModel>
     * @return string
     */
    private function render_html(array $banners): string
    {
        $domain = SettingsService::DOMAIN;
        $option_name = SettingsService::OPTION_NAME;
        $banners_key = SettingsRepository::BANNERS;
        $image_title = __('Зображення', $domain);
        $url_title = __('Посилання (URL)', $domain);
        $action_title = __('Дія', $domain);
        $add_baner = __('Додати банер', $domain);
        $name = esc_attr($option_name);
        $key = esc_attr($banners_key);
        $image_key = esc_attr(SettingsRepository::IMAGE);
        $url_key = esc_attr(SettingsRepository::URL);
        $html = <<<HTML
            <div id="banners-repeater">
                <table class="widefat" id="banners-table">
                    <thead>
                        <tr>
                            <th style="width: 40%;">{$image_title}</th>
                            <th style="width: 40%;">{$url_title}</th>
                        <th style="width: 20%;">${$action_title}</th>
                        </tr>
                    </thead>
                    <tbody>
HTML;
                if (!empty($banners) && is_array($banners)) {
                    foreach ($banners as $banner) {
                        $image = esc_attr($banner->get_image() ?? '');
                        $url = esc_attr($banner->get_url() ?? '');
                        $html .= <<< HTML
                        <tr class="banner-row">
                            <td>
                                <div class="banner-image-wrapper">
                                    <input type="hidden" class="banner-image-url"
                                        name="{$name}[{$key}][][{$image_key}]"
                                        value="{$image}" />
                                </div>
                            </td>
                            <td>
                                <input type="url" class="regular-text banner-url"
                                    name="{$name}[{$key}][][{$url_key}]"
                                    value="{$url}" placeholder="https://example.com" />
                            </td>
                        </tr>
                    HTML;
                    }
                }
                else {
                    $no_banners = __('Банери ще не додані. Натисніть "Додати банер" щоб почати.', $domain);
                    $html .= <<<HTML
                    <tr class="no-banners-message">
                        <td colspan="3" style="text-align: center; padding: 30px; color: #888;">{$no_banners}</td>
                    </tr>
                    HTML;
                }
                $html .= <<<HTML
            </tbody>
        </table>
        <p>
            <button type="button" class="button button-primary" id="add-banner">{$add_baner}</button>
        </p>
    </div>
    HTML;
    return $html;
    }

    private function render_styles(): string
    {
        $styles = <<<HTML
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
        HTML;
        return $styles;
    }

    private function render_scripts(): string
    {
        $domain = SettingsService::DOMAIN;
        $option_name = SettingsService::OPTION_NAME;
        $banners_key = SettingsRepository::BANNERS;
        $media_title = __('Виберіть зображення для банера', $domain);
        $media_button_text = __('Вибрати', $domain);
        $no_image = __('Зображення не вибрано', $domain);
        $image_key = esc_attr(SettingsRepository::IMAGE);
        $url_key = esc_attr(SettingsRepository::URL);
        $choose_image = __('Вибрати зображення', $domain);
        $delete = __('Видалити', $domain);
        $confirm = __('Ви впевнені, що хочете видалити цей банер?', $domain);
        $scripts = <<<HTML
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var frame = null;
                function openMediaUploader(button) {
                    var button = $(button);
                    var row = button.closest('.banner-row');
                    var imageInput = row.find('.banner-image-url');
                    var preview = row.find('.banner-image-preview');
                    var removeBtn = row.find('.remove-banner-image');
                    if (frame) {
                        frame.open();
                        return;
                    }
                    frame = wp.media({
                        title: '{$media_title}',
                        button: {
                            text: '{$media_button_text}'
                        },
                        multiple: false
                    });
                    frame.on('select', function () {
                        var attachment = frame.state().get('selection').first().toJSON();
                        imageInput.val(attachment.url);
                        preview.html('<img src="' + attachment.url + '" style="max-width: 150px; max-height: 80px;" />');
                        removeBtn.show();
                    });

                    frame.open();
                }
                function removeImage(button) {
                    var button = $(button);
                    var row = button.closest('.banner-row');
                    var imageInput = row.find('.banner-image-url');
                    var preview = row.find('.banner-image-preview');
                    var removeBtn = row.find('.remove-banner-image');

                    imageInput.val('');
                    preview.html('<span class="no-image">{$no_image}</span>');
                    removeBtn.hide();
                }
                function addNewRow() {
                    var tableBody = $('#banners-table tbody');
                    tableBody.find('.no-banners-message').remove();
                    var newRow = `
            <tr class="banner-row">
                <td>
                    <div class="banner-image-wrapper">
                        <input type="hidden"
                               class="banner-image-url"
                               name="{$option_name}[{$banners_key}][][{$image_key}]"
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
                    <input type="url" class="regular-text banner-url"
                           name="{$option_name}[{$banners_key}][][${url_key}]"
                           placeholder="https://example.com" />
                </td>
                <td>
                    <button type="button" class="button remove-banner">{$delete}</button>
                </td>
            </tr>
            `;
                    tableBody.append(newRow);
                }
                $('#add-banner').on('click', function () {
                    addNewRow();
                });
                $(document).on('click', '.select-banner-image', function () {
                    openMediaUploader(this);
                });
                $(document).on('click', '.remove-banner-image', function () {
                    removeImage(this);
                });
                $(document).on('click', '.remove-banner', function () {
                    if (confirm('<?php _e ?>')) {
                        var row = $(this).closest('tr');
                        row.remove();

                        if ($('#banners-table tbody tr').length === 0) {
                            $('#banners-table tbody').append(`
                    <tr class="no-banners-message">
                        <td colspan="3" style="text-align: center; padding: 30px; color: #888;">{$confirm}</td>
                    </tr>
                    `);
                        }
                    }
                });
            });
        </script>
        HTML;
        return $scripts;
    }
}
