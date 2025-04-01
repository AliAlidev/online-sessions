<div class="main-body gallery-section">
    <div class="gallery" id="gallery">
        @foreach ($images as $image)
            <div class="grid-item" style="grid-row-end: span 20;">
                <a href="{{ asset($image->file) }}" data-fancybox="gallery"
                    data-caption="{{ $image->description }}">
                    <img class="lazy" alt="" data-src="{{ asset($image->file) }}">
                </a>
            </div>
        @endforeach
    </div><!-- End Gallery -->
</div><!-- End Main Body -->

