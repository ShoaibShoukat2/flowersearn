@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form id="levelsForm" method="POST">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">@lang('Select Plan')</label>
                            <select class="form-control" name="plan_id" id="planSelect" required>
                                <option value="">@lang('Select a plan')</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive--sm">
                        <table class="table table--light style--two" id="levelsTable">
                            <thead>
                                <tr>
                                    <th>@lang('Level')</th>
                                    <th>@lang('No. of Referrals')</th>
                                    <th>@lang('Daily Clicks Limit')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Levels loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group mt-3">
                        <button type="button" class="btn btn--success" id="addLevelBtn">
                            <i class="las la-plus"></i> @lang('Add Level')
                        </button>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Save Changes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(document).ready(function() {
    let levelCounter = 1;
        
    // Load levels when plan is selected
    $('#planSelect').on('change', function() {
        const planId = $(this).val();
        if (!planId) return;
        
        $.ajax({
            url: "{{ route('admin.setting.get-levels', '') }}/" + planId,
            type: 'GET',
            success: function(response) {
                
                levelCounter = 1;
                const tbody = $('#levelsTable tbody');
                tbody.empty();
            
                const levels = response.levels;
                
                console.log(levels);
                
                // Check if levels exist and is not empty
                if (!levels || $.isEmptyObject(levels)) {
                    return;
                    alert('none');
                    
                }else{
            
                    const levels = response.levels || {};
                    const orderedKeys = Object.keys(levels).sort(function(a, b) {
                        return Number(a) - Number(b);
                    });

                    orderedKeys.forEach(function(key) {
                        const levelData = levels[key] || {};
                        addLevelRow(levelData.referrals_required, levelData.daily_limit);
                    });
                }
                
            },
            error: function(xhr) {
                notify('error', xhr.responseJSON?.message || 'Failed to load levels');
            }
        });
    });
        
    // Add new level row
    $('#addLevelBtn').click(function() {
        if (!$('#planSelect').val()) {
            notify('error', 'Please select a plan first');
            return;
        }
        addLevelRow();
    });

    // Remove level row
    $('#levelsTable').on('click', '.remove-level', function() {
        $(this).closest('tr').remove();
        renumberLevels();
    });

    // Form submission
    $('#levelsForm').on('submit', function(e) {
        
        e.preventDefault();
        
        const formData = {
            plan_id: $('#planSelect').val(),
            levels: {}
        };

        $('#levelsTable tbody tr').each(function(index) {
            const levelNumber = index + 1;
            formData.levels[levelNumber] = {
                referrals_required: $(this).find('[name*="referrals_required"]').val(),
                daily_limit: $(this).find('[name*="daily_limit"]').val()
            };
        });
        
        // Convert empty object to empty array if no levels
        if (Object.keys(formData.levels).length === 0) {
            formData.levels = {};
        }
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('admin.setting.update-levels') }}",
            type: 'POST',
            data: formData,
            success: function(response) {
                notify('success', response.message);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).forEach(error => {
                        notify('error', error[0]);
                    });
                } else {
                    notify('error', xhr.responseJSON?.message || 'Failed to save levels');
                }
            }
        });
    });

    function addLevelRow(referrals = '', income = '') {
        const newRow = `
            <tr>
                <td>
                    <input type="text" class="form-control" value="Level ${levelCounter}" readonly>
                </td>
                <td>
                    <input type="number" class="form-control" name="levels[${levelCounter}][referrals_required]" 
                        value="${referrals}" min="1" required>
                </td>
                <td>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" 
                            name="levels[${levelCounter}][daily_limit]" 
                            value="${income}" min="0" required>
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn--danger remove-level">
                        <i class="las la-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#levelsTable tbody').append(newRow);
        levelCounter++;
    }

    function renumberLevels() {
        levelCounter = 1;
        $('#levelsTable tbody tr').each(function() {
            $(this).find('td:first input').val(`Level ${levelCounter}`);
            $(this).find('[name*="referrals_required"]').attr('name', `levels[${levelCounter}][referrals_required]`);
            $(this).find('[name*="daily_limit"]').attr('name', `levels[${levelCounter}][daily_limit]`);
            levelCounter++;
        });
    }

    function notify(type, message) {
        alert(message);
    }
});
</script>
@endpush