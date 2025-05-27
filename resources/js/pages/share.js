import { getUserToken } from '../auth';

async function checkEventIfHavePassword() {
    var eventSlug = $('#global-event-data').val();
    var galleryUrl = $('#global-event-data').data('eventGalleryUrl');
    if ($('#global-event-data').data('eventHasP')) {
        if (localStorage.getItem(eventSlug)) {
            var token = await getUserToken();
            axios.post(galleryUrl, {
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                password: localStorage.getItem(eventSlug)
            }, {
                headers: {
                    'pageToken': token
                }
            })
                .then(response => {
                    if (response.data.success) {
                        setTimeout(() => {
                            document.querySelector('.main-container').classList.remove('auth-checking');
                            removeGalleryMainLoader()
                        }, 5);
                    } else {
                        gotoPasswordVerification();
                    }
                }).catch(error => {
                    gotoPasswordVerification();
                });
        } else {
            gotoPasswordVerification();
        }
    } else {
        setTimeout(() => {
            document.querySelector('.main-container').classList.remove('auth-checking');
            removeGalleryMainLoader()
        }, 5);
    }
}

async function checkAuthentication() {
    const token = await getUserToken();
    try {
        const response = await fetch('/events/check-token', {
            method: 'GET',
            headers: {
                'pageToken': token
            }
        });
        if (!response.ok) throw new Error('Unauthorized');
        checkEventIfHavePassword();
    } catch (error) {
        window.location.href = document.getElementById('main-page-url')?.dataset?.url || '/';
    }
}

checkAuthentication();

function removeGalleryMainLoader() {
    const loader = document.getElementById('page-loader');
    if (loader) {
        loader.style.display = 'none';
    }
}

async function gotoPasswordVerification() {
    var url = $('#global-event-data').data('url');
    var token = await getUserToken();
    axios.get(url, {
        headers: {
            'pageToken': token
        }
    })
        .then(response => {
            window.location.href = response.data.url;
        }).catch(error => {
            console.log(error);
        });
}

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


function closeModal() {
    const overlay = document.getElementById('overlay');
    overlay.classList.remove('show');
    overlay.addEventListener('transitionend', () => {
        overlay.classList.add('hidden');
    }, {
        once: true
    });
}

document.getElementById('closeModalBtn').addEventListener('click', closeModal);
document.getElementById('closeXBtn').addEventListener('click', closeModal);

$('#uploadForm').submit(function (e) {

    e.preventDefault(); // Prevent default form submission
    $('.alert').remove();
    $('.image-error').attr('hidden', true);
    var submitBtn = $("#storeButton");
    showButtonLoader(submitBtn);
    var file = $('#image-compressed')[0].files[0];
    if (!file) {
        $('.image-error').attr('hidden', false);
        $('.image-error').text('Please select image');
        hideButtonLoader(submitBtn);
        return false;
    }

    var progressBar = $('#uploadProgressModal #progressContainer');
    /// open progress modal
    const overlay = document.getElementById('overlay');
    overlay.classList.remove('hidden');
    setTimeout(() => overlay.classList.add('show'), 10);

    progressBar.empty(); // Clear previous progress bars
    let fileContainer = $(`
                <div class="mb-4" id="file-container-0" style="margin-top:10px">
                    <p class="mb-0">Stage1: File Upload ${file.name}</p>
                    <div class="progress" style="margin-top:10px">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                            role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                            style="width: 0%;" id="progress-bar-0"></div>
                    </div>
                    <div class='d-inline-block'>
                        <p id="status-0" class="text-danger mt-3" style="text-align:start; margin-top:10px"></p>
                        <button class="btn btn-sm btn-warning retry-btn hidden-force mt-3" style="height:15px; width:auto; font-size:12px; margin-top:10px; text-align:start" data-index="0">Retry</button>
                    </div>
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
            const $alert = $(`<div class="alert alert-success guest-upload-success" style=""><i class="fa fa-check" aria-hidden="true"></i>${message}</div>`);
            $('#uploadProgressModal').prepend($alert);
            $('#uploadProgressModalLabel').attr('hidden', true);
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
            timeout: 999000
        });

        return response; // This will resolve the outer promise

    } catch (error) {
        let statusText = $("#status-" + index);
        statusText.text("Stage2: Failed");
        clearInterval(statusText.data("interval"));
        showRetryButton(0);
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

$(document).on("click", ".retry-btn", function (e) {
    e.preventDefault();
    $(this).addClass('hidden-force');
    var file = $('#image-compressed')[0].files[0];

    // Reset progress bar and status text
    $("#progress-bar-0")
        .css("width", "0%")
        .removeClass("bg-danger")
        .addClass("bg-success")
        .text("0%");

    $("#status-0").text("Stage2: Retrying...");

    // Retry uploading the file using the existing progress bar
    uploadFile(file, 0);
});

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

async function compressImages(formId) {
    const input = document.getElementById('image');
    const fileInput = input.files[0];
    if (!fileInput) return;

    let compressionRatios = [];
    try {
        var filePath = document.getElementById('compression-ratios-file-path');
        const response = await fetch(filePath.value);
        if (!response.ok) {
            throw new Error(`Failed to fetch compression ratios: ${response.status}`);
        }
        compressionRatios = await response.json();
    } catch (error) {
        console.error('Error loading compression ratios:', error);
        // Fallback to default ratios
        compressionRatios = [
            { minSizeMB: 10, quality: 0.7 },
            { minSizeMB: 9, quality: 0.75 },
            { minSizeMB: 8, quality: 0.8 },
            { minSizeMB: 7, quality: 0.85 },
            { minSizeMB: 6, quality: 0.9 },
            { minSizeMB: 5, quality: 0.91 },
            { minSizeMB: 4, quality: 0.92 },
            { minSizeMB: 3, quality: 0.93 },
            { minSizeMB: 2, quality: 0.94 },
            { minSizeMB: 1, quality: 0.95 },
            { minSizeMB: 0.5, quality: 0.99 }
        ];
    }

    const dataTransfer = new DataTransfer(); // Holds all final files
    const fileSizeMB = fileInput.size / (1024 * 1024); // Convert bytes to MB
    let quality = null;
    for (const ratio of compressionRatios) {
        if (fileSizeMB > ratio.minSizeMB) {
            quality = Math.max(ratio.quality, 0.7); // Ensure minimum quality of 0.7
            break;
        }
    }

    if (quality === null) {
        dataTransfer.items.add(fileInput);
        document.getElementById('image-compressed').files = dataTransfer.files;
        document.getElementById('file_size').value = fileInput.size;
    } else {
        new Compressor(fileInput, {
            quality: quality,
            maxWidth: 1920,
            maxHeight: 1920,
            success(result) {
                const compressedFile = new File([result], fileInput.name, {
                    type: fileInput.type,
                    lastModified: Date.now()
                });
                dataTransfer.items.add(compressedFile);
                document.getElementById('image-compressed').files = dataTransfer.files;
                document.getElementById('file_size').value = compressedFile.size;
            },
            error(err) {
                console.error(`Compression error for ${fileInput.name}:`, err);
            }
        });
    }
}
