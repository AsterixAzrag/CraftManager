<form method="POST" action="{{ $action }}" class="inline-block">
    @csrf
    @method('PATCH')
    <button class="text-sm font-medium {{ $active ? 'text-red-700' : 'text-emerald-700' }}" type="submit">
        {{ $active ? 'Desactivar' : 'Activar' }}
    </button>
</form>
