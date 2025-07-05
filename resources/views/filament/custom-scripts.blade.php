<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

{{-- Script client side compress image yang lebih stabil --}}
<script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const compressionOptions = {
            maxSizeMB: 1,
            maxWidthOrHeight: 1920,
            useWebWorker: true,
            fileType: 'image/jpeg'
        };

        const handleFileProcessing = (pond, fileItem) => {
            if (fileItem.getMetadata('processed')) return;
            fileItem.setMetadata('processed', true);

            console.log(`➡️ File added: ${fileItem.file.name}. Starting compression...`);

            imageCompression(fileItem.file, compressionOptions)
                .then(compressedFile => {
                    const newFile = new File([compressedFile], fileItem.file.name, {
                        type: compressedFile.type,
                        lastModified: Date.now()
                    });
                    pond.removeFile(fileItem.id);
                    pond.addFile(newFile).then(newFileItem => {
                        if (newFileItem) newFileItem.setMetadata('processed', true);
                    });
                })
                .catch(err => console.error('❌ Compression Error:', err.message));
        };

        document.addEventListener('FilePond:init', (e) => {
            const pond = e.detail.pond;
            pond.on('addfile', (error, fileItem) => {
                if (error || !(fileItem.file instanceof File) || fileItem.getMetadata(
                        'processed')) {
                    return;
                }
                handleFileProcessing(pond, fileItem);
            });
        });
    });
</script>

{{-- Script update yang berfungsi tetapi berefek disable client side compress image --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log('✅ Custom script loaded. Waiting for FilePond instances...');

        const compressionOptions = {
            maxSizeMB: 1,
            maxWidthOrHeight: 1920,
            useWebWorker: true,
            fileType: 'image/jpeg'
        };

        // Fungsi ini akan kita panggil setiap kali ada file BARU dari pengguna
        const handleCompression = (pond, fileItem) => {
            console.log(`➡️ New user file detected: ${fileItem.file.name}. Starting compression...`);

            // Tandai file ini agar tidak diproses berulang kali
            fileItem.setMetadata('processed_for_compression', true);

            const sizeElement = fileItem.element.querySelector('.filepond--file-info-main');
            if (sizeElement) sizeElement.innerHTML = 'Compressing...';

            imageCompression(fileItem.file, compressionOptions)
                .then(compressedFile => {
                    console.log('✅ Compression successful. Replacing file in queue.');

                    // Buat objek file baru dari hasil kompresi
                    const newFile = new File([compressedFile], fileItem.file.name, {
                        type: compressedFile.type,
                        lastModified: Date.now()
                    });

                    // Hapus file asli dan tambahkan file baru untuk memicu upload ulang
                    pond.removeFile(fileItem.id);
                    pond.addFile(newFile).then(newFileItem => {
                        // Tandai file yang baru ditambahkan ini agar tidak diproses lagi oleh listener
                        if (newFileItem) newFileItem.setMetadata('processed_for_compression',
                            true);
                    });

                })
                .catch(err => {
                    console.error('❌ Compression Error:', err.message);
                    // Jika kompresi gagal, kembalikan tampilan ukuran file asli
                    if (sizeElement) sizeElement.innerHTML = pond.formatSize(fileItem.file.size);
                });
        };

        // Dengarkan event global saat FilePond siap
        document.addEventListener('FilePond:init', (e) => {
            const pond = e.detail.pond;
            console.log('✅ FilePond instance initialized!', pond);

            // Tambahkan listener untuk event 'addfile'
            pond.on('addfile', (error, fileItem) => {
                if (error) {
                    return;
                }

                // =======================================================================
                //       INTEGRASI DAN PERUBAHAN KRUSIAL ADA DI SINI
                // =======================================================================
                // Kita hanya akan memproses file jika asalnya dari 'input' (pilihan pengguna)
                // dan belum pernah kita proses sebelumnya (cek metadata).
                // File yang dimuat dari server saat Edit/View memiliki origin 'local',
                // sehingga akan diabaikan oleh blok 'if' ini.
                // =======================================================================
                if (fileItem.origin === 'input' && !fileItem.getMetadata(
                        'processed_for_compression')) {
                    handleCompression(pond, fileItem);
                } else {
                    console.log(
                        `⚪️ Ignoring file. Origin: ${fileItem.origin}. File: ${fileItem.file.name}`
                        );
                }
            });
        });
    });
</script> --}}

