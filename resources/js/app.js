import WaveSurfer from 'wavesurfer.js';

// When the page is loaded, initialize WaveSurfer
document.addEventListener('DOMContentLoaded', function () {
    const wavesurfer = WaveSurfer.create({
        container: '#waveform', // Container to render the waveform
        waveColor: '#ddd', // Color of the waveform
        progressColor: '#4db8ff', // Color of the progress bar
        height: 150, // Height of the waveform
        barWidth: 3, // Width of each waveform bar
    });

    // Load the audio file (replace with your actual file path)
    const audioUrl = '/storage/audio_file/00210331.wav'; // Correct audio file path in storage
    wavesurfer.load(audioUrl);

    // Play/Pause functionality
    const playPauseButton = document.getElementById('playPauseButton');
    playPauseButton.addEventListener('click', function () {
        wavesurfer.playPause();
    });
});
