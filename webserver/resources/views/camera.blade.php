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
    </style>
</head>
<body>
    <h1>位你好WellPark</h1>
    <h3>停車場AI相機辨識系統</h3>
    <video id="video" autoplay></video>
    <canvas id="canvas" style="display: none;"></canvas>
    <button id="startButton">Start Upload</button>
    <button id="stopButton" style="display: none;">Stop Upload</button>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const startButton = document.getElementById('startButton');
        const stopButton = document.getElementById('stopButton');
        let intervalId;

        // Get access to the camera
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => {
                console.error("Error accessing the camera: ", err);
            });

        // Function to capture and upload image
        async function captureAndUpload() {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            canvas.toBlob(async (blob) => {
                const formData = new FormData();
                formData.append('image', blob, 'capture.jpg');
                formData.append('park_no', 'nctu-demo');
                formData.append('captured_at', new Date().toISOString());

                try {
                    const response = await fetch('{{ url("/api/park-image") }}', {
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

        // Start uploading images at intervals
        startButton.addEventListener('click', () => {
            startButton.style.display = 'none';
            stopButton.style.display = 'inline';
            intervalId = setInterval(captureAndUpload, 5000); // Upload every 5 seconds
        });

        // Stop uploading images
        stopButton.addEventListener('click', () => {
            clearInterval(intervalId);
            startButton.style.display = 'inline';
            stopButton.style.display = 'none';
        });
    </script>
</body>
</html>
