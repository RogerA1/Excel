<x-home-layout title="{{ $rad->title }}" style="css/plusrad.css">
<x-frame.header-layout/>
    <h1>Radio Details</h1>

    <div class="card-container">
        <!-- Card for Radio Details -->
        <div class="card">
            <div class="card-header">
                <p><span>Title ID:</span> {{ $rad->title_id }}</p>
            </div>
            <div class="card-content">
                <p><span>Title:</span> {{ $rad->title }}</p>
                <p><span>Author:</span> {{ $rad->author }}</p>
                <p><span>Duration:</span> {{ $rad->Durée }}</p>
                <p><span>Interpret:</span> {{ $rad->interpret }}</p>
                <p><span>Download WAV file: </span><a href="{{ route('upload', ['id' => $rad->title_id]) }}" >Download</a></p>
                <sub><i>Last update : {{$rad->last_modif_time}}</i></sub>
            </div>
            <div class="card-footer">
                <div id="waveform"></div> <!-- Waveform container -->
                <label for="volumeSlider">Volume:</label>
                <input type="range" id="volumeSlider" min="0" max="1" step="0.01" value="1">
                <button id="playPauseButton">Play / Pause</button>
                
            </div>
            <div>
                <h4>Comments:</h4>
                <p>{{$rad->commentaire1}}</p>
                <p>{{$rad->commentaire2}}</p>
                <p>{{$rad->commentaire3}}</p>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/wavesurfer.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const wavesurfer = WaveSurfer.create({
            container: '#waveform',
            waveColor: '#888',          
         progressColor: '#ffffff',
            height: 100,
            barWidth: 2
        });

        const audioUrl = '{{ Storage::url("audio_files/$mp3file") }}';
        wavesurfer.load(audioUrl);

        // Play / Pause button
        document.getElementById('playPauseButton').addEventListener('click', function () {
            wavesurfer.playPause();
        });

        // Volume control
        const volumeSlider = document.getElementById('volumeSlider');
        wavesurfer.setVolume(1); // Set initial volume

        volumeSlider.addEventListener('input', function () {
            wavesurfer.setVolume(this.value);
        });
    });
</script>

  
</x-home-layout>
