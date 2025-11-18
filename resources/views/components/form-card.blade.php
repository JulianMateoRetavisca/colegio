<div class="pro-card form-card">
    @if(!empty($title))
        <div class="pro-card-header d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">{{ $title }}</h2>
            {{ $headerExtra ?? '' }}
        </div>
    @endif
    <div class="pro-card-body">
        {{ $slot }}
    </div>
    @if(!empty($actions))
        <div class="pro-card-footer d-flex justify-content-end gap-2">
            {{ $actions }}
        </div>
    @endif
</div>