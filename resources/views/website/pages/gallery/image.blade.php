<div class="main-body gallery-section">
    <div class="gallery" id="gallery">
        @foreach ($images as $key => $image)
            <div class="grid-item" style="grid-row-end: span 10;">
                <a href="{{ asset($image->file) }}" data-fancybox="gallery"
                    data-can-delete="{{ $image->created_by == auth()->user()->id ? 'true' : 'false' }}"
                    data-image-id={{ $image->id }}
                    data-caption="<span style='font-size:18px;'>{{ $image->user_name }}</span><br>{{ $image->description }}">
                    <img class="lazy" alt="" data-src="{{ asset($image->file) }}">
                </a>
            </div>
        @endforeach
    </div><!-- End Gallery -->
</div><!-- End Main Body -->
