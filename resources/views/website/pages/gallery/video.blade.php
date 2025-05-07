<div class="main-body">
    <a data-id= "{{ 'folder-' . $folder->id }}" class="refresh-button">
        <svg class="reload-icon" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M14.66 15.66A8 8 0 1 1 17 10h-2a6 6 0 1 0-1.76 4.24l1.42 1.42zM12 10h8l-4 4-4-4z"
                fill="currentColor" />
        </svg>
        Reload
    </a>
    <!-- Video Section-->
    <div class="video-container">
        <!-- Video Player -->
        <div class="sticky-section">
            <div class="video-player" style="{{ count($videos) == 0 ? 'display:none' : '' }}">
                <iframe id="videoIframe" width="700" height="350" frameborder="0" allow="autoplay" allowfullscreen
                    webkitallowfullscreen mozallowfullscreen></iframe>
            </div>
        </div>
        <!-- Video List -->
        <div class="sticky-section">
            <div class="video-list">
                @foreach ($videos as $video)
                    <div class="video-item" data-url = "{{ $video->file }}"
                        data-increment-view="{{ route('landing.increase_view') . '/' . $video->id }}"
                        data-video-id="{{ $video->id }}">
                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->file_name }}">
                        <div>
                            <h4>{{ $video->video_name }}</h4>
                            <p>Duration: {{ $video->video_duration }}</p>
                            <p id="{{ 'video-' . $video->id }}">Views: {{ $video->view_count }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div><!-- End Video Container -->
</div>
