<div
    x-data="{
        filePath: @entangle('data.file_path'),
        preview: null,
        uploading: false,
        error: null,
        selectFile(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.error = null;
            this.uploading = true;
            this.preview = URL.createObjectURL(file);
            const form = new FormData();
            form.append('file', file);
            form.append('_token', document.querySelector('meta[name=csrf-token]').content);
            fetch('/admin/media-upload', { method: 'POST', body: form })
                .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
                .then(data => { this.filePath = data.path; this.uploading = false; })
                .catch(err => { this.error = 'Upload fehlgeschlagen (' + err.message + ')'; this.uploading = false; this.preview = null; });
        },
        clear() {
            this.filePath = null;
            this.preview = null;
            this.error = null;
            this.$refs.input.value = '';
        }
    }"
    class="space-y-2"
>
    {{-- Existing stored file preview --}}
    <template x-if="filePath && !preview">
        <div class="flex items-center gap-3">
            <img :src="'/storage/' + filePath" class="w-52 h-auto rounded object-contain border border-gray-200 dark:border-gray-700" alt="">
            <div class="text-sm text-gray-600 dark:text-gray-400" x-text="filePath"></div>
            <button type="button" @click="clear()" class="text-danger-600 hover:text-danger-700 text-sm font-medium">Entfernen</button>
        </div>
    </template>

    {{-- Live preview after selecting a new file --}}
    <template x-if="preview">
        <div class="flex items-center gap-3">
            <img :src="preview" class="w-52 h-auto rounded object-contain border border-gray-200 dark:border-gray-700" alt="">
            <span x-show="uploading" class="text-sm text-gray-500 dark:text-gray-400">Wird gespeichert…</span>
            <span x-show="!uploading && filePath" class="text-sm text-success-600">Bereit zum Speichern</span>
            <button type="button" @click="clear()" class="text-danger-600 hover:text-danger-700 text-sm font-medium">Entfernen</button>
        </div>
    </template>

    {{-- Error --}}
    <p x-show="error" x-text="error" class="text-sm text-danger-600"></p>

    {{-- Native file input --}}
    <input
        x-ref="input"
        type="file"
        accept="image/*"
        @change="selectFile($event)"
        :disabled="uploading"
        class="block w-full text-sm text-gray-700 dark:text-gray-300
               file:mr-3 file:py-1.5 file:px-3
               file:rounded file:border-0
               file:text-sm file:font-medium
               file:bg-primary-50 file:text-primary-700
               dark:file:bg-primary-900/30 dark:file:text-primary-400
               hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50
               cursor-pointer"
    >
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Erlaubt: JPEG, PNG, WebP, GIF · max. 10 MB</p>
</div>
