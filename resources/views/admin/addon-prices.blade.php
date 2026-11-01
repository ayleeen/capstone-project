<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add-on Prices</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:520px;">
  <div class="card shadow-sm">
    <div class="card-body p-4">
      <h4 class="mb-1">Add-on Tray Prices</h4>
      <p class="text-muted small">Presyo ng dagdag na ulam kada tray (per serving).</p>

      @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('admin.addon-prices.update') }}">
        @csrf
        @method('PUT')

        @foreach ($prices as $p)
          <div class="mb-3">
            <label class="form-label">{{ $p->servings }} pax (₱)</label>
            <input type="number" step="0.01" min="0"
                   name="prices[{{ $p->id }}]" value="{{ $p->price }}"
                   class="form-control" required>
          </div>
        @endforeach

        <button type="submit" class="btn btn-success w-100">Save Prices</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>