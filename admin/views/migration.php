<?php
if (!defined('ABSPATH')) {
    exit;
}

$migration_manager = new WP_SEO_Pro\Migrations\MigrationManager();
$available_migrations = $migration_manager->get_available_migrations();
?>

<div class="wrap">
    <h1><?php _e('SEO Data Migration', 'wp-seo-pro'); ?></h1>
    
    <div class="wp-seo-pro-migration-intro">
        <p><?php _e('Migrate your SEO data from popular SEO plugins to WP SEO Pro. This will help you maintain your existing SEO settings and avoid starting from scratch.', 'wp-seo-pro'); ?></p>
    </div>
    
    <?php if (empty($available_migrations)): ?>
        <div class="wp-seo-pro-no-migrations">
            <h3><?php _e('No SEO Plugins Detected', 'wp-seo-pro'); ?></h3>
            <p><?php _e('We couldn\'t detect any compatible SEO plugins on your site. Make sure the plugin is installed and activated before attempting migration.', 'wp-seo-pro'); ?></p>
            <p><?php _e('Supported plugins:', 'wp-seo-pro'); ?></p>
            <ul>
                <li><?php _e('Yoast SEO', 'wp-seo-pro'); ?></li>
                <li><?php _e('All in One SEO Pack', 'wp-seo-pro'); ?></li>
                <li><?php _e('Rank Math', 'wp-seo-pro'); ?></li>
            </ul>
        </div>
    <?php else: ?>
        <div class="wp-seo-pro-migration-grid">
            <?php foreach ($available_migrations as $key => $migration): ?>
                <div class="wp-seo-pro-migration-card" data-migration="<?php echo esc_attr($key); ?>">
                    <div class="migration-header">
                        <h3><?php echo esc_html($migration['name']); ?></h3>
                        <span class="migration-version">v<?php echo esc_html($migration['version']); ?></span>
                    </div>
                    
                    <div class="migration-description">
                        <p><?php echo esc_html($migration['description']); ?></p>
                    </div>
                    
                    <div class="migration-status">
                        <div class="status-indicator" id="status-<?php echo esc_attr($key); ?>">
                            <span class="status-text"><?php _e('Ready to migrate', 'wp-seo-pro'); ?></span>
                        </div>
                    </div>
                    
                    <div class="migration-actions">
                        <button type="button" class="button button-primary migration-start" data-migration="<?php echo esc_attr($key); ?>">
                            <?php _e('Start Migration', 'wp-seo-pro'); ?>
                        </button>
                        <button type="button" class="button migration-check" data-migration="<?php echo esc_attr($key); ?>" style="display: none;">
                            <?php _e('Check Status', 'wp-seo-pro'); ?>
                        </button>
                    </div>
                    
                    <div class="migration-progress" id="progress-<?php echo esc_attr($key); ?>" style="display: none;">
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                        <div class="progress-text"><?php _e('Migrating...', 'wp-seo-pro'); ?></div>
                    </div>
                    
                    <div class="migration-results" id="results-<?php echo esc_attr($key); ?>" style="display: none;">
                        <h4><?php _e('Migration Results', 'wp-seo-pro'); ?></h4>
                        <div class="results-content"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="wp-seo-pro-migration-help">
        <h3><?php _e('Migration Tips', 'wp-seo-pro'); ?></h3>
        <ul>
            <li><?php _e('Always backup your site before starting migration.', 'wp-seo-pro'); ?></li>
            <li><?php _e('Migration will not delete data from the original plugin.', 'wp-seo-pro'); ?></li>
            <li><?php _e('You can deactivate the original plugin after successful migration.', 'wp-seo-pro'); ?></li>
            <li><?php _e('Review your SEO settings after migration to ensure everything is correct.', 'wp-seo-pro'); ?></li>
        </ul>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.migration-start').on('click', function() {
        var migrationType = $(this).data('migration');
        var $card = $('[data-migration="' + migrationType + '"]');
        var $progress = $('#progress-' + migrationType);
        var $results = $('#results-' + migrationType);
        var $status = $('#status-' + migrationType);
        
        // Show progress
        $progress.show();
        $results.hide();
        $(this).prop('disabled', true);
        
        // Start migration
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_seo_pro_migrate',
                migration_type: migrationType,
                nonce: wpSeoPro.nonce
            },
            success: function(response) {
                $progress.hide();
                
                if (response.success) {
                    $status.find('.status-text').text('<?php _e('Migration completed', 'wp-seo-pro'); ?>');
                    $status.removeClass('status-pending status-error').addClass('status-success');
                    
                    // Show results
                    var stats = response.data.stats;
                    var resultsHtml = '<ul>';
                    resultsHtml += '<li><strong><?php _e('Posts Processed:', 'wp-seo-pro'); ?></strong> ' + stats.posts_processed + '</li>';
                    resultsHtml += '<li><strong><?php _e('Meta Data Imported:', 'wp-seo-pro'); ?></strong> ' + stats.meta_imported + '</li>';
                    resultsHtml += '<li><strong><?php _e('Settings Imported:', 'wp-seo-pro'); ?></strong> ' + stats.settings_imported + '</li>';
                    resultsHtml += '</ul>';
                    
                    $results.find('.results-content').html(resultsHtml);
                    $results.show();
                } else {
                    $status.find('.status-text').text('<?php _e('Migration failed', 'wp-seo-pro'); ?>');
                    $status.removeClass('status-pending status-success').addClass('status-error');
                    
                    $results.find('.results-content').html('<p class="error">' + response.data + '</p>');
                    $results.show();
                }
            },
            error: function() {
                $progress.hide();
                $status.find('.status-text').text('<?php _e('Migration failed', 'wp-seo-pro'); ?>');
                $status.removeClass('status-pending status-success').addClass('status-error');
            },
            complete: function() {
                $('.migration-start[data-migration="' + migrationType + '"]').prop('disabled', false);
            }
        });
    });
    
    $('.migration-check').on('click', function() {
        var migrationType = $(this).data('migration');
        var $status = $('#status-' + migrationType);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_seo_pro_check_migration',
                migration_type: migrationType,
                nonce: wpSeoPro.nonce
            },
            success: function(response) {
                if (response.success) {
                    var status = response.data;
                    if (status.migrated) {
                        $status.find('.status-text').text('<?php _e('Migration completed', 'wp-seo-pro'); ?>');
                        $status.removeClass('status-pending status-error').addClass('status-success');
                    } else {
                        $status.find('.status-text').text('<?php _e('Ready to migrate', 'wp-seo-pro'); ?>');
                        $status.removeClass('status-success status-error').addClass('status-pending');
                    }
                }
            }
        });
    });
});
</script>

