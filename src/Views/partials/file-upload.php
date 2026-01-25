<?php
/**
 * File Upload Component Partial
 * Drag-and-drop file upload with preview
 *
 * Props:
 * - $entityId: Entity ID for upload endpoint
 * - $accept: Accepted file types (default: images)
 * - $multiple: Allow multiple files (default: true)
 * - $maxSize: Max file size in MB (default: 10)
 */

$accept = $accept ?? 'image/*';
$multiple = $multiple ?? true;
$maxSize = $maxSize ?? 10;
?>

<div
    x-data="{
        isDragging: false,
        files: [],
        uploading: false,
        uploadProgress: 0,
        handleFiles(fileList) {
            this.files = Array.from(fileList);
        },
        removeFile(index) {
            this.files.splice(index, 1);
        },
        async upload() {
            if (this.files.length === 0) return;

            this.uploading = true;
            this.uploadProgress = 0;

            const formData = new FormData();
            this.files.forEach(file => {
                formData.append('files[]', file);
            });

            try {
                // Simulate upload progress
                const interval = setInterval(() => {
                    if (this.uploadProgress < 90) {
                        this.uploadProgress += 10;
                    }
                }, 200);

                const response = await fetch('<?= url('/entities/' . ($entityId ?? '') . '/files') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-Token': '<?= csrf_token() ?>'
                    }
                });

                clearInterval(interval);
                this.uploadProgress = 100;

                if (response.ok) {
                    setTimeout(() => {
                        this.files = [];
                        this.uploading = false;
                        this.uploadProgress = 0;
                        location.reload();
                    }, 500);
                } else {
                    alert('Upload failed');
                    this.uploading = false;
                }
            } catch (error) {
                alert('Upload error: ' + error.message);
                this.uploading = false;
            }
        }
    }"
    class="space-y-4"
>
    <!-- Drop Zone -->
    <div
        @drop.prevent="isDragging = false; handleFiles($event.dataTransfer.files)"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        class="relative border-2 border-dashed transition-colors"
        :class="isDragging ? 'border-emerald-400 bg-emerald-400/10' : 'border-zinc-700 bg-zinc-900/50'"
    >
        <input
            type="file"
            id="file-upload-<?= $entityId ?? 'default' ?>"
            <?= $multiple ? 'multiple' : '' ?>
            accept="<?= e($accept) ?>"
            @change="handleFiles($event.target.files)"
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
        >

        <div class="p-12 text-center pointer-events-none">
            <svg class="w-16 h-16 mx-auto mb-4 transition-colors" :class="isDragging ? 'text-emerald-400' : 'text-zinc-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>

            <p class="text-zinc-300 font-medium mb-2">
                <span x-show="!isDragging">Drop files here or click to browse</span>
                <span x-show="isDragging" class="text-emerald-400">Drop to upload</span>
            </p>
            <p class="text-zinc-600 text-sm">
                Max size: <?= $maxSize ?>MB per file
            </p>
        </div>
    </div>

    <!-- File Preview List -->
    <div x-show="files.length > 0" x-transition class="space-y-2" style="display: none;">
        <h4 class="text-sm font-semibold text-zinc-300">Selected Files</h4>

        <template x-for="(file, index) in files" :key="index">
            <div class="flex items-center gap-3 p-3 bg-zinc-900 border border-zinc-800">
                <!-- File Icon/Preview -->
                <div class="flex-shrink-0 w-10 h-10 bg-zinc-800 border border-zinc-700 flex items-center justify-center overflow-hidden">
                    <template x-if="file.type.startsWith('image/')">
                        <img
                            :src="URL.createObjectURL(file)"
                            :alt="file.name"
                            class="w-full h-full object-cover"
                        >
                    </template>
                    <template x-if="!file.type.startsWith('image/')">
                        <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </template>
                </div>

                <!-- File Info -->
                <div class="flex-1 min-w-0">
                    <p class="text-zinc-200 text-sm font-medium truncate" x-text="file.name"></p>
                    <p class="text-zinc-600 text-xs" x-text="(file.size / 1024).toFixed(1) + ' KB'"></p>
                </div>

                <!-- Remove Button -->
                <button
                    type="button"
                    @click="removeFile(index)"
                    class="text-red-400 hover:text-red-300"
                    :disabled="uploading"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </template>
    </div>

    <!-- Upload Progress -->
    <div x-show="uploading" x-transition class="space-y-2" style="display: none;">
        <div class="flex items-center justify-between text-sm">
            <span class="text-zinc-400">Uploading...</span>
            <span class="text-emerald-400 font-medium" x-text="uploadProgress + '%'"></span>
        </div>
        <div class="h-2 bg-zinc-800 overflow-hidden">
            <div
                class="h-full bg-emerald-500 transition-all duration-300"
                :style="`width: ${uploadProgress}%`"
            ></div>
        </div>
    </div>

    <!-- Upload Button -->
    <div x-show="files.length > 0 && !uploading" x-transition style="display: none;">
        <button
            type="button"
            @click="upload()"
            class="w-full px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-zinc-900 font-bold transition-colors"
        >
            Upload <span x-text="files.length"></span> File<span x-show="files.length > 1">s</span>
        </button>
    </div>
</div>
