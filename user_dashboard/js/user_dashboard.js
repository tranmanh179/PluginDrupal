(function ($, drupalSettings, once) {
  Drupal.behaviors.user_dashboard = {
    attach: function (context, settings) {
      $("#dashboard", context).once("dashboard-process").each(function () {
        $('.customize #load-disabled-blocks').click(function (e) {
          e.preventDefault();
          //send the blocks to our default blocks callback
          $('#dashboard .canvas .canvas-content').removeClass('hidden');
          $('#dashboard .btn-close.hidden').removeClass('hidden');
          $.get(drupalSettings.dashboard.customize, function (data) {
            $('#dashboard .canvas .canvas-content .disabled-blocks').html(data);
            $(".user-dashboard-region .region").addClass('ui-sortable');
            draggableInit();
            close();
            collapse();
          });
        });
        function draggableInit() {
          let blockId = '';
          $("#disabled-blocks [draggable=true]").bind("dragstart", function (event) {
            blockId = $(this).attr('id');
            let that = $(this);
            let urlBlockContent = drupalSettings.dashboard.blockContent + '/' + $(this).data('delta');

            $.get(urlBlockContent, function (data) {
              that.next().hide().html(data);
            });

            event.originalEvent.dataTransfer.setData("application/json", JSON.stringify({id: blockId, delta: $(this).data('delta')}));
          });

          $("#dashboard [droppable=true]").bind("dragover", function (event) {
            $(this).find('.region').addClass("bg-info");
            event.preventDefault();
          });
          $("#dashboard [droppable=true]").bind("dragleave", function () {
            $(this).find('.region').removeClass("bg-info");
          });
          $("#dashboard [droppable=true]").bind("drop", function (event) {
            let region_id = $(this).attr('id');
            if (!$(this).data('status')) {
              $(this).data('status', 'lock');
              let data = JSON.parse(event.originalEvent.dataTransfer.getData("application/json"));
              let elementId = data.id;
              let blockId = data.delta;
              let that = $(this);
              let spinners =
                '<div class="spinners d-flex justify-content-center"><div class="spinner-border" role="status">' +
                '<span class="sr-only">' + Drupal.t('Loading') + '…' + '</span>' +
                '</div></div>';

              $(this).find('.region').prepend(spinners);
              $.post(drupalSettings.dashboard.drawer,
                {region: region_id, blockId: blockId})
                .done(function (data) {
                  let bid = data.data.id;
                  let block = $(".disabled-blocks #" + elementId).data('bid', bid).hide().parent().detach();
                  that.find('.region').prepend(block);
                  that.find(".bg-info").removeClass("bg-info");
                  that.find(".spinners").remove();
                  that.find(".content").show();
                })
                .fail(function () {
                  Drupal.t("An error occurred during the update of the entity. Please consult the watchdog.");
                });

              that.data('status', '');
            }
          });
        }
        function close() {
          $('#dashboard .btn-close').on('click', function () {
            let bid = $(this).data('bid');
            let that = $(this);
            $.post(drupalSettings.dashboard.updatePath, {action: 'delete', bid: bid})
              .done(function (data) {
                that.closest('.card').remove();
              })
              .fail(function () {
                Drupal.t("An error occurred during the update of the entity. Please consult the watchdog.");
              });
          });
        }
        function collapse() {
          $('#dashboard .card-header').on('click', function () {
            let bid = $(this).data('bid');
            let status = $(this).closest('.card').attr('open');
            $.post(drupalSettings.dashboard.updatePath, {action: 'collapse', bid: bid, status: status})
              .fail(function () {
                Drupal.t("An error occurred during the update of the entity. Please consult the watchdog.");
              });
          });
        }
      });
    }
  };
})(jQuery, drupalSettings, once);
