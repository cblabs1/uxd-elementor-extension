/**
 * UXD Admin JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initAdminSettings();
        initWidgetToggles();
        initFormHandlers();
        initTooltips();
    });

    /**
     * Initialize admin settings
     */
    function initAdminSettings() {
        // Add loading states
        $(document).ajaxStart(function() {
            $('.uxd-admin-content').addClass('uxd-loading');
        }).ajaxStop(function() {
            $('.uxd-admin-content').removeClass('uxd-loading');
        });

        // Handle tab navigation
        $('.uxd-tabs a').on('click', function(e) {
            if ($(this).attr('href').indexOf('#') === -1) {
                return; // Let normal navigation happen
            }
            
            e.preventDefault();
            var target = $(this).attr('href');
            
            // Update active tab
            $('.uxd-tabs li').removeClass('active');
            $(this).parent().addClass('active');
            
            // Show target content
            $('.uxd-tab-content').hide();
            $(target).show();
        });

        // Auto-save settings on change
        let saveTimeout;
        $('input, select, textarea').on('change', function() {
            if ($(this).closest('#uxd-settings-form').length > 0) {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(function() {
                    showMessage('Settings will be saved when you click "Save Settings"', 'info', 2000);
                }, 1000);
            }
        });
    }

    /**
     * Initialize widget toggles
     */
    function initWidgetToggles() {
        // Handle widget toggle changes
        $('.uxd-widgets-table input[type="checkbox"]').on('change', function() {
            var $checkbox = $(this);
            var $status = $checkbox.closest('tr').find('.uxd-widget-status');
            var widgetName = $checkbox.data('widget');
            
            if ($checkbox.is(':checked')) {
                $status.text(uxd_admin_ajax.strings.enabled || 'Enabled');
                $status.addClass('enabled').removeClass('disabled');
                
                // Show success animation
                $checkbox.closest('td').addClass('uxd-animate-success');
                setTimeout(function() {
                    $checkbox.closest('td').removeClass('uxd-animate-success');
                }, 1000);
            } else {
                $status.text(uxd_admin_ajax.strings.disabled || 'Disabled');
                $status.addClass('disabled').removeClass('enabled');
                
                // Show disable animation
                $checkbox.closest('td').addClass('uxd-animate-disable');
                setTimeout(function() {
                    $checkbox.closest('td').removeClass('uxd-animate-disable');
                }, 1000);
            }
        });

        // Handle widget page toggles
        $('.uxd-widget-toggle input').on('change', function() {
            var $checkbox = $(this);
            var $status = $checkbox.closest('.uxd-widget-footer').find('.uxd-widget-status');
            var widget = $checkbox.data('widget');
            
            // Update status text
            if ($checkbox.is(':checked')) {
                $status.text('Enabled');
            } else {
                $status.text('Disabled');
            }

            // Save widget status
            saveWidgetStatus(widget, $checkbox.is(':checked'));
        });
    }

    /**
     * Initialize form handlers
     */
    function initFormHandlers() {
        // Handle settings form submission
        $('#uxd-settings-form').on('submit', function(e) {
            e.preventDefault();
            saveSettings();
        });

        // Handle reset settings
        $('#uxd-reset-settings').on('click', function(e) {
            e.preventDefault();
            
            if (confirm(uxd_admin_ajax.strings.confirm_reset || 'Are you sure you want to reset all settings?')) {
                resetSettings();
            }
        });

        // Handle import/export (if implemented)
        $('#uxd-export-settings').on('click', function(e) {
            e.preventDefault();
            exportSettings();
        });

        $('#uxd-import-settings').on('change', function(e) {
            importSettings(e.target.files[0]);
        });
    }

    /**
     * Initialize tooltips
     */
    function initTooltips() {
        // Add tooltips to pro badges
        $('.uxd-pro-badge').attr('title', 'This feature requires Pro version');
        
        // Add tooltips to switches
        $('.uxd-switch').attr('title', 'Click to toggle');
        
        // Initialize custom tooltips if needed
        $('[data-tooltip]').each(function() {
            var $element = $(this);
            var tooltip = $element.data('tooltip');
            
            $element.on('mouseenter', function() {
                showTooltip($element, tooltip);
            }).on('mouseleave', function() {
                hideTooltip();
            });
        });
    }

    /**
     * Save all settings
     */
    function saveSettings() {
        var formData = $('#uxd-settings-form').serialize();
        
        showMessage(uxd_admin_ajax.strings.saving || 'Saving...', 'info');
        
        $.ajax({
            url: uxd_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'uxd_save_settings',
                nonce: uxd_admin_ajax.nonce,
                settings: getFormDataAsObject($('#uxd-settings-form'))
            },
            success: function(response) {
                if (response.success) {
                    showMessage(uxd_admin_ajax.strings.saved || 'Settings saved!', 'success');
                    
                    // Add success animation to save button
                    $('#uxd-settings-form button[type="submit"]').addClass('uxd-animate-success');
                    setTimeout(function() {
                        $('#uxd-settings-form button[type="submit"]').removeClass('uxd-animate-success');
                    }, 2000);
                } else {
                    showMessage(response.data || uxd_admin_ajax.strings.error || 'Error saving settings', 'error');
                }
            },
            error: function() {
                showMessage(uxd_admin_ajax.strings.error || 'Error saving settings', 'error');
            }
        });
    }

    /**
     * Reset settings to default
     */
    function resetSettings() {
        showMessage('Resetting settings...', 'info');
        
        $.ajax({
            url: uxd_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'uxd_reset_settings',
                nonce: uxd_admin_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showMessage('Settings reset successfully!', 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showMessage(response.data || 'Error resetting settings', 'error');
                }
            },
            error: function() {
                showMessage('Error resetting settings', 'error');
            }
        });
    }

    /**
     * Save individual widget status
     */
    function saveWidgetStatus(widget, enabled) {
        $.ajax({
            url: uxd_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'uxd_save_widget_status',
                nonce: uxd_admin_ajax.nonce,
                widget: widget,
                enabled: enabled
            },
            success: function(response) {
                if (response.success) {
                    showMessage('Widget status updated', 'success', 2000);
                }
            }
        });
    }

    /**
     * Export settings
     */
    function exportSettings() {
        $.ajax({
            url: uxd_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'uxd_export_settings',
                nonce: uxd_admin_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Create download link
                    var blob = new Blob([JSON.stringify(response.data, null, 2)], {
                        type: 'application/json'
                    });
                    var url = window.URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = 'uxd-settings-export.json';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                    
                    showMessage('Settings exported successfully', 'success');
                }
            }
        });
    }

    /**
     * Import settings
     */
    function importSettings(file) {
        if (!file) return;
        
        var reader = new FileReader();
        reader.onload = function(e) {
            try {
                var settings = JSON.parse(e.target.result);
                
                $.ajax({
                    url: uxd_admin_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'uxd_import_settings',
                        nonce: uxd_admin_ajax.nonce,
                        settings: settings
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage('Settings imported successfully', 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            showMessage(response.data || 'Error importing settings', 'error');
                        }
                    }
                });
            } catch (error) {
                showMessage('Invalid settings file', 'error');
            }
        };
        reader.readAsText(file);
    }

    /**
     * Show message
     */
    function showMessage(message, type, duration) {
        type = type || 'info';
        duration = duration || 4000;
        
        // Remove existing messages
        $('.uxd-message').remove();
        
        var $message = $('<div class="uxd-message ' + type + '">' + message + '</div>');
        $('.uxd-admin-content').prepend($message);
        
        // Auto-remove after duration
        setTimeout(function() {
            $message.fadeOut(function() {
                $(this).remove();
            });
        }, duration);
        
        // Scroll to message
        $('html, body').animate({
            scrollTop: $message.offset().top - 100
        }, 300);
    }

    /**
     * Show tooltip
     */
    function showTooltip($element, text) {
        var $tooltip = $('<div class="uxd-tooltip">' + text + '</div>');
        $('body').append($tooltip);
        
        var position = $element.offset();
        $tooltip.css({
            position: 'absolute',
            top: position.top - $tooltip.outerHeight() - 5,
            left: position.left + ($element.outerWidth() / 2) - ($tooltip.outerWidth() / 2),
            zIndex: 9999
        });
        
        $tooltip.fadeIn(200);
    }

    /**
     * Hide tooltip
     */
    function hideTooltip() {
        $('.uxd-tooltip').fadeOut(200, function() {
            $(this).remove();
        });
    }

    /**
     * Convert form data to object
     */
    function getFormDataAsObject($form) {
        var formArray = $form.serializeArray();
        var formObject = {};
        
        $.each(formArray, function(i, field) {
            var name = field.name;
            var value = field.value;
            
            // Handle nested objects (e.g., enabled_widgets[widget-name])
            if (name.includes('[') && name.includes(']')) {
                var parts = name.split('[');
                var objName = parts[0];
                var propName = parts[1].replace(']', '');
                
                if (!formObject[objName]) {
                    formObject[objName] = {};
                }
                formObject[objName][propName] = value;
            } else {
                formObject[name] = value;
            }
        });
        
        // Handle unchecked checkboxes
        $form.find('input[type="checkbox"]').each(function() {
            var name = $(this).attr('name');
            if (name && !$(this).is(':checked')) {
                if (name.includes('[') && name.includes(']')) {
                    var parts = name.split('[');
                    var objName = parts[0];
                    var propName = parts[1].replace(']', '');
                    
                    if (!formObject[objName]) {
                        formObject[objName] = {};
                    }
                    formObject[objName][propName] = 'off';
                } else {
                    formObject[name] = 'off';
                }
            }
        });
        
        return formObject;
    }

    /**
     * Add custom CSS animations
     */
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .uxd-animate-success {
                animation: uxd-success-pulse 1s ease-in-out;
            }
            
            .uxd-animate-disable {
                animation: uxd-disable-fade 0.5s ease-in-out;
            }
            
            @keyframes uxd-success-pulse {
                0% { background-color: transparent; }
                50% { background-color: rgba(40, 167, 69, 0.2); }
                100% { background-color: transparent; }
            }
            
            @keyframes uxd-disable-fade {
                0% { opacity: 1; }
                50% { opacity: 0.5; }
                100% { opacity: 1; }
            }
            
            .uxd-tooltip {
                background: rgba(0, 0, 0, 0.8);
                color: white;
                padding: 8px 12px;
                border-radius: 4px;
                font-size: 12px;
                white-space: nowrap;
                display: none;
            }
            
            .uxd-tooltip::after {
                content: '';
                position: absolute;
                top: 100%;
                left: 50%;
                margin-left: -5px;
                border-width: 5px;
                border-style: solid;
                border-color: rgba(0, 0, 0, 0.8) transparent transparent transparent;
            }
        `)
        .appendTo('head');

})(jQuery);