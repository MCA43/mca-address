@if ($sort ?? null)
    <input type="hidden" name="sort" value="{{ $sort }}">
    <input type="hidden" name="dir" value="{{ $dir ?? 'asc' }}">
@endif
