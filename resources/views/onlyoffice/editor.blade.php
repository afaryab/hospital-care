<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $config['document']['title'] }}</title>
    <style>html, body, #editor { height: 100%; margin: 0; padding: 0; }</style>
</head>
<body>
    <div id="editor"></div>

    <script src="{{ $apiUrl }}"></script>
    <script>
        new DocsAPI.DocEditor('editor', @json($config));
    </script>
</body>
</html>
