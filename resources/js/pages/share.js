
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
                reader.onload = function (e) {
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


$('#uploadForm').submit(function (e) {
    e.preventDefault(); // Prevent default form submission
    $('.alert').remove();
    var submitBtn = $("#storeButton");
    showButtonLoader(submitBtn);
    var file = $('#image-compressed')[0].files[0];
    if (!file) {
        $('.image-error').attr('hidden', false);
        $('.image-error').text('Please select image');
        hideButtonLoader(submitBtn);
        return false;
    }

    var progressBar = $('#progressContainer');
    progressBar.empty(); // Clear previous progress bars
    let fileContainer = $(`
                <div class="mb-4" id="file-container-0">
                    <p class="mb-0">Stage1: File Upload ${file.name}</p>
                    <div class="progress mt-2">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                            role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                            style="width: 0%" id="progress-bar-0"></div>
                    </div>
                    <p id="status-0" class="text-danger d-inline-block mt-1"></p>
                    <button class="btn btn-sm btn-warning retry-btn hidden-force" style="height:15px; width:auto; font-size:10px" data-index="0" style="font-size: 12px;">Retry</button>
                </div>
            `);
    progressBar.append(fileContainer);
    uploadFile(file, 0, submitBtn)
        .then((response) => {
            clearInterval($("#status-0").data("interval"));
            $("#progress-bar-0").removeClass("bg-success").addClass("bg-primary").text("Completed");
            $("#status-0").addClass('hidden-force');
            hideButtonLoader(submitBtn);
            $('#uploadForm')[0].reset();
            document.getElementById('thumbnails').innerHTML = '';
            const message = response.data?.message || 'Upload completed successfully';
            const $alert = $(`<div class="alert alert-success">${message}</div>`);
            $('#uploadForm').closest('.upload-container').prepend($alert);
            // setTimeout(() => {
            //     $alert.fadeOut(500, () => $alert.remove());
            // }, 5000);
        })
        .catch((error) => {
            hideButtonLoader(submitBtn);
            console.error("Upload failed:", error);
        });
});

async function uploadFile(file, index, submitBtn) {
    let formData = new FormData();
    formData.append('file', file);
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var eventId = $('#event_id').data('id');
    formData.append('_token', csrfToken);
    formData.append('user_name', $('#user_name').val());
    formData.append('description', $('#caption').val());
    formData.append('file_size', $('#file_size').val());
    formData.append('event_id', eventId);
    var url = document.getElementById('share-post-btn').dataset.url;

    $(`#file-container-${index} .start-btn`).remove(); // Remove "Start Upload" button

    try {
        const response = await axios.post(url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            onUploadProgress: function (progressEvent) {
                if (progressEvent.lengthComputable) {
                    const percent = Math.round((progressEvent.loaded / progressEvent.total) * 100);
                    $(`#progress-bar-${index}`).css("width", percent + "%").text(percent + "%");

                    if (percent === 100) {
                        showProcessingStatus(0);
                    }
                }
            },
            timeout: 30000
        });

        return response; // This will resolve the outer promise

    } catch (error) {
        throw error; // This will reject the outer promise
    }
}

function showProcessingStatus(index) {
    let statusText = $("#status-" + index);
    let dots = 0;
    statusText.removeClass('hidden-force');
    statusText.text("Stage2: Processing");

    let interval = setInterval(() => {
        dots = (dots + 1) % 4;
        statusText.text("Stage2: Processing" + ".".repeat(dots));
    }, 500);

    statusText.data("interval", interval); // Save interval to clear later
}

function showRetryButton(index) {
    $(`#file-container-${index} .retry-btn`).removeClass('hidden-force'); // Show Retry button
}

function showButtonLoader(submitBtn) {
    const spinner = document.getElementById('spinner');
    spinner.style.display = 'inline-block';
    submitBtn.disabled = true;
}

function hideButtonLoader(submitBtn) {
    const spinner = document.getElementById('spinner');
    spinner.style.display = 'none';
    submitBtn.disabled = false;
}

$('#image').on('change', function (e) {
    compressImages();
});

function compressImages() {
    const input = document.getElementById('image');
    const file = input.files[0];
    if (!file) return;
    const targetSizeKB = 500; // Target size in KB
    const dataTransfer = new DataTransfer(); // Holds all compressed files
    const fileSizeMB = file.size / (1024 * 1024);
    const fileSizeKB = file.size / 1024; // Convert bytes to KB
    let quality;
    if (fileSizeKB <= targetSizeKB) {
        quality = 1.0;
    } else {
        quality = Math.max(0.98, targetSizeKB / fileSizeKB);
    }
    new Compressor(file, {
        quality: quality,
        maxWidth: 1024, // Resize if needed
        maxHeight: 1024,
        success(result) {
            // Convert Blob to File
            const compressedFile = new File([result], file.name, {
                type: file.type,
                lastModified: Date.now(),
            });
            dataTransfer.items.add(compressedFile);
            document.getElementById('image-compressed').files = dataTransfer.files;
            document.getElementById('file_size').value = compressedFile.size;
        },
        error(err) {
            console.error("Compression error:", err);
        }
    });
}
