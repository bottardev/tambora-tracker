<div class="flex justify-center">
    @if($getRecord()->image)
        <img src="{{ asset('storage/' . $getRecord()->image) }}" 
             alt="{{ $getRecord()->name }}" 
             class="w-20 h-20 object-cover rounded-lg shadow-sm">
    @else
        <img src="https://images.unsplash.com/photo-1464822759844-d150baec93c5?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&h=150&q=80" 
             alt="{{ $getRecord()->name }}" 
             class="w-20 h-20 object-cover rounded-lg shadow-sm">
    @endif
</div>