<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camera Upload</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        video, canvas {
            width: 100%;
            max-width: 640px;
            height: auto;
        }
        button {
            margin: 10px;
        }
        #timer {
            font-size: 1.5em;
            margin-top: 10px;
        }
        h1 {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>位你好 WellPark</h1>
    <h3>請對鏡頭對準「剩餘停車位LED燈」</h3>
    <video id="video" autoplay playsinline></video>
    <canvas id="canvas" style="display: none;"></canvas>
    <button id="startButton">Start Upload</button>
    <button id="stopButton" style="display: none;">Stop Upload</button>
    <button id="switchToFront">Switch to Front Camera</button>
    <button id="switchToRear">Switch to Rear Camera</button>
    <div id="timer">5</div>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const startButton = document.getElementById('startButton');
        const stopButton = document.getElementById('stopButton');
        const timer = document.getElementById('timer');
        const switchToFront = document.getElementById('switchToFront');
        const switchToRear = document.getElementById('switchToRear');
        let intervalId;
        let countdownId;
        let seconds = 5;
        let currentStream;

        // Function to start the video stream with the specified facing mode
        function startVideo(facingMode) {
            const constraints = {
                video: {
                    facingMode: facingMode
                }
            };

            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
            }

            navigator.mediaDevices.getUserMedia(constraints)
                .then((stream) => {
                    currentStream = stream;
                    video.srcObject = stream;
                })
                .catch((error) => {
                    console.error('Error accessing the camera: ', error);
                });
        }

        // Event listeners for the buttons
        switchToFront.addEventListener('click', () => startVideo('user'));
        switchToRear.addEventListener('click', () => startVideo('environment'));

        // Start with the rear camera by default
        startVideo('environment');

        // Function to capture and upload image
        async function captureAndUpload() {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            canvas.toBlob(async (blob) => {
                const formData = new FormData();
                formData.append('image', blob, 'capture.jpg');
                formData.append('captured_at', new Date().toISOString());
                formData.append('park_no', 'nctu-demo');

                try {
                    const response = await fetch('/api/park-image', {
                        method: 'POST',
                        body: formData
                    });

                    if (response.ok) {
                        console.log('Image uploaded successfully');
                    } else {
                        console.error('Failed to upload image');
                    }
                } catch (error) {
                    console.error('Error uploading image: ', error);
                }
            }, 'image/jpeg');
        }

        // Function to update the timer
        function updateTimer() {
            timer.textContent = seconds;
            if (seconds > 0) {
                seconds--;
            } else {
                clearInterval(countdownId);
                captureAndUpload();
                seconds = 5; // Reset seconds after each upload
                countdownId = setInterval(updateTimer, 1000); // Restart countdown
            }
        }

        // Start uploading images at intervals
        startButton.addEventListener('click', () => {
            startButton.style.display = 'none';
            stopButton.style.display = 'inline';
            countdownId = setInterval(updateTimer, 1000); // Start countdown
        });

        // Stop uploading images
        stopButton.addEventListener('click', () => {
            clearInterval(intervalId);
            clearInterval(countdownId);
            startButton.style.display = 'inline';
            stopButton.style.display = 'none';
        });
    </script>
</body>
</html>
