<?php
// no direct access
if (! isset($data)) {
	exit;
}

// Show areas by:
// "Plugins", "Themes" (parent theme and child theme), "WordPress Core"
// External locations (outside plugins and themes)
// 3rd party external locations (e.g. Google API Fonts, CND urls such as the ones for Bootstrap etc.)
$listAreaStatus = $data['plugin_settings']['assets_list_layout_areas_status'];

/*
* -------------------------
* [START] BY EACH LOCATION
* -------------------------
*/
?>

<div>
    <?php
    if (! empty($data['all']['styles']) || ! empty($data['all']['scripts'])) {
    ?>
    <p><?php echo sprintf(
            __('Please select the styles &amp; scripts that are %sNOT NEEDED%s from the list below. Not sure which ones to unload? %s Use "Test Mode" (to make the changes apply only to you), while you are going through the trial &amp; error process.', 'wp-asset-clean-up'),
            '<span style="color: #CC0000;"><strong>',
            '</strong></span>',
		    '<img draggable="false" class="wpacu-emoji" alt="🤔" src="https://s.w.org/images/core/emoji/12.0.0-1/svg/1f914.svg" />'
        ); ?> <?php echo __('"Load in on this page (make exception)" will take effect when a bulk unload rule is used. Otherwise, the asset will load anyway unless you select it for unload.', 'wp-asset-clean-up'); ?></p>
    <?php
    if ($data['plugin_settings']['hide_core_files']) {
        ?>
        <div class="wpacu_note"><span class="dashicons dashicons-info"></span> WordPress CSS &amp; JavaScript core files are hidden as requested in the plugin's settings. They are meant to be managed by experienced developers in special situations.</div>
        <div class="wpacu-clearfix" style="margin-top: 10px;"></div>
        <?php
    }
    ?>
</div>

        <div style="margin: 10px 0;">
	    <?php
	    echo $data['assets_list_layout_output'];
	    ?>
        </div>

        <div style="margin-bottom: 20px;" class="wpacu-contract-expand-area">
            <div class="col-left">
                <strong>&#10141; Total enqueued files (including core files): <?php echo (int)$data['total_styles'] + (int)$data['total_scripts']; ?></strong>
            </div>
            <div class="col-right">
                <a href="#" id="wpacu-assets-contract-all" class="wpacu-wp-button wpacu-wp-button-secondary">Contract All Areas</a>&nbsp;
                <a href="#" id="wpacu-assets-expand-all" class="wpacu-wp-button wpacu-wp-button-secondary">Expand All Areas</a>
            </div>
            <div class="wpacu-clearfix"></div>
        </div>

        <?php
	    if (! function_exists('get_plugins') && ! is_admin()) {
		    require_once ABSPATH . 'wp-admin/includes/plugin.php';
	    }

	    $allPlugins = get_plugins();
	    $allThemes  = wp_get_themes();
	    $allActivePluginsIcons = \WpAssetCleanUp\Misc::getAllActivePluginsIcons();

	    $locationsText = array(
            'plugins'  => '<span class="dashicons dashicons-admin-plugins"></span> From Plugins (.css &amp; .js)',
            'themes'   => '<span class="dashicons dashicons-admin-appearance"></span> From Themes (.css &amp; .js)',
            'uploads'  => '<span class="dashicons dashicons-wordpress"></span> WordPress Uploads Directory (.css &amp; .js)',
            'wp_core'  => '<span class="dashicons dashicons-wordpress"></span> WordPress Core (.css &amp; .js)',
            'external' => '<span class="dashicons dashicons-cloud"></span> External 3rd Party (.css &amp; .js)'
        );

	    $data['view_by_location'] =
        $data['rows_build_array'] =
        $data['rows_by_location'] = true;

        $data['rows_assets'] = array();

        require_once __DIR__.'/_asset-style-rows.php';
        require_once __DIR__.'/_asset-script-rows.php';

        if (! empty($data['rows_assets'])) {
            // Sorting: Plugins, Themes, Uploads Directory and External Assets
            $rowsAssets = array('plugins' => array(), 'themes' => array(), 'uploads' => array(), 'external' => array());

	        foreach ($data['rows_assets'] as $locationMain => $values) {
		        $rowsAssets[$locationMain] = $values;
	        }

            foreach ($rowsAssets as $locationMain => $values) {
	            ksort($values);
	            $totalLocationAssets  = count($values);
	            $hideLocationMainArea = ($locationMain === 'uploads' && $totalLocationAssets === 0);
	            $hideListOfAssetsOnly = ($locationMain === 'wp_core' && $data['plugin_settings']['hide_core_files']);
	            ?>
                <div <?php if ($hideLocationMainArea) {
		            echo 'style="display: none;"';
	            } ?> class="wpacu-assets-collapsible-wrap wpacu-by-location wpacu-<?php echo $locationMain; ?>">
                <a class="wpacu-assets-collapsible <?php if ($listAreaStatus !== 'contracted') { ?>wpacu-assets-collapsible-active<?php } ?>"
                   href="#wpacu-assets-collapsible-content-<?php echo $locationMain; ?>">
		            <?php _e($locationsText[$locationMain], 'wp-asset-clean-up'); ?>
                </a>

                <div class="wpacu-assets-collapsible-content <?php if ($listAreaStatus !== 'contracted') { ?>wpacu-open<?php } ?>">
	            <?php if ($locationMain === 'external') { ?>
                    <p class="wpacu-assets-note"><strong>Note:</strong> External .css and .js assets are considered
                        those who are hosted on a different domain (e.g. Google Font API, assets loaded from external
                        CDNs) and the ones outside the WordPress "plugins" (usually /wp-content/plugins/), "themes"
                        (usually /wp-content/themes/) and "uploads" (usually /wp-content/uploads/) directories.</p>
	            <?php
		            // WP Core CSS/JS list is visible
	            } elseif ($locationMain === 'wp_core' && ! $data['plugin_settings']['hide_core_files']) { ?>
                    <p class="wpacu-assets-note"><span style="color: red;" class="dashicons dashicons-warning"></span> <strong>Warning:</strong> Please be careful when doing any changes to the
                        following core assets as they can break the functionality of the front-end website. If you're
                        not sure about unloading any asset, just leave it loaded.</p>
	            <?php
                    // WP Core CSS/JS list is hidden
	            } elseif ($locationMain === 'wp_core' && $data['plugin_settings']['hide_core_files']) {
	                ?>
                    <p class="wpacu-assets-note"><strong>Note:</strong> By default, <?php echo WPACU_PLUGIN_TITLE; ?> does not show the list of CSS/JS loaded from the WordPress core. Usually, WordPress core files are loaded for a reason and this setting was applied to prevent accidental unload of files that could be needed (e.g. jQuery library, Underscore library etc.).</p>
                    <p class="wpacu-assets-note"><span class="dashicons dashicons-info"></span> If you believe that you do not need some of the loaded core files (e.g. WordPress Gutenberg styling - Handle: 'wp-block-library') and you want to manage the files loaded from <em>/wp-includes/</em>, you can go to the plugin's <strong>"Settings"</strong>, click on the <strong>"Plugin Usage Preferences"</strong> tab, scroll to <strong>"Hide WordPress Core Files From The Assets List?"</strong> and make sure the option <strong>is turned off</strong>.</p>
                    <?php
                } elseif ($locationMain === 'uploads') { ?>
                    <p class="wpacu-assets-note" style="padding: 15px 15px 0 0;"><strong>Note:</strong> These are the CSS/JS files load from the /wp-content/uploads/ WordPress directory. They were copied there by other plugins or developers working on the website. In case the file was detected to be generated by a specific plugin through various verification patterns (e.g. for plugins such as Elementor, Oxygen Builder etc.), then it will be not listed here, but in the "From Plugins (.css &amp; .js)" area for the detected plugin. This is to have all the files related to a plugin organised in one place.</p>
                    <?php } ?>

	                <?php
	                if ($totalLocationAssets > 0) {
	                    $locI = 1;

                        foreach ($values as $locationChild => $values2) { ?>
                            <?php if ($locationChild !== 'none') {
                            if ($locationMain === 'plugins') {
                                $locationChildText = \WpAssetCleanUp\Info::getPluginInfo($locationChild, $allPlugins, $allActivePluginsIcons);
                            } elseif ($locationMain === 'themes') {
	                            $locationChildThemeArray = \WpAssetCleanUp\Info::getThemeInfo($locationChild, $allThemes);
	                            $locationChildText = $locationChildThemeArray['output'];
                            } else {
                                $locationChildText = $locationChild;
                            }
                            ?>
                            <div <?php if ($locationMain === 'plugins') { echo ' data-wpacu-plugin="' . $locationChild . '" '; } ?> class="wpacu-location-child-area <?php if ($locI === 1) { echo ' wpacu-location-child-area-first '; } ?> wpacu-area-expanded">
                                <div class="wpacu-area-title <?php if ($locationMain === 'themes' && $locationChildThemeArray['has_icon'] === true) { echo 'wpacu-theme-has-icon'; } ?>">
                                    <?php echo $locationChildText; ?>

                                    <?php if ($locationMain === 'plugins') { ?>
                                        <div class="wpacu-plugin-toggle-all">
                                            <ul>
                                                <li>"<?php _e('Unload on this page', 'wp-asset-clean-up'); ?>"</li>
                                                <li>
                                                    <a class="wpacu-plugin-check-all"
                                                       data-wpacu-plugin="<?php echo $locationChild; ?>" href="#"><?php _e('Check All', 'wp-asset-clean-up'); ?></a>
                                                    |
                                                    <a class="wpacu-plugin-uncheck-all"
                                                       data-wpacu-plugin="<?php echo $locationChild; ?>" href="#"><?php _e('Uncheck All', 'wp-asset-clean-up'); ?></a>
                                                </li>
                                            </ul>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                            <table <?php
                            if ($locationMain === 'plugins') {
                                echo 'data-wpacu-plugin="' . $locationChild . '" ';
                            }

                            if ($hideListOfAssetsOnly) {
                                echo ' style="display: none;"';
                            }
                            ?>
                                    class="wpacu_list_table wpacu_list_by_location wpacu_widefat wpacu_striped">
                                <tbody>
                                <?php
                                if ($locationMain === 'plugins') {
	                                do_action('wpacu_assets_plugin_notice_table_row', $locationChild);
                                }

                                ksort($values2);

                                foreach ($values2 as $assetType => $assetRows) {
                                    foreach ($assetRows as $assetRow) {
                                        echo $assetRow . "\n";
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        <?php
	                        $locI++;
                        }
                    } else {
                        // There are no loaded CSS/JS
                        $showOxygenMsg = $locationMain === 'themes' && in_array('oxygen/functions.php', apply_filters('active_plugins', get_option('active_plugins')));

                        if ($showOxygenMsg) {
                        ?>
                            <div style="padding: 12px 0;">
                                <img style="height: 30px; vertical-align: bottom;" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIHdpZHRoPSIzODFweCIgaGVpZ2h0PSIzODVweCIgdmlld0JveD0iMCAwIDM4MSAzODUiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+ICAgICAgICA8dGl0bGU+VW50aXRsZWQgMzwvdGl0bGU+ICAgIDxkZXNjPkNyZWF0ZWQgd2l0aCBTa2V0Y2guPC9kZXNjPiAgICA8ZGVmcz4gICAgICAgIDxwb2x5Z29uIGlkPSJwYXRoLTEiIHBvaW50cz0iMC4wNiAzODQuOTQgMzgwLjgwNSAzODQuOTQgMzgwLjgwNSAwLjYyOCAwLjA2IDAuNjI4Ij48L3BvbHlnb24+ICAgIDwvZGVmcz4gICAgPGcgaWQ9IlBhZ2UtMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9IiNhMGE1YWEiIGZpbGwtcnVsZT0iZXZlbm9kZCI+ICAgICAgICA8ZyBpZD0iT3h5Z2VuLUljb24tQ01ZSyI+ICAgICAgICAgICAgPG1hc2sgaWQ9Im1hc2stMiIgZmlsbD0iI2EwYTVhYSI+ICAgICAgICAgICAgICAgIDx1c2UgeGxpbms6aHJlZj0iI3BhdGgtMSI+PC91c2U+ICAgICAgICAgICAgPC9tYXNrPiAgICAgICAgICAgIDxnIGlkPSJDbGlwLTIiPjwvZz4gICAgICAgICAgICA8cGF0aCBkPSJNMjk3LjUwOCwzNDkuNzQ4IEMyNzUuNDQzLDM0OS43NDggMjU3LjU1NiwzMzEuODYgMjU3LjU1NiwzMDkuNzk2IEMyNTcuNTU2LDI4Ny43MzEgMjc1LjQ0MywyNjkuODQ0IDI5Ny41MDgsMjY5Ljg0NCBDMzE5LjU3MywyNjkuODQ0IDMzNy40NiwyODcuNzMxIDMzNy40NiwzMDkuNzk2IEMzMzcuNDYsMzMxLjg2IDMxOS41NzMsMzQ5Ljc0OCAyOTcuNTA4LDM0OS43NDggTDI5Ny41MDgsMzQ5Ljc0OCBaIE0yMjIuMzA0LDMwOS43OTYgQzIyMi4zMDQsMzEyLjAzOSAyMjIuNDQ3LDMxNC4yNDcgMjIyLjYzOSwzMTYuNDQxIEMyMTIuMzMsMzE5LjA5MiAyMDEuNTI4LDMyMC41MDUgMTkwLjQwMywzMjAuNTA1IEMxMTkuMDEsMzIwLjUwNSA2MC45MjksMjYyLjQyMyA2MC45MjksMTkxLjAzMSBDNjAuOTI5LDExOS42MzggMTE5LjAxLDYxLjU1NyAxOTAuNDAzLDYxLjU1NyBDMjYxLjc5NCw2MS41NTcgMzE5Ljg3NywxMTkuNjM4IDMxOS44NzcsMTkxLjAzMSBDMzE5Ljg3NywyMDYuODMzIDMxNy4wMiwyMjEuOTc4IDMxMS44MTUsMjM1Ljk5IEMzMDcuMTc5LDIzNS4wOTcgMzAyLjQwNCwyMzQuNTkyIDI5Ny41MDgsMjM0LjU5MiBDMjU1Ljk3NCwyMzQuNTkyIDIyMi4zMDQsMjY4LjI2MiAyMjIuMzA0LDMwOS43OTYgTDIyMi4zMDQsMzA5Ljc5NiBaIE0zODAuODA1LDE5MS4wMzEgQzM4MC44MDUsODYuMDQyIDI5NS4zOTIsMC42MjggMTkwLjQwMywwLjYyOCBDODUuNDE0LDAuNjI4IDAsODYuMDQyIDAsMTkxLjAzMSBDMCwyOTYuMDIgODUuNDE0LDM4MS40MzMgMTkwLjQwMywzODEuNDMzIEMyMTIuNDk4LDM4MS40MzMgMjMzLjcwOCwzNzcuNjA5IDI1My40NTYsMzcwLjY1NyBDMjY1Ljg0NSwzNzkuNjQxIDI4MS4wMzQsMzg1IDI5Ny41MDgsMzg1IEMzMzkuMDQyLDM4NSAzNzIuNzEyLDM1MS4zMyAzNzIuNzEyLDMwOS43OTYgQzM3Mi43MTIsMjk2LjA5MiAzNjguOTg4LDI4My4yODMgMzYyLjU4NCwyNzIuMjE5IEMzNzQuMjUxLDI0Ny41NzUgMzgwLjgwNSwyMjAuMDU4IDM4MC44MDUsMTkxLjAzMSBMMzgwLjgwNSwxOTEuMDMxIFoiIGlkPSJGaWxsLTEiIGZpbGw9IiNhMGE1YWEiIG1hc2s9InVybCgjbWFzay0yKSI+PC9wYXRoPiAgICAgICAgPC9nPiAgICA8L2c+PC9zdmc+" alt="" />
                                &nbsp;You're using <a href="<?php echo admin_url('admin.php?page=ct_dashboard_page'); ?>" target="_blank"><span style="font-weight: 600; color: #6036ca;">Oxygen</span></a> to design your site, which disables the WordPress theme system. Thus, no assets related to the theme are loaded.
                            </div>
                        <?php } else { ?>
                            <div style="padding: 0 0 16px 16px;"><?php _e('There are no CSS/JS loaded from this location.', 'wp-asset-clean-up'); ?></div>
                        <?php } ?>
                        <?php
	                }
	                ?>
                    </div>
                </div>
                <?php
            }
        }
    }
/*
* -----------------------
* [END] BY EACH LOCATION
* -----------------------
*/

include '_inline_js.php';
