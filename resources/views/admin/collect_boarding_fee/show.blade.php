@extends('admin.layout')

@section('section')

<div class="col-sm-12">
    <div class="col-lg-12">
        <div class="form-panel mb-3 ml-3 mt-5 mb-5">
            <form class="form-horizontal" role="form" method="POST" action="{{route('admin.boarding_fees_details', [$student_id, $id])}}">
                <div class="form-group @error('class_id') has-error @enderror ">
                    <div class="col-lg-2">
                        <select class="form-control section" name="section_id">
                            <option value="">Select Section</option>
                            @foreach($school_units as $key => $unit)
                            <option value="{{$unit->id}}">{{$unit->name}}</option>
                            @endforeach
                        </select>
                        @error('section_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-xs-1"></div>
                    <div class="col-lg-2">
                        <select class="form-control Circle" id="circle" name="circle">
                            <option value="">Select Circle</option>
                        </select>
                        @error('circle')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-xs-1"></div>
                    <div class="col-lg-2">
                        <select class="form-control class" name="class_id">
                            <option value="">Select Class</option>
                        </select>
                        @error('class_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-xs-1"></div>
                    <div class="col-lg-2">
                        <select class="form-control" name="batch_id">
                            <option value="">Select Year</option>
                            @foreach($years as $key => $year)
                            <option value="{{$year->id}}">{{$year->name}}</option>
                            @endforeach
                        </select>
                        @error('batch_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class=" col-lg-1 mb-1">
                        <button class="btn btn-xs btn-primary" id="submit" type="submit">Get Details</button>
                    </div>
                </div>
                @csrf
            </form>
        </div>
    </div>
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Amount Paid(CFA)</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paid_boarding_fee_details as $k=>$boarding_fee)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{number_format($boarding_fee->amount_payable)}}</td>
                        <td>{{date('jS F Y', strtotime($boarding_fee->created_at))}}</td>
                        @if($boarding_fee->status == 0)
                        <td>Incomplete</td>
                        @endif
                        @if($boarding_fee->status == 1)
                        <td>Completed</td>
                        @endif
                        <td class="d-flex justify-content-end  align-items-center">
                            <a class="btn btn-sm btn-primary" href="#"><i class="fa fa-printer">Print</i> </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                {{$paid_boarding_fee_details->links()}}
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $('.section').on('change', function() {

        let value = $(this).val();
        url = "{{route('admin.getSections', ':id')}}";
        search_url = url.replace(':id', value);
        $.ajax({
            type: 'GET',
            url: search_url,
            success: function(response) {
                let size = response.data.length;
                let data = response.data;
                let html = "";
                if (size > 0) {
                    html += '<div><select class="form-control"  name="' + data[0].id + '" >';
                    html += '<option selected> Select Circle</option>'
                    for (i = 0; i < size; i++) {
                        html += '<option value=" ' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    html += '</select></div>';
                } else {
                    html += '<div><select class="form-control"  >';
                    html += '<option selected> No data is avalaible</option>'
                    html += '</select></div>';
                }
                $('.circle').html(html);
            },
            error: function(e) {

            }
        })
    })
    $('#circle').on('change', function() {

        let value = $(this).val();
        url = "{{route('admin.getClasses', ':id')}}";
        search_url = url.replace(':id', value);
        $.ajax({
            type: 'GET',
            url: search_url,
            success: function(response) {
                let size = response.data.length;
                let data = response.data;
                let html = "";
                if (size > 0) {
                    html += '<div><select class="form-control"  name="' + data[0].id + '" >';
                    html += '<option selected> Select Class</option>'
                    for (i = 0; i < size; i++) {
                        html += '<option value=" ' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    html += '</select></div>';
                } else {
                    html += '<div><select class="form-control"  >';
                    html += '<option selected> No data is avalaible</option>'
                    html += '</select></div>';
                }
                $('.class').html(html);
            },
            error: function(e) {

            }
        })
    })
</script>
@endsection
