@extends('admin.printable2')
@section('section')
    <div style="line-height: 2.3rem; font-size:larger;">
        <span class="d-block py-2 text-capitalize">{!! __('text.admission_letter_phrase1', ['name'=>$name]) !!}</span>
        <span class="d-block py-2">{!! __('text.admission_letter_phrase2', ['campus'=>$campus, 'program'=>$program, 'matric'=>$matric, 'degree'=>$degree]) !!}</span>
        <ul style="list-style-type:disc;margin-block:2rem;">
            <li><span class="d-block py-2">{!! __('text.admission_letter_phrase3') !!}</span></li>
            <li><span class="d-block py-2">{!! __('text.admission_letter_phrase4', ['date1'=>$fee1_dateline, 'date2'=>$fee2_dateline]) !!}</span></li>
            <li><span class="d-block py-2">{!! __('text.admission_letter_phrase5') !!}</span></li>
        </ul>
        <div class="">
            <span class="d-block py-1">{!! __('text.admission_letter_phrase6', ['email'=>$help_email]) !!}</span><br>
            <table class="border table-stripped">
                <thead class="py-1 text-capitalize">
                    <th class="border-left border-right">{{ __('text.word_address') }}</th>
                    <th class="border-left border-right">{{ __('text.word_username') }}</th>
                    <th class="border-left border-right">{{ __('text.word_password') }}</th>
                </thead>
                <tbody>
                    <tr class="py-1 border-top border-bottom">
                        <td class="border-left border-right">https://students.stlouissystems.org/</td>
                        <td class="border-left border-right">{{ $matric }}</td>
                        <td class="border-left border-right">12345678</td>
                    </tr>
                </tbody>
            </table>
            
        </div>
        <table class="table-stripped">
            <thead class="py-1 text-capitalize">
                <th class=" text-center">{{ __('text.the_registrar') }}</th>
            </thead>
            <tbody>
                <tr class="py-1">
                    <td class="text-center">{{ $registrar }}</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection