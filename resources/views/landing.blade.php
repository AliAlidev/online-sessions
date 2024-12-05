<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <script src="/js/app.js"></script>
</head>
<body>
    <h1>Welcome to the Landing Page</h1>
    <button onclick="callAPI()">Call API</button>
    <script>
        function callAPI() {
            fetch('/api/user', {
                method: 'POST',
                credentials: 'include'  // Important for cookies to be sent
            })
            .then(response => response.json())
            .then(data => console.log(JSON.stringify(data)))
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
