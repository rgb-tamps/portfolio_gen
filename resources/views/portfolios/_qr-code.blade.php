<div class="flex flex-col items-start gap-4 rounded-2xl border border-slate-200 bg-white p-4 sm:flex-row sm:items-center"
     data-portfolio-qr
     data-qr-url="{{ route('portfolios.public', $portfolio->slug) }}"
     data-qr-filename="{{ \Illuminate\Support\Str::slug($portfolio->full_name) ?: 'portfolio' }}-qr.png">
    <canvas data-qr-canvas width="192" height="192" class="h-36 w-36 rounded-xl border border-slate-100 p-2" aria-label="QR code for {{ $portfolio->full_name }}'s public portfolio"></canvas>
    <div>
        <h2 class="font-bold text-slate-900">Share this portfolio</h2>
        <p class="mt-1 max-w-md text-sm leading-6 text-slate-600">Scan the code to open this public portfolio, or download it to share anywhere.</p>
        <button type="button" data-qr-download disabled class="mt-3 rounded-xl bg-brand px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-hover disabled:cursor-wait disabled:opacity-60">
            Download QR code
        </button>
        <p data-qr-status role="status" aria-live="polite" class="mt-2 text-xs text-slate-500">Creating QR code…</p>
    </div>
</div>
