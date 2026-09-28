<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document editor unavailable</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f5f5f5; color: #1f2937; display: flex; min-height: 100vh; align-items: center; justify-content: center; margin: 0; padding: 16px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.1); padding: 32px; max-width: 480px; }
        h1 { font-size: 20px; margin: 0 0 12px; }
        p { margin: 0 0 12px; line-height: 1.5; }
        a { color: #2563eb; }
    </style>
</head>
<body>
    <div class="card">
        <h1>The document editor is temporarily unavailable</h1>
        <p><strong>{{ $documentName }}</strong> can't be opened for editing right now because the document server is not responding.</p>
        <p>You can still download, upload and manage documents. Please try editing again later, or let IT know if this continues.</p>
        <p><a href="{{ $backUrl }}">Back to Documents</a></p>
    </div>
</body>
</html>