<style>
.wp-seo-pro-migration-intro {
    background: #f0f6fc;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    padding: 20px;
    margin: 20px 0;
}

.wp-seo-pro-no-migrations {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 30px;
    text-align: center;
    margin: 20px 0;
}

.wp-seo-pro-migration-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.wp-seo-pro-migration-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    transition: box-shadow 0.3s ease;
}

.wp-seo-pro-migration-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.migration-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.migration-header h3 {
    margin: 0;
    color: #23282d;
}

.migration-version {
    background: #f0f0f1;
    color: #666;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
}

.migration-description {
    margin-bottom: 15px;
    color: #666;
}

.migration-status {
    margin-bottom: 15px;
}

.status-indicator {
    display: inline-flex;
    align-items: center;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 14px;
}

.status-indicator.status-pending {
    background: #f0f6fc;
    color: #0073aa;
    border: 1px solid #c3c4c7;
}

.status-indicator.status-success {
    background: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
}

.status-indicator.status-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c2c7;
}

.migration-actions {
    margin-bottom: 15px;
}

.migration-actions .button {
    margin-right: 10px;
}

.migration-progress {
    margin-bottom: 15px;
}

.progress-bar {
    width: 100%;
    height: 20px;
    background: #f0f0f1;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 10px;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #0073aa, #005a87);
    width: 0%;
    transition: width 0.3s ease;
    animation: progress-animation 2s ease-in-out infinite;
}

@keyframes progress-animation {
    0% { width: 0%; }
    50% { width: 70%; }
    100% { width: 100%; }
}

.progress-text {
    text-align: center;
    color: #666;
    font-size: 14px;
}

.migration-results {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
}

.migration-results h4 {
    margin-top: 0;
    color: #23282d;
}

.results-content ul {
    margin: 10px 0 0 0;
    padding-left: 20px;
}

.results-content li {
    margin-bottom: 5px;
    color: #666;
}

.wp-seo-pro-migration-help {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    margin-top: 30px;
}

.wp-seo-pro-migration-help h3 {
    margin-top: 0;
    color: #23282d;
}

.wp-seo-pro-migration-help ul {
    margin: 10px 0 0 0;
    padding-left: 20px;
}

.wp-seo-pro-migration-help li {
    margin-bottom: 8px;
    color: #666;
}
</style>