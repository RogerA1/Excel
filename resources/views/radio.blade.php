<x-home-layout title='Radio List' style="css/radio.css">
<x-frame.header-layout/>

<h1>Radio List</h1>

<!-- Search Form -->
<form method="GET" action="{{ route('listen') }}" class="form-container search">
    <div class="search-bar">
        <a href="{{ url()->previous() }}" class="back-button">Go Back</a>
        <input type="text" name="search" value="{{ request('search') }}" id="search-input" placeholder="Search radios...">
        <label id="output" style="color: red; margin-left: 10px;"></label>
        <button type="submit">Search</button>
    </div>
</form>

<!-- Download Form -->
<form method="POST" action="{{ route('radios.downloadMultiple') }}">
    @csrf

    <div class="button-container">
        <button type="submit" id="downloadSelectedBtn" style="display:none;">Download Selected</button>
    </div>

    <table id="radioTable">
        <thead>
            <tr>
                <th></th>
                <th>Title</th>
                <th>Author</th>
                <th>Duration</th>
                <th>Interpret</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($radios as $radio)
                <tr>
                    <td><input type="checkbox" name="selected_radios[]" value="{{ $radio->title_id }}"></td>
                    <td>{{ $radio->title }}</td>
                    <td>{{ $radio->author }}</td>
                    <td>{{ $radio->Durée }}</td>
                    <td>{{ $radio->interpret }}</td>
                    <td><a href="{{ route('plusrad', $radio->title_id) }}">More</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No results found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(request()->has('search') && request('search') !== '' && $radios->count() > 0)
        <a class="export-link" href="{{ request()->fullUrlWithQuery(['download' => 'excel']) }}">Download Excel</a>
    @endif
</form>

<!-- Pagination -->
<div class="mt-4" style="overflow-x: auto;">
    <div class="d-flex justify-content-center" style="min-width: max-content;">
        {{ $radios->links('pagination::bootstrap-5') }}
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Toggle download button visibility
    const checkboxes = document.querySelectorAll('input[name="selected_radios[]"]');
    const downloadButton = document.getElementById('downloadSelectedBtn');

    function toggleDownloadButton() {
        let anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        downloadButton.style.display = anyChecked ? 'inline-block' : 'none';
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', toggleDownloadButton);
    });

    // Search form validation
    const searchForm = document.querySelector('form.form-container.search');
    const searchInput = document.getElementById('search-input');
    const outputLabel = document.getElementById('output');

    searchForm.addEventListener('submit', function(e) {
        outputLabel.textContent = ''; // clear previous message
        if (searchInput.value.trim() === '') {
            e.preventDefault();
            outputLabel.textContent = 'No result';
        }
    });
});
</script>

</x-home-layout>