{{-- Script yang berfungsi compress image client-side --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log('✅ Custom script loaded. Waiting for FilePond instances...');

        const compressionOptions = {
            maxSizeMB: 1,
            maxWidthOrHeight: 1920,
            useWebWorker: true,
            fileType: 'image/jpeg'
        };

        const handleFileProcessing = (pond, fileItem) => {
            // Tambahkan flag untuk menandai file ini sedang/sudah diproses
            // untuk mencegah infinite loop.
            if (fileItem.getMetadata('processed')) {
                return;
            }
            fileItem.setMetadata('processed', true);

            console.log(`➡️ File added: ${fileItem.file.name}. Starting compression...`);

            imageCompression(fileItem.file, compressionOptions)
                .then(compressedFile => {
                    console.log('✅ Compression successful. Replacing file...');

                    // Buat file baru dengan nama yang sama dengan file asli
                    const newFile = new File([compressedFile], fileItem.file.name, {
                        type: compressedFile.type,
                        lastModified: new Date().getTime()
                    });

                    // Ini adalah bagian kuncinya:
                    // 1. Hapus file asli dari pond
                    pond.removeFile(fileItem.id);

                    // 2. Tambahkan file baru hasil kompresi. Ini akan memicu
                    //    proses upload sementara yang baru dengan file yang benar.
                    pond.addFile(newFile).then(newFileItem => {
                        // Tandai file yang baru ditambahkan agar tidak diproses lagi
                        if (newFileItem) {
                            newFileItem.setMetadata('processed', true);
                        }
                    });

                })
                .catch(err => {
                    console.error('❌ Compression Error:', err.message);
                    // Jika gagal, biarkan file asli tetap ada
                });
        };

        document.addEventListener('FilePond:init', (e) => {
            const pond = e.detail.pond;
            console.log('✅ FilePond instance has been initialized!', pond);

            pond.on('addfile', (error, fileItem) => {
                if (error) {
                    return;
                }

                // Pastikan file belum diproses dan merupakan gambar
                if (!fileItem.getMetadata('processed') && fileItem.file.type.startsWith(
                        'image/')) {
                    handleFileProcessing(pond, fileItem);
                }
            });
        });
    });
</script> --}}


{{-- Versi original sebelum berfungsi --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('imageCompressor', () => ({
            // State untuk melacak apakah sedang dalam proses kompresi
            isCompressing: false,

            // Opsi untuk kompresi, bisa disesuaikan
            options: {
                maxSizeMB: 1, // Ukuran maksimal file hasil kompresi (dalam MB)
                maxWidthOrHeight: 1920, // Resolusi maksimal
                useWebWorker: true, // Gunakan web worker untuk performa lebih baik
                fileType: 'image/jpeg', // Paksa output ke JPEG untuk konsistensi
            },

            // Fungsi inisialisasi yang akan dipanggil oleh FileUpload
            init(element) {
                // FileUpload Filament menggunakan FilePond di belakang layar.
                // Kita akan mengaitkan ke event 'addfile' milik FilePond.
                const pond = window.Livewire.find(element.closest('[wire\\:id]').getAttribute(
                    'wire:id')).find('livewire-file-upload').pond;

                if (!pond) {
                    console.error('FilePond instance not found.');
                    return;
                }

                pond.on('addfile', (error, fileItem) => {
                    if (error) {
                        return;
                    }

                    // Hanya proses file gambar
                    if (!fileItem.file.type.startsWith('image/')) {
                        return;
                    }

                    this.isCompressing = true;
                    console.log(`Compressing ${fileItem.file.name}...`);

                    // Panggil fungsi kompresi
                    imageCompression(fileItem.file, this.options)
                        .then(compressedFile => {
                            console.log(
                                `Compression successful for ${fileItem.file.name}.`);
                            console.log(
                                `Original size: ${(fileItem.file.size / 1024 / 1024).toFixed(2)} MB`
                            );
                            console.log(
                                `New size: ${(compressedFile.size / 1024 / 1024).toFixed(2)} MB`
                            );

                            // Ini bagian penting: ganti file asli di FilePond dengan file hasil kompresi
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(compressedFile);

                            // Ganti file di antrian upload FilePond
                            fileItem.file = dataTransfer.files[0];

                        })
                        .catch(error => {
                            console.error('Image compression error:', error.message);
                            // Jika gagal, biarkan file asli yang diunggah
                        })
                        .finally(() => {
                            this.isCompressing = false;
                        });
                });
            }
        }));
    });
</script> --}}
