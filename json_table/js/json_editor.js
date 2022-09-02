(function ($, Drupal, drupalSettings) {
  'use strict';

  function parseJson(string) {
    try {
      return JSON.parse(string);
    }
    catch (e) {
      return null;
    }
  }

  /**
   * Attach behavior for JSON Fields.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.json_editor = {
    attach(context) {
      $(context)
        .find('.json-editor')
        .once('json-editor')
        .each(function (index, element) {
          let $textarea = $(this);
          let mode = $(this).data('json-editor');
          let data = parseJson($textarea.val());
          let id = 'json-editor-' + $textarea.attr('name');
          let $editor = $(Drupal.theme('jsonEditorWrapper', id));
          $(this).addClass('js-hide').after($editor);
          const container = document.getElementById(id);
          const editor = new JSONEditor(container, {
            mode: mode,
            modes: ['code', 'form', 'text', 'tree'],
            onChange: function () {
              $textarea.text(editor.getText());
            }
          },data);
        });
    }
  };

  Drupal.theme.jsonEditorWrapper = function (id) {
    return '<div style="width:100%;height:500px" id="' + id + '"></div>';
  }

})(jQuery, Drupal, drupalSettings);
