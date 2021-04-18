@extends('admin.layout')

@section('section')
    <!-- page start-->

    <div class="col-sm-12">
        <p class="text-muted">
           <a href="{{route('admin.subjects.create')}}" class="btn btn-info btn-xs">Add Subjects</a>
        </p>

        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Coefficient</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($subjects as $k=>$subject)
                        <tr>
                            <td>{{ $k+1 }}</td>
                            <td>{{ $subject->name }}</td>
                            <td>{{ $subject->coef }}</td>
                            <td style="float: right;">
                                <a class="btn btn-xs btn-success" href="{{route('admin.subjects.edit',[$subject->id])}}"><i class="fa fa-edit"> Edit</i></a> |
                                <a onclick="event.preventDefault();
                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-xs m-2">Delete</a>
                                <form id="delete" action="{{route('admin.subjects.destroy',$subject->id)}}" method="POST" style="display: none;">
                                    @method('DELETE')
                                    {{ csrf_field() }}
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">
                    {{$subjects->links()}}
                </div>
            </div>
        </div>
    </div>
@endsection
