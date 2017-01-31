<?php
namespace Flynt\Features\AdminComponentPreview;

use Flynt\Features\Components\Component;
use Flynt\Utils\Log;

define(__NAMESPACE__ . '\NS', __NAMESPACE__ . '\\');

// @codingStandardsIgnoreLine
function enqueueComponentScripts() {
  Component::addAsset('enqueue', [
    'type' => 'script',
    'name' => 'adminComponentPreview',
    'path' => 'Features/AdminComponentPreview/script.js'
  ]);

  // Component::addAsset('enqueue', [
  //   'type' => 'style',
  //   'name' => 'adminComponentPreview',
  //   'path' => 'Features/AdminComponentPreview/style.css'
  // ]);

  Component::addAsset('enqueue', [
    'type' => 'script',
    'name' => 'draggabilly',
    'path' => 'vendor/draggabilly.js'
  ]);

  // add data to the javascript
  $data = [
    'templateDirectoryUri' => get_template_directory_uri()
  ];
  wp_localize_script('adminComponentPreview', 'wpData', $data);
}

if (class_exists('acf')) {
  if (is_user_logged_in() || is_admin()) {
    if (is_admin()) {
      add_action('admin_enqueue_scripts', NS . 'enqueueComponentScripts');
      // add image to the flexible content component name
      add_filter('acf/fields/flexible_content/layout_title', function ($title, $field, $layout, $i) {
        $componentPath = "/Components/$layout[name]/";
        $componentPreviewDesktopPath = get_template_directory() . $componentPath . 'preview-desktop.jpg';
        $componentPreviewDesktopUrl = get_template_directory_uri() . $componentPath . 'preview-desktop.jpg';

        if (is_file($componentPreviewDesktopPath)) {
          $newTitle = '<span class="layout-component-preview">';
          $newTitle .= '<img src="' . $componentPreviewDesktopUrl . '" height="36px">';
          $newTitle .= '<span class="label">' . $title . '</span>';
          $newTitle .= '</span>';
          $title = $newTitle;
        }
        return $title;
      }, 11, 4);

      // adds buttons to collapse/expand components
      add_filter('acf/get_field_label', function ($label, $field) {
        if ($field['type'] === 'flexible_content') {
          $label .= '<span class="flexible-content-controls">';
          $label .= '<a class="acf-icon small -collapse collapse-all" title="collapse all"></a>';
          $label .= '<span class="-collapsed">';
          $label .= '<a class="acf-icon small -collapse expand-all" title="expand all"></a>';
          $label .= '</span></span>';
        }
        return $label;
      }, 10, 2);
    } else {
      add_action('wp_enqueue_scripts', function () {
        wp_enqueue_script('jquery');
      });
      add_action('wp_enqueue_scripts', NS . 'enqueueComponentScripts');
      // adds Component Previews button to admin bar on front-end when logged in
      add_action('admin_bar_menu', function ($wpAdminBar) {
        $title = 'Component Previews';
        $wpAdminBar->add_menu([
          'id' => 'toggleComponentPreviews',
          'title' => $title
        ]);
      });
    }
  }
}
