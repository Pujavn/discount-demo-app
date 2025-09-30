<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Discount Demo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">

  <h1 class="mb-4">Discount Demo</h1>

  <div class="alert alert-info">
    <strong>How this works:</strong>
    Pick a discount below, enter an amount in rupees, and apply. Same <em>Application Key</em> is idempotent.
  </div>

  {{-- Available discounts --}}
  <h5 class="mb-3">Available Discounts</h5>
  @if($discounts->isEmpty())
    <p class="text-muted">No active discounts found.</p>
  @else
    <div class="table-responsive mb-4">
      <table class="table table-sm table-bordered align-middle">
        <thead>
          <tr>
            <th>Name</th>
            <th>Slug</th>
            <th>Percent</th>
            <th>Per-User Cap</th>
          </tr>
        </thead>
        <tbody>
          @foreach($discounts as $d)
            <tr @class(['table-primary' => $selected && $selected->id === $d->id])>
              <td>{{ $d->name }}</td>
              <td><code>{{ $d->slug }}</code></td>
              <td>{{ $d->percent ?? '-' }}%</td>
              <td>{{ $d->per_user_cap ?? '∞' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

  {{-- Apply form --}}
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="card-title mb-3">Apply a Discount</h5>
      <form method="post" action="{{ route('discount.demo.apply') }}" class="row gy-3 gx-2 align-items-end">
        @csrf

        <div class="col-md-4">
          <label class="form-label">Choose Discount</label>
          <select name="discount_slug" class="form-select" required>
            @foreach($discounts as $d)
              <option value="{{ $d->slug }}" @selected($selected && $selected->slug === $d->slug)>
                {{ $d->name }} ({{ $d->percent ?? 0 }}%)
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Amount (₹)</label>
          <input type="number" step="0.01" min="0" class="form-control" name="amount_rupees" placeholder="e.g. 100.00" required>
        </div>

        <div class="col-md-3">
          <label class="form-label">Application Key (optional)</label>
          <input type="text" class="form-control" name="application_key" placeholder="e.g. ORDER#123">
          <div class="form-text">Idempotency key. Same key ⇒ no double-count.</div>
        </div>

        <div class="col-md-2">
          <button class="btn btn-primary w-100">Apply</button>
        </div>
      </form>

      @if($result)
        <hr class="my-4">
        <h6>Result</h6>
        <ul class="list-unstyled mb-0">
          <li>Key: <code>{{ $result['key'] }}</code></li>
          <li>Subtotal: ₹{{ number_format($result['subtotalMinor']/100, 2) }}</li>
          <li>Discount Applied: <strong>₹{{ number_format($result['appliedMinor']/100, 2) }}</strong></li>
          <li>Payable: <strong>₹{{ number_format($result['payableMinor']/100, 2) }}</strong></li>
        </ul>
      @endif
    </div>
  </div>

  {{-- Usage + Audits for selected discount --}}
  @if($selected)
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-3">Usage & Recent Audits — {{ $selected->name }}</h5>
        <p class="mb-2">
          Used by this user: <strong>{{ $usageCount }}</strong> /
          Cap: <strong>{{ $selected->per_user_cap ?? '∞' }}</strong>
        </p>

        @if($audits->isEmpty())
          <p class="text-muted mb-0">No audits yet. Apply a discount above.</p>
        @else
          <div class="table-responsive">
            <table class="table table-sm align-middle">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Action</th>
                  <th>Key</th>
                  <th>Amount (₹)</th>
                  <th>When</th>
                </tr>
              </thead>
              <tbody>
                @foreach($audits as $a)
                  <tr>
                    <td>{{ $a->id }}</td>
                    <td><span class="badge text-bg-secondary">{{ $a->action }}</span></td>
                    <td><code>{{ $a->application_key }}</code></td>
                    <td>{{ number_format(($a->amount_minor ?? 0)/100, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($a->created_at)->diffForHumans() }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  @endif

</body>
</html>
