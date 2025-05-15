<!DOCTYPE html>
<html>
<head>
    <title>Client List</title>
    <style>
        /* Simple CSS */
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #e9e9e9;
        }
    </style>
</head>
<body>
    <h1>Client List</h1>
    
    <!-- Import Form -->
    <form action="{{ url('import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="test" accept=".xlsx,.csv">
        <button type="submit">Import</button>
    </form>
    
    <!-- Export Link -->
    <p><a href="{{ url('export') }}">Export Clients</a></p>
    
    <!-- Clients Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Lastname</th>
                <th>Age</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients->take(5) as $client)
            <tr>
                <td>{{ $client->id }}</td>
                <td>{{ $client->name }}</td>
                <td>{{ $client->lastname }}</td>
                <td>{{ $client->age }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $clients->links() }}
</body>
</html>