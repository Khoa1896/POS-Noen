<!-- resources/views/partials/customer-create-form.blade.php -->
<div class="modal-header">
    <h5 class="modal-title" id="createCustomerModalLabel">Create New Customer</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body">
    <form id="customer-form" action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('utils.alerts')
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="customer_name">Customer Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="customer_name" required value="{{ old('customer_name') }}">
                    @error('customer_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="customer_email">Email</label>
                    <input type="email" class="form-control" name="customer_email" value="{{ old('customer_email') }}">
                    @error('customer_email') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="customer_phone">Phone</label>
                    <input type="text" class="form-control" name="customer_phone" value="{{ old('customer_phone') }}">
                    @error('customer_phone') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="customer_address">Address</label>
                    <input type="text" class="form-control" name="customer_address" value="{{ old('customer_address') }}">
                    @error('customer_address') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="customer_note">Note</label>
            <textarea name="customer_note" id="customer_note" rows="4" class="form-control">{{ old('customer_note') }}</textarea>
            @error('customer_note') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Create Customer <i class="bi bi-check"></i></button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
    </form>
</div>

@section('third_party_scripts')
    <script src="{{ asset('js/jquery-mask-money.js') }}"></script>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function () {
            $('#customer-form').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $('#createCustomerModal').modal('hide');
                        // Refresh Livewire component or update dropdown
                    @if(isset($this))
                        @this.set('customer_id', response.customer_id);
                    @this.call('refreshCustomers');
                        @endif
                        // Clear form
                        $('#customer-form')[0].reset();
                    },
                    error: function (xhr) {
                        // Display validation errors
                        let errors = xhr.responseJSON.errors;
                        if (errors) {
                            $.each(errors, function (key, value) {
                                $(`#customer-form [name="${key}"]`).addClass('is-invalid');
                                $(`#customer-form [name="${key}"]`).after(`<span class="text-danger">${value[0]}</span>`);
                            });
                        } else {
                            alert('Error creating customer. Please try again.');
                        }
                    }
                });
            });

            // Clear validation errors on input
            $('#customer-form input, #customer-form textarea').on('input', function () {
                $(this).removeClass('is-invalid');
                $(this).next('.text-danger').remove();
            });
        });
    </script>
@endpush
