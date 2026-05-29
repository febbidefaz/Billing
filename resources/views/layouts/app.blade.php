@extends('adminlte::page')

@section('content')
<div id="main-content">
    @yield('page-content')
</div>
@stop

@section('js')
<script>
$(document).on('click', '.menu-link', function(e) {
    e.preventDefault();

    let url = $(this).attr('href');

    $('#main-content').html(`
        <div class="p-4 text-center">
            <i class="fas fa-spinner fa-spin"></i> Memuat data...
        </div>
    `);

    $('#main-content').load(url + ' #main-content > *', function() {
        $('.nav-link').removeClass('active');
        $('a[href="' + url + '"]').addClass('active');
    });

    history.pushState(null, '', url);
});

window.onpopstate = function() {
    $('#main-content').load(location.href + ' #main-content > *');
};
</script>
@stop