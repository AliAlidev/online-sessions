@extends('website.layout.app')

@push('styles')
{{-- <link rel="stylesheet" href="{{ asset('assets/website/share-assets/css/style-light.css') }}" /> --}}
    <link rel="stylesheet" href="{{ asset('assets/website/share-assets/css/style-dark.css') }}" />
@endpush

@section('content')
    <div class="main-container">
        <div class="main-header">
            <div class="top-bar">
                <a href="{{ route('landing.index', ['year' => $year, 'month' => $month, 'customer' => $customer]) }}"><img class="top-bar-iamge" src="{{ asset($event->cover_image) }}"
                        alt=""></a>
                <div class="event-title-date">
                    <h1 class="event-title">{{ $event->event_name }}</h1>
                    <p class="event-date">{{ $event->start_date }}</p>
                </div>
            </div>
        </div>
        <div class="main-body">
            <div class="main-body-upload">
                <div class="upload-container">
                    <h1 class="share-memory-with-us">Share a memory with us</h1>
                    <form action="" method="post" enctype="multipart/form-data" id="uploadForm">
                        <div class="drop-area" id="dropArea">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Select image to upload</p>
                            <input type="file" name="image[]" id="image" accept="image/*" required>
                        </div>
                        <div class="thumbnails" id="thumbnails"></div><br>
                        </select>
                        <!-- Label for Enter User Name field -->
                        <label for="user_name" class="user_name_label">Enter your name (Optional)</label>
                        <input type="text" name="user_name" id="user_name" placeholder="Enter your name"><br>
                        <!-- Label for Enter Caption field -->
                        <label for="caption" class="special_message_label">Share a special message with your photo!
                            (Optional)</label>
                        <!-- Changed input to textarea for multi-line caption input -->
                        <textarea name="caption" id="caption" placeholder="Enter a caption" rows="4" cols="50"></textarea><br>
                        <input type="submit" name="submit" value="Upload">
                    </form>
                    <a class="back-button" href="{{ route('landing.gallery', ['year' => $year, 'month' => $month, 'customer' => $customer]) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                            viewBox="0 0 16 16" style="vertical-align: middle; margin-right: 5px;">
                            <path fill-rule="evenodd"
                                d="M15 8a.5.5 0 0 1-.5.5H3.707l4.147 4.146a.5.5 0 0 1-.708.708l-5-5a.5.5 0 0 1 0-.708l5-5a.5.5 0 1 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z" />
                        </svg>
                        Go to Gallery
                    </a>

                </div><!-- End upload-container -->
            </div><!-- End main-body-upload -->
        </div><!-- End Main Body -->

    </div><!-- End Main Container -->
    <!-- Footer -->
    <div class="footer">
        <p class="footer-copywrite">Powered by UP EVENTS</p>
    </div>
@endsection

@push('scripts')
    <script>
        // File Drop Field
        const dropArea = document.getElementById('dropArea');
        const fileInput = document.getElementById('image');
        const thumbnailsContainer = document.getElementById('thumbnails');
        dropArea.addEventListener('click', () => {
            fileInput.click();
        });
        fileInput.addEventListener('change', updateThumbnails);
        dropArea.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropArea.classList.add('dragover');
        });
        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('dragover');
        });
        dropArea.addEventListener('drop', (event) => {
            event.preventDefault();
            dropArea.classList.remove('dragover');
            fileInput.files = event.dataTransfer.files;
            updateThumbnails();
        });
        // Create Thumbnails
        function updateThumbnails() {
            const files = fileInput.files;
            thumbnailsContainer.innerHTML = '';
            if (files.length > 0) {
                thumbnailsContainer.style.marginTop = '15px';
                for (const file of files) {
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            thumbnailsContainer.appendChild(img);
                        }
                        reader.readAsDataURL(file);
                    }
                }
            } else {
                thumbnailsContainer.style.marginTop = '0';
            }
        }
    </script>
@endpush
