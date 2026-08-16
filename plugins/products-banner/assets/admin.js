/**
 * Products Banner Admin Scripts
 * 
 * @package ProductsBanner
 */

(function($) {
    'use strict';

    /**
     * Клас для управління банерами
     */
    class BannersManager {
        constructor() {
            this.rowIndex = 0;
            this.mediaFrame = null;
            this.optionName = window.productsBannerData?.optionName || 'products_banner_settings';
            this.domain = window.productsBannerData?.domain || 'products-banner';
            
            this.init();
        }

        /**
         * Ініціалізація
         */
        init() {
            // Оновлюємо rowIndex
            const rows = $('#banners-table tbody tr.banner-row').length;
            this.rowIndex = rows || 0;

            // Додавання банера
            $('#add-banner').on('click', () => this.addBanner());

            // Видалення банера
            $(document).on('click', '.remove-banner', (e) => this.removeBanner(e));

            // Вибір зображення
            $(document).on('click', '.select-banner-image', (e) => this.openMediaUploader(e));

            // Видалення зображення
            $(document).on('click', '.remove-banner-image', (e) => this.removeImage(e));

            // Оновлення прев'ю при зміні URL вручну
            $(document).on('change', '.banner-image-url', (e) => this.updatePreview(e));
        }

        /**
         * Додавання нового банера
         */
        addBanner() {
            const $tableBody = $('#banners-table tbody');
            
            // Видаляємо повідомлення "немає банерів"
            $tableBody.find('.no-banners-message').remove();

            const index = this.rowIndex;
            const imageKey = 'image';
            const urlKey = 'url';
            const optionName = this.optionName;
            const bannersKey = 'banners';
            const domain = this.domain;

            const newRow = `
                <tr class="banner-row" data-index="${index}">
                    <td>
                        <div class="banner-image-wrapper">
                            <input type="hidden" 
                                   class="banner-image-url" 
                                   name="${optionName}[${bannersKey}][${index}][${imageKey}]" 
                                   value="" />
                            <div class="banner-image-preview">
                                <span class="no-image">${this.translate('Зображення не вибрано', domain)}</span>
                            </div>
                            <div class="banner-image-actions">
                                <button type="button" class="button button-small select-banner-image">
                                    ${this.translate('Вибрати зображення', domain)}
                                </button>
                                <button type="button" class="button button-small remove-banner-image" style="display:none;">
                                    ${this.translate('Видалити', domain)}
                                </button>
                            </div>
                        </div>
                    </td>
                    <td>
                        <input type="url" class="regular-text banner-url"
                               name="${optionName}[${bannersKey}][${index}][${urlKey}]" 
                               placeholder="https://example.com" />
                    </td>
                    <td>
                        <div class="banner-actions">
                            <button type="button" class="button remove-banner">
                                ${this.translate('Видалити банер', domain)}
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            
            $tableBody.append(newRow);
            this.rowIndex++;

            // Прокручуємо до нового рядка
            const $newRow = $tableBody.find('tr:last-child');
            $newRow[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        /**
         * Видалення банера
         */
        removeBanner(e) {
            const $button = $(e.currentTarget);
            const $row = $button.closest('tr');
            const domain = this.domain;

            if (confirm(this.translate('Ви впевнені, що хочете видалити цей банер?', domain))) {
                $row.fadeOut(300, function() {
                    $(this).remove();
                    
                    // Перевіряємо чи залишились банери
                    if ($('#banners-table tbody tr').length === 0) {
                        const domain = window.productsBannerData?.domain || 'products-banner';
                        $('#banners-table tbody').append(`
                            <tr class="no-banners-message">
                                <td colspan="3" style="text-align: center; padding: 30px; color: #888;">
                                    ${this.translate('Банери ще не додані. Натисніть "Додати банер" щоб почати.', domain)}
                                </td>
                            </tr>
                        `);
                    }
                }.bind(this));
            }
        }

        /**
         * Відкриття медіа-завантажувача
         */
        openMediaUploader(e) {
            const $button = $(e.currentTarget);
            const $row = $button.closest('.banner-row');
            const $imageInput = $row.find('.banner-image-url');
            const $preview = $row.find('.banner-image-preview');
            const $removeBtn = $row.find('.remove-banner-image');

            // Якщо медіа-завантажувач вже відкритий - закриваємо
            if (this.mediaFrame) {
                this.mediaFrame.open();
                return;
            }

            // Перевіряємо чи підключений wp.media
            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                alert(this.translate('Помилка: медіа-завантажувач не доступний. Будь ласка, перезавантажте сторінку.', this.domain));
                return;
            }

            // Створюємо медіа-завантажувач
            this.mediaFrame = wp.media({
                title: this.translate('Виберіть зображення для банера', this.domain),
                button: {
                    text: this.translate('Вибрати', this.domain)
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });

            // Обробка вибору зображення
            this.mediaFrame.on('select', function() {
                const attachment = this.mediaFrame.state().get('selection').first().toJSON();
                
                // Оновлюємо поле з URL
                $imageInput.val(attachment.url).trigger('change');
                
                // Оновлюємо прев'ю
                const previewHtml = `<img src="${attachment.url}" alt="${attachment.alt || ''}" />`;
                $preview.html(previewHtml);
                
                // Показуємо кнопку видалення
                $removeBtn.show();
            }.bind(this));

            this.mediaFrame.open();
        }

        /**
         * Видалення зображення
         */
        removeImage(e) {
            const $button = $(e.currentTarget);
            const $row = $button.closest('.banner-row');
            const $imageInput = $row.find('.banner-image-url');
            const $preview = $row.find('.banner-image-preview');
            const $removeBtn = $row.find('.remove-banner-image');
            const domain = this.domain;

            if (confirm(this.translate('Ви впевнені, що хочете видалити це зображення?', domain))) {
                $imageInput.val('').trigger('change');
                $preview.html(`<span class="no-image">${this.translate('Зображення не вибрано', domain)}</span>`);
                $removeBtn.hide();
            }
        }

        /**
         * Оновлення прев'ю при зміні URL вручну
         */
        updatePreview(e) {
            const $input = $(e.currentTarget);
            const $row = $input.closest('.banner-row');
            const $preview = $row.find('.banner-image-preview');
            const $removeBtn = $row.find('.remove-banner-image');
            const url = $input.val();

            if (url) {
                // Перевіряємо чи це валідний URL зображення
                const img = new Image();
                img.onload = function() {
                    $preview.html(`<img src="${url}" alt="" />`);
                    $removeBtn.show();
                };
                img.onerror = function() {
                    // Якщо не валідне зображення - показуємо текст
                    $preview.html(`<span class="no-image">${this.translate('Невірне посилання', this.domain)}</span>`);
                }.bind(this);
                img.src = url;
            } else {
                $preview.html(`<span class="no-image">${this.translate('Зображення не вибрано', this.domain)}</span>`);
                $removeBtn.hide();
            }
        }

        /**
         * Переклад тексту
         */
        translate(text, domain) {
            // Якщо є функція __ (WordPress) - використовуємо її
            if (typeof __ === 'function') {
                return __(text, domain);
            }
            // Інакше повертаємо текст без перекладу
            return text;
        }
    }

    /**
     * Ініціалізація після завантаження DOM
     */
    $(document).ready(function() {
        new BannersManager();
    });

})(jQuery);