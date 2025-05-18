<div class="input-group d-flex justify-content-center">
    <input
        wire:model.lazy="quantity.{{ $cart_item->id }}"
        wire:change="updateQuantity('{{ $cart_item->rowId }}', {{ $cart_item->id }})"
        style="min-width: 40px; max-width: 90px;"
        type="number"
        class="form-control"
        min="1"
    >
</div>
