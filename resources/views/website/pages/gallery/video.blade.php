<div class="main-body">
    <!-- Video Section-->
    <div class="video-container">
        <!-- Video Player -->
        <div class="sticky-section">
            <div class="video-player">
                <img src="{{ asset('assets/website/gallery-assets/images/video-player.jpg') }}">
            </div>
        </div>
        <!-- Video List -->
        <div class="sticky-section">
            <div class="video-list">
                @foreach ($videos as $video)
                    <div class="video-item" onclick="">
                        <img src="{{ asset('assets/website/gallery-assets/images/samples/photo01.jpg') }}"
                            alt="Video Title">
                        <div>
                            <h4 class="video-item-title">{{ $video->file_name }}</h4>
                            <p class="video-item-duration">{{ $video->video_duration }}</p>
                            <p class="video-item-views">Views: 21</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div><!-- End Video Container -->

    <!-- Footer -->
    <div class="footer">
        <p class="footer-copywrite">Powered by UP EVENTS</p>
        <!-- Share Button -->
        <a href="{{ route('landing.share', ['year' => $year, 'month' => $month, 'customer' => $customer]) }}">
            <div class="share-button">
                <img class="share-button-image"
                    src="{{ asset('assets/website/gallery-assets/images/upload-icon.svg') }}" alt=""
                    width="26px" height="26px">
            </div>
        </a>
    </div>

</div>
