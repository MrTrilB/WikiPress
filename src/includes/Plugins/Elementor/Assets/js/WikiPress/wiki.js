(( $ ) => {
  'use strict';

    $(document).on('click', '.wikipress-wiki-searchmodal .open-search', (event) => {
      $(event.currentTarget).closest('.wikipress-wiki-searchmodal').find('.overlay').show();
  });

  $(document).on('click', '.wikipress-wiki-searchmodal .close', (event) => {
    $(event.currentTarget).closest('.overlay').hide();
  });
})(jQuery);
