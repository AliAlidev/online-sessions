<div class="main-body gallery-section">
    <div class="gallery" id="gallery">
        @foreach ($images as $image)
            <div class="grid-item" style="grid-row-end: span 20;">
                <a href="{{ asset($image->file) }}" data-fancybox="gallery"
                    data-caption="John Doe<br>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.">
                    <img class="lazy" alt=""
                        data-src="{{ asset($image->file) }}">
                </a>
            </div>
        @endforeach
    </div><!-- End Gallery -->
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
</div><!-- End Main Body -->

