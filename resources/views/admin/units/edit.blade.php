@extends('admin.layout')


@section('section')
    <div class="form-panel">
        <form class="cmxform form-horizontal style-form" method="post" action="{{route('admin.units.update', $id)}}">
            {{csrf_field()}}
            <input type="hidden" name="parent" value="{{$unit->parent_id}}">
            <input type="hidden" name="_method" value="put">
            <div class="form-group @error('type') has-error @enderror"">
                <label for="cname" class="control-label col-lg-2">Unit type (required)</label>
                <div class="col-lg-10">
                    <select class="form-control" name="type">
                        <option selected disabled>Select Unit Type</option>
                        @foreach(\App\Models\Unit::get() as $type)
                            <option {{ (old('type', $unit->unit_id) == $type->id)?'selected':''  }} value="{{$type->id}}">{{$type->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group @error('name') has-error @enderror"">
                <label for="name" class="control-label col-lg-2" >Name (required)</label>
                <div class="col-lg-10">
                    <input class=" form-control" name="name" value="{{old('name',$unit->name)}}" type="text" required />
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-offset-2 col-lg-10">
                    <button class="btn btn-xs btn-theme" type="submit">Save</button>
                    <a class="btn btn-xs btn-theme04" href="{{route('admin.units.index',[$parent_id])}}" type="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@stop
