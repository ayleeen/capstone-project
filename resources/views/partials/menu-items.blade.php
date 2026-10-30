<div class="menu-items-scroll">
    @forelse($items as $item)
        <div class="menu-item-row">
            @if($item->image_path)
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="menu-item-thumb">
            @endif
            <div class="menu-item-info">
                <strong>{{ $item->name }}</strong>
                @if($item->description)
                    <small class="text-muted d-block">{{ $item->description }}</small>
                @endif
            </div>
            @if(!is_null($item->price))
                <span class="menu-item-price">₱{{ number_format($item->price, 2) }}</span>
            @endif
        </div>
    @empty
        <div class="menu-cat-empty">No items added yet</div>
    @endforelse
</div>