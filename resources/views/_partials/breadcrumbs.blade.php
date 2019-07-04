@if ($breadcrumbs)
    <div class="breadcrumb ui">
        @foreach ($breadcrumbs as $breadcrumb)
            @if (!$breadcrumb->last)
                <a href="{{ $breadcrumb->url }}"  class="section">{{ $breadcrumb->title }}</a>
                <i class="right angle icon divider"></i>
            @else
                <div class="active section">{{ $breadcrumb->title }}</div>
            @endif
        @endforeach
    </div>
@endif
