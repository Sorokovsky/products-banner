<?php

namespace ProductsBanner\Views;

use ProductsBanner\Services\SettingsService;

class SettingsPageView
{
    public function render(): string
    {
        $html = $this->render_html();
        $styles = $this->render_styles();
        return $html . $styles;
    }

    private function render_html(): string
    {
        $option_name = SettingsService::OPTION_NAME; // ← Додаємо змінну
        $domain = SettingsService::DOMAIN;
        $title = SettingsService::TITLE;
        ob_start();
        ?>
        <div class="wrap products-banner-settings">
            <h1><?php _e(SettingsService::TITLE, SettingsService::DOMAIN); ?></h1>

            <?php
            settings_errors($option_name);
            ?>

            <form method="post" action="options.php" class="products-banner-form">
                <?php
                settings_fields($option_name);
                do_settings_sections($option_name);
                ?>

                <div class="submit-wrapper">
                    <?php submit_button(__('Зберегти налаштування', SettingsService::DOMAIN), 'primary', 'submit', false); ?>
                    <span class="spinner"></span>
                </div>
            </form>

            <div class="products-banner-info">
                <h3><?php _e('Як це працює?', SettingsService::DOMAIN); ?></h3>
                <ul>
                    <li><?php _e('Додайте банери зображень з посиланнями', SettingsService::DOMAIN); ?></li>
                    <li><?php _e('Вкажіть через скільки товарів показувати банери', SettingsService::DOMAIN); ?></li>
                    <li><?php _e('Виберіть чи можуть банери повторюватися', SettingsService::DOMAIN); ?></li>
                    <li><?php _e('Банери будуть автоматично вставлятися в список товарів', SettingsService::DOMAIN); ?></li>
                </ul>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_styles(): string
    {
        return <<<HTML
        <style>
            .products-banner-settings {
                max-width: 1200px;
                margin: 20px 20px 0 0;
            }
            
            .products-banner-settings h1 {
                margin-bottom: 20px;
                padding-bottom: 10px;
                border-bottom: 1px solid #ccc;
            }
            
            .products-banner-form {
                background: #fff;
                padding: 20px 25px;
                border: 1px solid #ccd0d4;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
                margin-bottom: 20px;
            }
            
            .products-banner-form .form-table th {
                width: 200px;
                padding: 20px 10px 20px 0;
            }
            
            .products-banner-form .form-table td {
                padding: 20px 10px;
            }
            
            .submit-wrapper {
                display: flex;
                align-items: center;
                gap: 15px;
                padding-top: 20px;
                border-top: 1px solid #ddd;
                margin-top: 20px;
            }
            
            .submit-wrapper .spinner {
                float: none;
                margin: 0;
            }
            
            .products-banner-info {
                background: #f1f1f1;
                padding: 20px 25px;
                border-left: 4px solid #2271b1;
                margin-top: 20px;
            }
            
            .products-banner-info h3 {
                margin-top: 0;
                color: #1d2327;
            }
            
            .products-banner-info ul {
                margin: 10px 0 0;
                padding-left: 20px;
            }
            
            .products-banner-info ul li {
                margin: 8px 0;
                list-style: disc;
            }
            
            /* Стилі для таблиці банерів */
            #banners-table {
                margin: 10px 0;
            }
            
            #banners-table .banner-row td {
                vertical-align: middle;
                padding: 8px 10px;
            }
            
            #banners-table .regular-text {
                width: 100%;
                max-width: 400px;
            }
            
            #banners-table .remove-banner {
                color: #a00;
                border-color: #a00;
            }
            
            #banners-table .remove-banner:hover {
                color: #dc3232;
                border-color: #dc3232;
                background: #fbeaea;
            }
            
            #add-banner {
                margin-top: 5px;
            }
            
            /* Медіа-запити для адаптивності */
            @media screen and (max-width: 782px) {
                .products-banner-form .form-table th {
                    width: 100%;
                    padding: 10px 0 5px;
                }
                
                .products-banner-form .form-table td {
                    padding: 5px 0 15px;
                }
                
                #banners-table .regular-text {
                    max-width: 100%;
                }
                
                .submit-wrapper {
                    flex-direction: column;
                    align-items: flex-start;
                }
            }
            
            @media screen and (max-width: 600px) {
                .products-banner-settings {
                    margin: 10px 10px 0 0;
                }
                
                .products-banner-form {
                    padding: 15px;
                }
            }
        </style>
HTML;
    }
}