{{--
    Shared FilePond upload hardening for mobile (Android) uploads.

    Problem: on some phones a picked photo is still being written / synced
    (fresh camera shot, cloud-backed gallery item) when the upload starts.
    Chrome then aborts the request (ERR_UPLOAD_FILE_CHANGED / XHR status 0)
    and every retry with the same File object fails too.

    Fix: copy the file bytes into memory the instant it is picked, and always
    upload that in-memory copy. Every failure is reported to client.upload.log.
--}}
<script>
window.EteraUpload = window.EteraUpload || (function () {
    const LOG_URL = @json(route('client.upload.log'));
    const CSRF = @json(csrf_token());
    const STALE_MSG = 'Photo changed on the phone while uploading. Remove it and pick it again.';

    // original File -> Promise<File> (in-memory copy)
    const copies = new WeakMap();

    function log(event, data) {
        try {
            fetch(LOG_URL, {
                method: 'POST',
                keepalive: true,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify(Object.assign({ event: event, page: location.pathname }, data || {})),
            }).catch(() => {});
        } catch (e) {}
    }

    function info(input, f) {
        return {
            input: input,
            file_name: f && f.name,
            file_size: f && f.size,
            file_type: f && f.type,
            last_modified: f && f.lastModified,
        };
    }

    function snapshot(file) {
        if (!(file instanceof Blob)) return Promise.reject(new Error('not a file'));
        if (!copies.has(file)) {
            const p = file.arrayBuffer().then(buf => new File([buf], file.name || 'image', {
                type: file.type,
                lastModified: file.lastModified || Date.now(),
            }));
            p.catch(() => {});
            copies.set(file, p);
        }
        return copies.get(file);
    }

    // Snapshot files as soon as the user picks them, before FilePond starts
    // its own (slower) processing, to beat any later on-disk change.
    function watch(pond, input) {
        pond.element.addEventListener('change', (e) => {
            const files = e.target && e.target.files;
            if (files) Array.from(files).forEach(f => snapshot(f).catch(err => log('read_failed', Object.assign(info(input, f), {
                stage: 'pick', error_main: err && err.name, error_sub: err && err.message,
            }))));
        }, true);

        pond.on('addfile', (error, item) => {
            if (error) {
                log('addfile_failed', Object.assign(info(input, item && item.file), {
                    error_main: error.main, error_sub: error.sub,
                }));
                return;
            }
            if (item && item.file) {
                snapshot(item.file).catch(() => {});
                log('file_added', info(input, item.file));
            }
        });

        pond.on('error', (error, item) => {
            log('filepond_error', Object.assign(info(input, item && item.file), {
                error_main: error && error.main,
                error_sub: error && error.sub,
                error_code: error && error.code,
            }));
        });
    }

    /**
     * storeAsFile mode (plain form POST): after FilePond prepares the output,
     * replace the hidden file input's content with an in-memory copy, and keep
     * the submit button disabled until that is done.
     */
    function attachForm(pond, input) {
        watch(pond, input);

        const form = pond.element.closest('form');
        const pending = new Set();
        const sync = () => {
            const btn = form && form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = pending.size > 0;
        };
        const done = (id) => { pending.delete(id); sync(); };

        pond.on('addfilestart', (item) => {
            pending.add(item.id);
            sync();
            setTimeout(() => done(item.id), 20000);
        });

        pond.on('preparefile', (item, output) => {
            snapshot(output || item.file)
                .then(copy => new Promise(resolve => setTimeout(() => {
                    // FilePond writes its own output on the next tick; overwrite after it
                    const field = pond.element.querySelector('.filepond--data input[type="file"]');
                    if (field && window.DataTransfer) {
                        const dt = new DataTransfer();
                        dt.items.add(copy);
                        field.files = dt.files;
                    }
                    resolve();
                }, 60)))
                .catch(err => {
                    log('read_failed', Object.assign(info(input, item.file), {
                        stage: 'prepare', error_main: err && err.name, error_sub: err && err.message,
                    }));
                    alert(STALE_MSG);
                    pond.removeFile(item.id);
                })
                .finally(() => done(item.id));
        });

        pond.on('addfile', (error, item) => { if (error && item) done(item.id); });
        pond.on('removefile', (...args) => {
            const item = args.find(a => a && a.id);
            if (item) done(item.id);
        });
    }

    /**
     * Async mode: FilePond server.process function that uploads the in-memory
     * copy and retries once on network failure (status 0).
     * parse(responseText) must return the serverId or throw.
     */
    function processor(opts) {
        return (fieldName, file, metadata, load, error, progress, abort) => {
            let xhr = null;
            let aborted = false;
            const input = opts.input;

            const send = (copy, attempt) => {
                xhr = new XMLHttpRequest();
                xhr.open('POST', opts.url);
                xhr.setRequestHeader('X-CSRF-TOKEN', CSRF);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.upload.onprogress = (e) => progress(e.lengthComputable, e.loaded, e.total);
                xhr.onload = () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            load(opts.parse(xhr.responseText));
                            log('process_success', Object.assign(info(input, copy), { attempt: attempt }));
                        } catch (e) {
                            log('process_failed', Object.assign(info(input, copy), {
                                stage: 'parse', error_code: xhr.status, error_sub: String(xhr.responseText).slice(0, 200),
                            }));
                            error('Upload failed.');
                        }
                        return;
                    }
                    let msg = 'Upload error (' + xhr.status + ').';
                    try { msg = JSON.parse(xhr.responseText).message || msg; } catch (e) {}
                    if (xhr.status === 413) msg = 'Image is too large for the server.';
                    if (xhr.status === 419) msg = 'Session expired. Refresh the page.';
                    log('process_failed', Object.assign(info(input, copy), {
                        stage: 'http', attempt: attempt, error_code: xhr.status,
                        error_sub: String(xhr.responseText).slice(0, 200),
                    }));
                    error(msg);
                };
                xhr.onerror = () => {
                    log('process_failed', Object.assign(info(input, copy), {
                        stage: 'network', attempt: attempt, error_code: 0,
                        error_main: navigator.onLine ? 'online' : 'offline',
                    }));
                    if (!aborted && attempt < 2) {
                        setTimeout(() => { if (!aborted) send(copy, attempt + 1); }, 1500);
                    } else {
                        error('Network error. Check the connection and tap retry.');
                    }
                };
                const fd = new FormData();
                fd.append(fieldName, copy, copy.name);
                xhr.send(fd);
            };

            snapshot(file)
                .then(copy => { if (!aborted) send(copy, 1); })
                .catch(err => {
                    log('read_failed', Object.assign(info(input, file), {
                        stage: 'process', error_main: err && err.name, error_sub: err && err.message,
                    }));
                    error(STALE_MSG);
                });

            return {
                abort: () => {
                    aborted = true;
                    if (xhr) xhr.abort();
                    abort();
                },
            };
        };
    }

    return { log: log, watch: watch, attachForm: attachForm, processor: processor };
})();
</script>
