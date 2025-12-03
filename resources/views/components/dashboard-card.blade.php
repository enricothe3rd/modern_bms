<div class="dashboard-card p-6 rounded-xl border bg-white shadow-md">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-slate-600 text-sm font-medium">{{ $label ?? 'Metric Label' }}</p>
            <p class="text-3xl font-bold text-slate-900 mt-2">{{ $value ?? '0' }}</p>
            @if($change)
                <p class="text-{{ $changeColor ?? 'cyan' }}-600 text-xs mt-2">{{ $change }}</p>
            @endif
        </div>
        <div class="w-12 h-12 {{ $iconBgClass ?? 'bg-gradient-to-br from-purple-500 to-cyan-500' }} rounded-lg opacity-20"></div>
    </div>
</div>
