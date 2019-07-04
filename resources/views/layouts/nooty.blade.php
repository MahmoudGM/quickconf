<script>
     @if(Session::has('success_create_conf'))
        noty({
          type: 'success',
          layout: 'top',
          text: '{{ Session::get('success_create_conf') }}',
          animation: {
              open: {height: 'toggle'},
              close: {height: 'toggle'},
              easing: 'swing',
              speed: 500 // opening & closing animation speed
          }
        })
      @endif

      @if(Session::has('success_edit_conf'))
        noty({
          type: 'success',
          layout: 'top',
          text: '{{ Session::get('success_edit_conf') }}',
          animation: {
              open: {height: 'toggle'},
              close: {height: 'toggle'},
              easing: 'swing',
              speed: 500 // opening & closing animation speed
          }
        })
      @endif

      @if(Session::has('success_edit_conf_sub'))
        noty({
          type: 'success',
          layout: 'top',
          text: '{{ Session::get('success_edit_conf_sub') }}',
          animation: {
              open: {height: 'toggle'},
              close: {height: 'toggle'},
              easing: 'swing',
              speed: 500 // opening & closing animation speed
          }
        })
      @endif
      
      @if(Session::has('success_add_message'))
        noty({
          type: 'success',
          layout: 'top',
          text: '{{ Session::get('success_add_message') }}',
          timeout: 2000,
          animation: {
              open: {height: 'toggle'},
              close: {height: 'toggle'},
              easing: 'swing',
              speed: 500 // opening & closing animation speed
          }
        })
      @endif

      @if(Session::has('success_remove_message'))
        noty({
          type: 'success',
          layout: 'top',
          text: '{{ Session::get('success_remove_message') }}',
          timeout: 2000,
          animation: {
              open: {height: 'toggle'},
              close: {height: 'toggle'},
              easing: 'swing',
              speed: 500 // opening & closing animation speed
          }
        })
      @endif
    </script>