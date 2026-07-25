document.addEventListener('alpine:init', () => {
    Alpine.data('cameraCapture', (component) => ({
        stream: null,
        capturedImage: null,
        isCameraActive: false,
        init() {
            this.startCamera();
        },

        async startCamera() {
            this.errorMsg = null;
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user', width: { ideal: 720 }, height: { ideal: 960 } },
                    audio: false
                });
                this.$nextTick(() => {
                    if (this.$refs.video) {
                        this.$refs.video.srcObject = this.stream;
                        this.isCameraActive = true;
                    }
                });
            } catch (err) {
                console.error("Camera access error:", err);
                this.errorMsg = "Gagal mengakses kamera. Pastikan izin kamera sudah diberikan.";
            }
        },

        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
            this.isCameraActive = false;
        },

        capture() {
            if (!this.$refs.video) return;
            const video = this.$refs.video;
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            const ctx = canvas.getContext('2d');

            // Flip horizontally to un-mirror front camera photo
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            this.capturedImage = canvas.toDataURL('image/jpeg', 0.85);
            this.stopCamera();

            // Emit to Livewire component
            if (component && typeof component.setPhoto === 'function') {
                component.setPhoto(this.capturedImage);
            }
        },

        retake() {
            this.capturedImage = null;
            if (component && typeof component.clearPhoto === 'function') {
                component.clearPhoto();
            }
            this.startCamera();
        }
    }));
});
