/**
 * WP SEO Pro Admin JavaScript
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        init();
    });

    /**
     * Initialize admin functionality
     */
    function init() {
        initCharacterCounters();
        initImageSelectors();
        initTabs();
        initTooltips();
        initFormValidation();
    }

    /**
     * Initialize character counters for meta fields
     */
    function initCharacterCounters() {
        $('.wp-seo-pro-char-counter').each(function() {
            var $field = $(this);
            var $counter = $field.siblings('.char-counter');
            var maxLength = $field.data('max-length') || 160;
            
            function updateCounter() {
                var length = $field.val().length;
                $counter.text(length + '/' + maxLength);
                
                if (length > maxLength) {
                    $counter.addClass('over-limit');
                } else {
                    $counter.removeClass('over-limit');
                }
            }
            
            $field.on('input keyup', updateCounter);
            updateCounter();
        });
    }

    /**
     * Initialize image selectors
     */
    function initImageSelectors() {
        $('.wp-seo-pro-image-selector').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $input = $button.siblings('input[type="url"]');
            var $preview = $button.siblings('.image-preview');
            
            var frame = wp.media({
                title: wpSeoPro.strings.selectImage || 'Select Image',
                button: {
                    text: wpSeoPro.strings.useImage || 'Use Image'
                },
                multiple: false
            });
            
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $input.val(attachment.url);
                
                if ($preview.length) {
                    $preview.html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto;" />');
                }
            });
            
            frame.open();
        });
    }

    /**
     * Initialize tabs
     */
    function initTabs() {
        $('.wp-seo-pro-tabs').each(function() {
            var $tabs = $(this);
            var $tabButtons = $tabs.find('.tab-button');
            var $tabContents = $tabs.find('.tab-content');
            
            $tabButtons.on('click', function(e) {
                e.preventDefault();
                
                var $button = $(this);
                var targetTab = $button.data('tab');
                
                // Update active button
                $tabButtons.removeClass('active');
                $button.addClass('active');
                
                // Show target content
                $tabContents.removeClass('active');
                $tabs.find('[data-tab-content="' + targetTab + '"]').addClass('active');
            });
            
            // Activate first tab by default
            if ($tabButtons.length > 0) {
                $tabButtons.first().click();
            }
        });
    }

    /**
     * Initialize tooltips
     */
    function initTooltips() {
        $('[data-tooltip]').each(function() {
            var $element = $(this);
            var tooltipText = $element.data('tooltip');
            
            $element.hover(
                function() {
                    showTooltip($(this), tooltipText);
                },
                function() {
                    hideTooltip();
                }
            );
        });
    }

    /**
     * Show tooltip
     */
    function showTooltip($element, text) {
        var $tooltip = $('<div class="wp-seo-pro-tooltip">' + text + '</div>');
        $('body').append($tooltip);
        
        var offset = $element.offset();
        var elementHeight = $element.outerHeight();
        var tooltipHeight = $tooltip.outerHeight();
        
        $tooltip.css({
            position: 'absolute',
            top: offset.top - tooltipHeight - 10,
            left: offset.left + ($element.outerWidth() / 2) - ($tooltip.outerWidth() / 2),
            zIndex: 9999,
            background: '#000',
            color: '#fff',
            padding: '8px 12px',
            borderRadius: '4px',
            fontSize: '12px',
            whiteSpace: 'nowrap',
            boxShadow: '0 2px 8px rgba(0,0,0,0.2)'
        });
        
        // Add arrow
        $tooltip.append('<div class="tooltip-arrow" style="position: absolute; top: 100%; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 5px solid transparent; border-right: 5px solid transparent; border-top: 5px solid #000;"></div>');
    }

    /**
     * Hide tooltip
     */
    function hideTooltip() {
        $('.wp-seo-pro-tooltip').remove();
    }

    /**
     * Initialize form validation
     */
    function initFormValidation() {
        $('.wp-seo-pro-form').on('submit', function(e) {
            var $form = $(this);
            var isValid = true;
            var errors = [];
            
            // Validate required fields
            $form.find('[required]').each(function() {
                var $field = $(this);
                if (!$field.val().trim()) {
                    isValid = false;
                    errors.push($field.attr('name') + ' is required');
                    $field.addClass('error');
                } else {
                    $field.removeClass('error');
                }
            });
            
            // Validate URLs
            $form.find('input[type="url"]').each(function() {
                var $field = $(this);
                var value = $field.val().trim();
                if (value && !isValidUrl(value)) {
                    isValid = false;
                    errors.push($field.attr('name') + ' must be a valid URL');
                    $field.addClass('error');
                } else {
                    $field.removeClass('error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showValidationErrors(errors);
            }
        });
    }

    /**
     * Validate URL
     */
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    /**
     * Show validation errors
     */
    function showValidationErrors(errors) {
        var $errorContainer = $('.wp-seo-pro-errors');
        if ($errorContainer.length === 0) {
            $errorContainer = $('<div class="wp-seo-pro-errors notice notice-error"></div>');
            $('body').prepend($errorContainer);
        }
        
        var errorHtml = '<p><strong>Please fix the following errors:</strong></p><ul>';
        errors.forEach(function(error) {
            errorHtml += '<li>' + error + '</li>';
        });
        errorHtml += '</ul>';
        
        $errorContainer.html(errorHtml).show();
        
        // Scroll to errors
        $('html, body').animate({
            scrollTop: $errorContainer.offset().top - 20
        }, 500);
        
        // Hide errors after 5 seconds
        setTimeout(function() {
            $errorContainer.fadeOut();
        }, 5000);
    }

    /**
     * AJAX helper function
     */
    function ajaxRequest(action, data, successCallback, errorCallback) {
        var requestData = $.extend({
            action: action,
            nonce: wpSeoPro.nonce
        }, data);
        
        $.ajax({
            url: wpSeoPro.ajaxUrl,
            type: 'POST',
            data: requestData,
            success: function(response) {
                if (response.success) {
                    if (successCallback) {
                        successCallback(response.data);
                    }
                } else {
                    if (errorCallback) {
                        errorCallback(response.data);
                    } else {
                        showError(response.data);
                    }
                }
            },
            error: function(xhr, status, error) {
                if (errorCallback) {
                    errorCallback(error);
                } else {
                    showError('An error occurred: ' + error);
                }
            }
        });
    }

    /**
     * Show success message
     */
    function showSuccess(message) {
        showNotice(message, 'success');
    }

    /**
     * Show error message
     */
    function showError(message) {
        showNotice(message, 'error');
    }

    /**
     * Show notice
     */
    function showNotice(message, type) {
        var $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        $('.wrap h1').after($notice);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $notice.fadeOut();
        }, 5000);
        
        // Manual dismiss
        $notice.on('click', '.notice-dismiss', function() {
            $notice.fadeOut();
        });
    }

    /**
     * Debounce function
     */
    function debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }

    /**
     * Throttle function
     */
    function throttle(func, limit) {
        var inThrottle;
        return function() {
            var args = arguments;
            var context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(function() {
                    inThrottle = false;
                }, limit);
            }
        };
    }

    // Expose functions globally
    window.WPSeoPro = {
        ajaxRequest: ajaxRequest,
        showSuccess: showSuccess,
        showError: showError,
        showNotice: showNotice,
        debounce: debounce,
        throttle: throttle
    };

})(jQuery);