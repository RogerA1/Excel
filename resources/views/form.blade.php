<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/form.css">
    <title>Document</title>
</head>
<body>
    <x-frame.header-layout/>
    <h3 style="text-align: center">admin page test</h3>
    <h4 style="text-align: center">insert excel file in mysql</h4>
<div class="content-wrapper">
<div class="form-wrapper">
    <form action="{{ url('imporadio') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="test" accept=".xlsx,.csv">
        <button type="submit">Import</button>
        <a href="{{ url('exporadio') }}" class="export-link">Download Full Data</a>
    </form>
</div>

    <!-- Export Link -->
</div>


    <footer id="contact" class="footer">
        <div class="footer-content">
            <p>&copy; 2025 Radio Station. All Rights Reserved.</p>
            <ul class="social-links">
                <li><a href="#">Facebook</a></li>
                <li><a href="#">Twitter</a></li>
                <li><a href="#">Instagram</a></li>
            </ul>
        </div>
    </footer>
</body>
</html>