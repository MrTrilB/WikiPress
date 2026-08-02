(function($){
  $(function(){
    // simple anchors fix: if link points to missing id, try to find heading by text
    $(document).on('click', 'a[href^="#"]', function(){
      var id = this.getAttribute('href').slice(1);
      if (!id) return;
      var el = document.getElementById(id);
      if (!el){
        var heading = $("h2,h3,h4,h5,h6").filter(function(){return $(this).text().trim().toLowerCase()===id.replace(/-/g,' ').toLowerCase()}).get(0);
        if (heading){ heading.setAttribute('id', id); }
      }
    });
    // collapsible category tree support
    $('.trilbdev-cat-tree .toggle').on('click', function(){
      $(this).closest('li').toggleClass('open');
    });
    // simple feedback AJAX
    $(document).on('click', '.trilbdev-feedback button', function(){
      var $btn = $(this), v = $btn.data('value');
      var $wrap = $btn.closest('.trilbdev-feedback');
      $.post(trilbdevDocs.ajax, {action:'trilbdev_docs_feedback', nonce: trilbdevDocs.nonce, post: trilbdevDocs.post, value: v}, function(r){
        $wrap.addClass('done').find('.thanks').show();
      });
    });
    // search form submit
    $(document).on('submit', '.trilbdev-docs-searchbox form', function(e){
      // allow normal submit; enhance later if needed
    });
    // live search suggestions
    var debounce = function(fn, wait){ var t; return function(){ var ctx=this, args=arguments; clearTimeout(t); t=setTimeout(function(){ fn.apply(ctx,args); }, wait||150); }; };
    $(document).on('input', '.trilbdev-docs-searchbox input[type="search"]', debounce(function(){
      var $input = $(this);
      var q = $input.val().trim();
      var $box = $input.closest('.trilbdev-docs-searchbox');
      var $sug = $box.find('.trilbdev-docs-suggestions');
      if (!q){ $sug.empty().hide(); return; }
      $.getJSON(trilbdevDocs.ajax, { action:'trilbdev_docs_suggest', q:q }, function(resp){
        if (!resp || !resp.success) { $sug.empty().hide(); return; }
        var items = resp.data.items || [];
        if (!items.length){ $sug.empty().hide(); return; }
        var html = '<ul class="suggest-list">' + items.map(function(it){
          var t = $('<div>').text(it.title||'').html();
          var e = $('<div>').text(it.excerpt||'').html();
          return '<li><a href="'+it.link+'"><span class="title">'+t+'</span><span class="excerpt">'+e+'</span></a></li>';
        }).join('') + '</ul>';
        $sug.html(html).show();
      });
    }, 200));
    // hide suggestions on blur/click outside
    $(document).on('click', function(e){
      var $t = $(e.target);
      if (!$t.closest('.trilbdev-docs-searchbox').length){ $('.trilbdev-docs-suggestions').empty().hide(); }
    });
    // reactions
    $(document).on('click', '.trilbdev-reactions .react', function(){
      var r = $(this).data('reaction');
      var $count = $(this).find('.count');
      $.post(trilbdevDocs.ajax, {action:'trilbdev_docs_reaction', nonce: trilbdevDocs.nonce, post: trilbdevDocs.post, reaction:r}, function(resp){
        if (resp && resp.success && resp.data && typeof resp.data.count !== 'undefined') {
          $count.text(resp.data.count);
        }
      });
    });
    // search modal open/close
    $(document).on('click', '.trilbdev-docs-searchmodal .open-search', function(){
      $(this).closest('.trilbdev-docs-searchmodal').find('.overlay').show();
    });
    $(document).on('click', '.trilbdev-docs-searchmodal .close', function(){
      $(this).closest('.overlay').hide();
    });

    // reading progress bar
    var $prog = $('.trilbdev-reading-progress .bar');
    if ($prog.length) {
      var $content = $('.entry-content');
      var update = function(){
        var wh = window.innerHeight || document.documentElement.clientHeight;
        var rect = $content.get(0) ? $content.get(0).getBoundingClientRect() : null;
        if (!rect) return;
        var total = rect.height - wh;
        var done = wh - rect.top;
        var pct = Math.max(0, Math.min(100, Math.round((done/Math.max(1,total))*100)));
        $prog.css('width', pct + '%');
      };
      update();
      $(window).on('scroll resize', update);
    }
  });
})(jQuery);
