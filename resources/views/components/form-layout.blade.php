<div class="form-container">
        <form action="{{ url('imporadio') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="test" accept=".xlsx,.csv">
            <button type="submit">Import</button>
        </form>
    </div>

    <!-- Export Link -->
    <a href="{{ url('exporadio') }}" class="export-link">Download Full Data</a>
