<!DOCTYPE html>
<html>
<head>
    <title>Studio List</title>
</head>
<body>
    <h1>Studio List</h1>
    <ul>
        @foreach ($studios as $studio)
            <li>{{ $studio['name'] ?? 'Unnamed Studio' }}</li>
        @endforeach
    </ul>
</body>
</html>
