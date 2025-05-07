<div class="main-body gallery-section">
    <a data-id= "{{ 'folder-' . $folder->id }}" class="refresh-button">
        <svg class="reload-icon" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M14.66 15.66A8 8 0 1 1 17 10h-2a6 6 0 1 0-1.76 4.24l1.42 1.42zM12 10h8l-4 4-4-4z"
                fill="currentColor" />
        </svg>
        Reload
    </a>
    <div class="gallery" id="gallery">
        @foreach ($images as $key => $image)
            <div class="grid-item" style="grid-row-end: span 10;">
                <a href="{{ asset($image->file) }}" data-fancybox="gallery"
                    data-can-delete="{{ $image->created_by == auth()->user()->id ? 'true' : 'false' }}"
                    data-image-id={{ $image->id }}
                    data-caption="<strong>{{ $image->user_name }}</strong><br>{{ $image->description }}">
                    <img class="lazy" alt="" data-src="{{ asset($image->file) }}">
                </a>
            </div>
        @endforeach
    </div><!-- End Gallery -->
</div><!-- End Main Body -->
