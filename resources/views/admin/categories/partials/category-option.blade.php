<option value="{{ $category->id }}">
    {!! str_repeat('&nbsp;&nbsp;&nbsp;', $level) !!}{{ $level > 0 ? '↳ ' : '' }}{{ $category->name }}
</option>

@if ($category->children->count())
    @foreach ($category->children as $child)
        @include('admin.categories.partials.category-option', [
            'category' => $child,
            'level' => $level + 1,
        ])
    @endforeach
@endif
