(( $ ) => {
  'use strict';

  $(document).on('click', '.trilbdev-wiki-searchmodal .open-search', (event) => {
    $(event.currentTarget).closest('.trilbdev-wiki-searchmodal').find('.overlay').show();
  });

  $(document).on('click', '.trilbdev-wiki-searchmodal .close', (event) => {
    $(event.currentTarget).closest('.overlay').hide();
  });
})(jQuery);
