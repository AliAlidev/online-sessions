<style>
.sticky-section.sticky-section-player {
    height: 350px !important;
    position: relative; /* Not sticky on desktop */
    overflow: hidden;
}
@media (max-width: 991px) {
    .sticky-section.sticky-section-player {
        height: 35vw !important;
    }
}

@media (max-width: 768px) {
    .sticky-section.sticky-section-player {
        height: 53vw !important;
        position: sticky;
        top: 0;
        
    }
}
.video-player {
    width: 100%;
    height: 100%;
    position: relative;
}

#videoIframe {
    position: absolute;
    top: 50%;
    left: 50%;
    min-width: 100%;
    min-height: 100%;
    transform: translate(-50%, -50%);
    border-radius: 5px;
    background-color: #000;
    pointer-events: auto;
}
.video-item:first-of-type {
    background-color: rgba(184, 145, 72, 0.2); /* Change to your preferred color */
}
.video-item {
    padding: 4px 4px 4px 0;
}
.video-item.active {
    background-color: rgba(184, 145, 72, 0.2); /* Change to your preferred color */
    border-radius: 6px;
}
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
    document.addEventListener("click", function (e) {
        const target = e.target.closest('.video-item');
        if (target) {
            document.querySelectorAll('.video-item').forEach(el => el.classList.remove('active'));
            target.classList.add('active');
        }
    });
</script>



