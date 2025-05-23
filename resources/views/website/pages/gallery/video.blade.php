<style>

</style>
<div class="main-body">
    <!-- Video Section-->
    <div class="video-container">
        <!-- Video Player -->
        <div class="sticky-section sticky-section-player">
            <div class="video-player" style="{{ count($videos) == 0 ? 'display:none' : '' }}"> 
                <iframe id="videoIframe" frameborder="0" allow="autoplay" allowfullscreen
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

<script>
    // Check periodically for the presence of video items
    const setFirstActive = setInterval(() => {
        const items = document.querySelectorAll('.video-item');
        if (items.length > 0) {
            // Set first item as active
            items[0].classList.add('active');
            clearInterval(setFirstActive); // Stop checking
        }
    }, 100); // Check every 100ms

    // Handle click to switch 'active' class
    document.addEventListener("click", function (e) {
        const target = e.target.closest('.video-item');
        if (target) {
            document.querySelectorAll('.video-item').forEach(el => el.classList.remove('active'));
            target.classList.add('active');
        }
    });
</script>



