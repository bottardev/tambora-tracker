<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambora Tracker - Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏔️ Tambora Tracker - Simple Test</h1>
        
        <p>This is a simplified test to check if the basic functionality works.</p>
        
        <h3>Available Routes</h3>
        @if($routes->count() > 0)
            @foreach($routes as $route)
                <div style="border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px;">
                    <h4>{{ $route->name }}</h4>
                    <p>{{ $route->description ?? 'No description available' }}</p>
                    <button class="btn" onclick="alert('Route ID: {{ $route->id }}')">Select This Route</button>
                </div>
            @endforeach
        @else
            <p>No routes available. Let's create one!</p>
            <button class="btn" onclick="createTestRoute()">Create Test Route</button>
        @endif
        
        <hr style="margin: 30px 0;">
        
        <h3>Test Links</h3>
        <a href="/test" class="btn">Test Basic Route</a>
        <a href="/check-booking" class="btn" style="margin-left: 10px;">Check Booking Status</a>
        
    </div>
    
    <script>
        function createTestRoute() {
            fetch('/api/test/create-route', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(() => location.reload());
        }
    </script>
</body>
</html>