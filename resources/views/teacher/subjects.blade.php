@extends('teacher.layout')
@section('section')
    <!-- page start-->

    <div class="col-sm-12">
        <p class="text-muted">
            <h4>My Subjects</h4>
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
                            <td>{{ $subject->subject->subject->name }}</td>
                            <td>{{ $subject->subject->subject->coef }}</td>
                            <td style="float: right;">
                                <a class="btn btn-xs btn-primary" href="{{route('user.result', [$subject->id])}}">Result</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection