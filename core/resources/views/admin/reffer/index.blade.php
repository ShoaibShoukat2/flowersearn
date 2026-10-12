@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30">
  <div class="col-md-12">
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive--sm">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Plan') }}</th>
                            <th scope="col">{{ __('Type') }}</th>
                            <th scope="col">{{ __('Referral') }}</th>
                            <th scope="col">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reffers as $ref)
                        <tr>
                            <td>{{ $ref->plan?->name }}</td>
                            <td>{{ ucwords(str_replace('_',' ',$ref->type)) }}</td>
                            <td>
                                @foreach ($ref->level as $key => $lv)
                                    <div class="mb-3">
                                        <span class="badge badge--primary">{{ $lv . ' => ' . $ref->commision[$key] }} %</span>
                                    </div>
                                @endforeach
                            </td>

                            <td>
                                <button class="btn btn-outline--primary btn-sm edit"
                                    data-route="{{ route('admin.reffer.update', $ref->id) }}"
                                    data-referral="{{ $ref }}"
                                    >
                                    <i class="la la-pencil"></i> @lang('Edit')
                                </button>
                                <button class="btn btn-outline--danger btn-sm delete"
                                    data-route="{{ route('admin.reffer.delete', $ref->id) }}"
                                    >
                                    <i class="la la-trash"></i> @lang('Delete')
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  </div>
</div>

{{-- add modal --}}
<div class="modal fade" id="add" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.reffer.store') }}" method="post">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add Commission') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">{{ __('Plan') }}</label>
                        <select name="plan_id" id="" class="form-control">
                            @foreach ($plans as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="form-group">
                        <label for="">{{ __('Type') }}</label>
                        <select name="type" id="" class="form-control">
                            <option value="plan_subscribe_commission">Plan Subscribe</option>
                            <option value="ptc_view_commission">Ptc View</option>
                        </select>
                    </div>


                    <div class="input-group mb-3 mt-3 ml-auto ">
                        <input type="number" class="form-control counter" placeholder="How Many Field You Want"
                            required>

                        <button class="btn btn--primary input-group-text" type="button" id="generator">{{ __('Generate') }}</button>
                    </div>


                    <div class="append  ml-auto">

                    </div>


                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- delete modal --}}
<div class="modal fade" id="delete" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="" method="post">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Delete Commission') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 class="text-center">
                        Are you sure to Delete?
                    </h4>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--danger w-100">Yes, Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- update modal --}}
<div class="modal fade" id="edit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="" method="post">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('update Commission') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">{{ __('Plan') }}</label>
                        <select name="plan_id" id="plan" class="form-control">
                            @foreach ($plans as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="form-group">
                        <label for="">{{ __('Type') }}</label>
                        <select name="type" id="type" class="form-control">
                            <option value="plan_subscribe_commission">Plan Subscribe</option>
                            <option value="ptc_view_commission">Ptc View</option>
                        </select>
                    </div>


                    <div class="input-group mb-3 mt-3 ml-auto ">
                        <input type="number" class="form-control counterUpdate" placeholder="How Many Field You Want">

                        <button class="btn btn--primary input-group-text" type="button"
                            id="generatorUpdate">{{ __('Generate') }}</button>
                    </div>


                    <div class="appendUpdate  ml-auto">



                    </div>


                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>



{{-- <div class="modal fade" id="planModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"><span class="act"></span> @lang('Subscription Plan')</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
            </div>
            <form action="{{ route('admin.plan.save') }}" method="post">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">@lang('Name') </label>
                        <input type="text" class="form-control" name="name" placeholder="@lang('Plan Name')" required>
                    </div>
                    <div class="form-group">
                        <label for="tagline">@lang('Tagline') </label>
                        <input type="text" class="form-control" name="tagline" placeholder="@lang('Tagline')" required>
                    </div>
                    <div class="form-group">
                        <label for="price">@lang('Price') </label>
                        <div class="input-group">
                            <input type="text" class="form-control has-append" name="price" placeholder="@lang('Price of Plan')" required>
                            <div class="input-group-text">{{ $general->cur_text }}</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="ptc_rate">@lang('PTC Rate') </label>
                        <div class="input-group">
                            <input type="text" class="form-control has-append" name="ptc_rate" placeholder="@lang('Rate of 1 ptc')" required>
                            <div class="input-group-text">{{ $general->cur_text }}</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="daily_limit">@lang('Daily Ad Limit')</label>
                        <input type="number" class="form-control" name="daily_limit" placeholder="@lang('Daily Ad Limit')" required>
                    </div>
                    <div class="form-group">
                        <label for="daily_limit">@lang('Validity')</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="validity" placeholder="@lang('Validity')" required>
                            <div class="input-group-text">@lang('Days')</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="highlight">@lang('Highlight')</label>
                        <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="highlight">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div> --}}

<x-confirmation-modal />

@endsection
@push('breadcrumb-plugins')
    <button class="btn btn-outline--primary btn-sm add" data-id="0" data-act="Add" data-bs-toggle="modal" data-bs-target="#planModal"><i class="las la-plus"></i> @lang('Add New')</button>
@endpush


@push('script')

<script>
    'use strict';
    $('.create-invest').hide();
    $('.create-interest').hide();

    $(document).ready(function() {


        $('.add').on('click', function() {
            const modal = $('#add');

            modal.modal('show')
        })

        $('.delete').on('click', function() {
            const modal = $('#delete');
            let url = $(this).data('route')
            modal.find('form').attr('action', url);

            modal.modal('show')
        })


        $(document).on('click','.edit', function() {
            const modal = $('#edit');

            let data = $(this).data('referral')
            let url = $(this).data('route')


            modal.find('form').attr('action', url);

            modal.find("#plan option[value='"+ data.plan_id +"']").attr("selected", "selected");
            modal.find("#type option[value='"+ data.type +"']").attr("selected", "selected");




            var html = "";

            for (let i = 0; i < data.level.length; i++) {
                html += `

                    <div class="input-group mb-3 mt-3 ">
                        <input class="btn btn--primary input-group-text" type="text"  name=level[] value="${data.level[i]}" readonly>
                        <input type="number" required class="form-control" name=commision[]
                            placeholder="Commision" value="${data.commision[i]}">

                        <button class="btn btn--primary text-light no-drop input-group-text" type="button"
                            >%</button>
                        <button class="btn btn--danger text-white delete_invest input-group-text" type="button"
                            >X</button>
                    </div>
         `

            }


            $('.appendUpdate').html(html).hide().slideDown('slow');

            modal.modal('show')
        })







        $(document).on('click', '#generator', function() {


            var value = $('.counter').val();



            if (value > 20) {
                iziToast.error({
                    message: 'Max Limit of Refferal level is 20 ',
                    position: 'topRight'
                });

                return;
            }
            var viewHtml = "";

            for (let i = 0; i < value; i++) {
                viewHtml += `

                    <div class="input-group mb-3 mt-3 ">
                            <input class="btn btn--primary input-group-text" type="text"  name=level[] value="level ${i+1}" readonly>
                            <input type="number" required class="form-control" name=commision[]
                                placeholder="Commision">
                            <button class="btn btn--primary input-group-text text-light no-drop" type="button"
                                >%</button>
                            <button class="btn btn--danger input-group-text text-white delete_invest" type="button"
                                >X</button>

                    </div>
         `
                $('.append').html(viewHtml).hide().slideDown('slow');


            }


        });


        $(document).on('click', '#generatorUpdate', function() {



            var value = $('.counterUpdate').val();



            if (value > 20) {
                iziToast.error({
                    message: 'Max Limit of Refferal level is 20 ',
                    position: 'topRight'
                });

                return;
            }
            var viewHtml = "";

            for (let i = 0; i < value; i++) {
                viewHtml += `

                <div class="input-group mb-3 mt-3 ">
                    <input class="btn btn--primary" type="text"  name=level[] value="level ${i+1}" readonly>
                    <input type="number" required class="form-control" name=commision[]
                        placeholder="Commision">
                    <button class="btn btn--primary text-light no-drop" type="button"
                        >%</button>
                    <button class="btn btn--danger text-white delete_invest" type="button"
                        >X</button>
                </div>
                `
                $('.appendUpdate').html(viewHtml).hide().slideDown('slow');


            }


        });

        $(document).on('click', '.delete_invest', function() {
            $(this).closest('.input-group').remove();

            var count = $('.append_invest').children().length;

            if (count == 0) {
                $('.create-invest').hide();
            }

        });
        // $('#interest').on('click', function() {
        //     var value = $('.interest_commision').val();
        //     var viewHtml = "";

        //     if (value > 20) {
        //         iziToast.error({
        //             message: 'Max Limit of Refferal level is 20 ',
        //             position: 'topRight'
        //         });

        //         return;
        //     }


        //     for (let i = 0; i < value; i++) {
        //         viewHtml += `

        //         <div class="input-group mb-3 mt-3 ">
        //             <div class="input-group-prepend">
        //                 <input class="btn btn-success" type="text"  name="level[]"  value="level ${i+1}" readonly>
        //             </div>
        //             <input type="number" name=commision[] class="form-control"
        //                 placeholder="Commision" min="0" required>

        //             <div class="input-group-append">
        //                 <button class="btn btn-success text-light no-drop" type="button"
        //                     >%</button>
        //                 <button class="btn btn-danger text-white delete_interest" type="button"
        //                     >X</button>
        //             </div>


        //         </div>

        //         `
        //         $('.append_interest').html(viewHtml).hide().slideDown('slow');
        //         $('.interest_commision').val('');
        //         $('.create-interest').show();
        //     }


        // });
        // $(document).on('click', '.delete_interest', function() {
        //     $(this).closest('.input-group').remove();
        //     var count = $('.append_interest').children().length;

        //     if (count == 0) {
        //         $('.create-interest').hide();
        //     }
        // });
    });
</script>


{{-- <script>
    (function($){
        "use strict";
        $('.planBtn').on('click', function() {
            var modal = $('#planModal');
            modal.find('.act').text($(this).data('act'));
            modal.find('input[name=id]').val($(this).data('id'));
            modal.find('input[name=name]').val($(this).data('name'));
            modal.find('input[name=tagline]').val($(this).data('tagline'));
            modal.find('input[name=price]').val($(this).data('price'));
            modal.find('input[name=ptc_rate]').val($(this).data('ptc_rate'));
            modal.find('input[name=daily_limit]').val($(this).data('daily_limit'));
            modal.find('input[name=validity]').val($(this).data('validity'));
            modal.find('input[name=highlight]').bootstrapToggle($(this).data('highlight') == 1 ? 'on' : 'off');
            modal.find('select[name=ref_level]').val($(this).data('ref_level'));
            if($(this).data('id') == 0){
                modal.find('form')[0].reset();
            }
            modal.modal('show');
        });
    })(jQuery);
</script> --}}
@endpush
