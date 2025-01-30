@extends('layouts.app')

@section("breadcrumb")
<li class="breadcrumb-item">Settings</li>
<li class="breadcrumb-item active">Fare Settings</li>
@endsection

@section('extra_css')
<style>
.fare-container {
    background: #f8fafc;
    padding: 2rem;
    min-height: 100vh;
}

.main-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}

.card-header {
    border-bottom: 1px solid #e5e7eb;
    padding: 1.5rem;
}

.card-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.vehicle-tabs {
    display: flex;
    justify-content: center;
    margin: 2rem 0;
    gap: 0.5rem;
}

.vehicle-tab {
    padding: 0.75rem 2rem;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.2s;
    background: white;
    border: 1px solid #e5e7eb;
    color: #4b5563;
}

.vehicle-tab:hover {
    background: #f3f4f6;
    color: #1f2937;
}

.vehicle-tab.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.slab-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: all 0.2s;
}

.slab-card:hover {
    border-color: #93c5fd;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.slab-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.slab-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
}

.slab-counter {
    background: #dbeafe;
    color: #1d4ed8;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 500;
}

.slab-fields {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.form-group {
    margin-bottom: 0;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #4b5563;
    margin-bottom: 0.5rem;
}

.input-group {
    position: relative;
}

.input-group-text {
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-right: none;
    color: #6b7280;
    padding: 0.5rem 1rem;
}

.form-control {
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    width: 100%;
    transition: all 0.2s;
}

.form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-weight: 500;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary {
    background: #3b82f6;
    color: white;
    border: none;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-danger {
    background: #ef4444;
    color: white;
    border: none;
}

.btn-danger:hover {
    background: #dc2626;
}

.btn-success {
    background: #10b981;
    color: white;
    border: none;
}

.btn-success:hover {
    background: #059669;
}

.card-footer {
    border-top: 1px solid #e5e7eb;
    padding: 1.5rem;
    background: #f9fafb;
}

.actions-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    padding: 0 1rem;
}
</style>
@endsection
@section('content')
<div class="fare-container">
    <div class="main-card">
        <div class="card-header">
            <h3 class="card-title">Fare Settings</h3>
        </div>

        <div class="card-body p-4">
            <!-- Vehicle Tabs -->
                <div class="vehicle-tabs">
                @foreach($types as $typeId => $vehicleType)
                    <a href="#{{ strtolower(str_replace(' ', '', $vehicleType)) }}" 
                    data-toggle="tab" 
                    data-type-id="{{ $typeId }}"
                    class="vehicle-tab @if($loop->first) active @endif">
                        {{ $vehicleType }}
                    </a>
                @endforeach
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                @foreach($types as $typeId => $vehicleType)
                @php($vehicleKey = strtolower(str_replace(' ', '', $vehicleType)))
                    <div class="tab-pane @if($loop->first) active @endif" id="{{ $vehicleKey }}">
                        <div class="slabs-container">
                            @if(isset($slabs[$typeId]))
                                @foreach($slabs[$typeId] as $slabIndex => $fareItems)
                                    <div class="slab-card" data-slab-index="{{ $slabIndex }}" >
                                        <div class="slab-header">
                                            <h5 class="slab-title">
                                                Fare Slab
                                                <span class="slab-counter">#{{ $slabIndex + 1 }}</span>
                                            </h5>
                                            <button type="button" class="btn btn-danger remove-slab">
                                                <i class="fa fa-trash"></i> Remove
                                            </button>
                                        </div>

                                        <div class="slab-fields">
                                            @foreach($fareItems as $fare)
                                                <div class="form-group">
                                                    <label class="form-label">{{ ucfirst(str_replace('_', ' ', $fare->fare_name)) }}</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                @if(str_contains($fare->fare_name, 'fare'))
                                                                    {{ Hyvikk::get('currency') }}
                                                                @elseif(str_contains($fare->fare_name, 'km'))
                                                                    {{ Hyvikk::get('dis_format') }}
                                                                @else
                                                                    Min
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <input type="number" 
                                                        class="form-control editable-field"
                                                        data-id="{{ $fare->id }}"
                                                        data-field="{{ $fare->fare_name }}" 
                                                        value="{{ $fare->fare_value }}" 
                                                        min="0"
                                                        step="0.01"
                                                        required>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p>No slabs found for this vehicle type.</p>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="actions-container">
                            <button type="button" class="btn btn-primary add-slab">
                                <i class="fa fa-plus"></i> Add Slab
                            </button>
                            <button type="submit" class="btn btn-success save-all">
                                <i class="fa fa-save"></i> Save All Changes
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    @if(isset($_GET['tab']))
        $('.vehicle-tabs a[href="#{{ $_GET['tab'] }}"]').tab('show');
    @endif

    // Handle tab clicks
    $('.vehicle-tab').click(function(e) {
        e.preventDefault();
        $(this).addClass('active').siblings().removeClass('active');
        $($(this).attr('href')).addClass('active').siblings('.tab-pane').removeClass('active');
    });

    // Inline field editing and saving
    $(document).on('input', '.editable-field', function () {
    const field = $(this);
    const fareId = field.data('id'); // ID of the fare setting being updated
    const fieldName = field.data('field'); // Name of the field being updated
    const fieldValue = field.val(); // New value entered by the user

    // AJAX request to update the field
    $.ajax({
        url: '/admin/fare-settings/update',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}', // CSRF token for security
        },
        data: {
            id: fareId,
            field: fieldName,
            value: fieldValue,
        },
        success: function (response) {
            if (response.success) {
                // Optionally, show a success message
                console.log(response.message);
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function (xhr, status, error) {
            // Show a detailed error message for debugging
            console.error('AJAX Error:', xhr.responseText);
            alert('An error occurred while updating the field.');
        },
    });
});



$(document).on('click', '.add-slab', function() {
    const $tabPane = $(this).closest('.tab-pane');
    const $slabsContainer = $tabPane.find('.slabs-container');
    const $template = $slabsContainer.find('.slab-card').first().clone();
    
    // Clear IDs and values for new slab
    $template.find('[data-id]').removeAttr('data-id');
    $template.find('input').val('');
    
    // Update slab index
    const newIndex = $slabsContainer.find('.slab-card').length;
    $template.find('.slab-counter').text(`#${newIndex + 1}`);
    
    // Add delete handler for new slab
    $template.find('.remove-slab').click(function() {
        $(this).closest('.slab-card').remove();
    });
    
    $slabsContainer.append($template);
});




    // Save all changes
    $(document).on('click', '.save-all', function() {
    const $tabPane = $(this).closest('.tab-pane');
    const typeId = $tabPane.find('.vehicle-tab.active').data('type-id');
    const slabs = [];

    $tabPane.find('.slab-card').each(function(index) {
        const slabIndex = $(this).data('slab-index') || index; // Use existing or DOM index
        const slabData = {
            slab_index: slabIndex,
            updates: [],
            creates: []
        };

        $(this).find('.editable-field').each(function() {
            const fieldData = {
                name: $(this).data('field'),
                value: $(this).val()
            };

            if ($(this).data('id')) {
                slabData.updates.push({
                    id: $(this).data('id'),
                    value: fieldData.value
                });
            } else {
                slabData.creates.push(fieldData);
            }
        });

        slabs.push(slabData);
    });

    $.ajax({
        url: '/admin/fare-settings/save-all',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        data: {
            type_id: typeId,
            slabs: slabs
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            }
        }
    });
});

});
</script>
@endsection