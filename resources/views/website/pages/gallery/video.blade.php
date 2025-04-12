<div class="main-body">
    <!-- Video Section-->
    <div class="video-container">
        <!-- Video Player -->
        <div class="sticky-section">
            <div class="video-player" style="{{ count($videos) == 0 ? 'display:none' : '' }}">
                <iframe id="videoIframe" width="640" height="450" frameborder="0" allow="autoplay" allowfullscreen
                    webkitallowfullscreen mozallowfullscreen></iframe>
            </div>
        </div>
        <!-- Video List -->
        <div class="sticky-section">
            <div class="video-list">
                @foreach ($videos as $video)
                    <div class="video-item" data-url = "{{ $video->file }}">
                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->file_name }}">
                        <div>
                            <h4>{{ $video->file_name }}</h4>
                            <p>Duration: {{ $video->video_duration }}</p>
                            <p>Views: {{ $video->view_count }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div><!-- End Video Container -->
</div>
