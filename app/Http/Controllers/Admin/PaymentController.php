<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\CampusProgram;
use App\Models\PaymentItem;
use App\Models\Payments;
use App\Models\SchoolUnits;
use App\Models\Students;
use App\Models\StudentScholarship;
use Illuminate\Http\Request;
use Session;
use Redirect;

use Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{

    private $batch_id;

    public function __construct()
    {
        $this->batch_id = Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->id;
    }
    public function index(Request $request, $student_id)
    {
        $student = Students::find($student_id);
        $data['title'] = __('text.fee_collections_for', ['item'=>$student->name]);
        $data['student'] = $student;
        return view('admin.fee.payments.index')->with($data);
    }

    public function create(Request $request, $student_id)
    {
        // return 1000000;
        $student = Students::find($student_id);
        $data['student'] = $student;
        $data['scholarship'] = Helpers::instance()->getStudentScholarshipAmount($student_id);
        $data['total_fee'] = (int)$student->total();
        // $data['total_fee'] = CampusProgram::where('campus_id', $student->campus_id)->where('program_level_id', $student->program_id)->first()->payment_items()->first()->amount;
        $data['balance'] =  $student->bal($student_id);
        $data['title'] = __('text.collect_fee_for', ['item'=>$student->name]);

        // if ($data['balance'] == 0) {
        //     return redirect(route('admin.fee.collect'))->with('success', 'Student has already completed fee');
        // }
        
        if ($data['total_fee'] <= 0) {

            return redirect(route('admin.fee.collect'))->with('error', __('text.fee_not_set'));
        }
        return view('admin.fee.payments.create')->with($data);
    }

    public function edit(Request $request, $student_id, $id)
    {
        $student = Students::find($student_id);
        $data['student'] = $student;
        $data['payment'] = Payments::find($id);
        $data['title'] = __('text.collect_fee_for', ['item'=>$student->name]);
        return view('admin.fee.payments.edit')->with($data);
    }

    public function store(Request $request, $student_id)
    {

        // return $request->all();
        $student = Students::find($student_id);
        $total_fee = $student->total($student_id);
        $balance =  $student->bal($student_id);
        $debt = $student->debt(Helpers::instance()->getCurrentAccademicYear());
        // return $balance;
        $this->validate($request, [
            'item' =>  'required',
            'amount' => 'required',
            'date' => 'required|date',
        ]);
        if ($request->amount >  $balance) {
            $debt += -$request->amount + $balance;
            $amount = $balance;
        }
        else{
            $amount = $request->amount;
        }

        if ($request->reference_number == null || (Payments::where(['reference_number' => $request->reference_number])->count() == 0)) {
            # code...
            Payments::create([
                "payment_id" => $request->item,
                "student_id" => $student->id,
                "unit_id" => $student->class(Helpers::instance()->getYear())->id,
                "batch_id" => Helpers::instance()->getYear(),
                "amount" => $amount,
                "date" => $request->date,
                'reference_number' => $request->reference_number,
                'user_id' => auth()->user()->id,
                'debt' => $debt,
            ]);
            return back()->with('success', __('text.word_done'));
        }
        else{return back()->with('error', __('text.reference_already_exist'));}

    }

    public function update(Request $request, $student_id, $id)
    {
        $student = Students::find($student_id);
        $this->validate($request, [
            'item' =>  'required',
            'amount' => 'required',
        ]);
        $total_fee = $student->total($student_id);
        $paid =  $student->paid();
        if ($request->amount > $total_fee) {
            return back()->with('error', __('text.deposited_amount_exceeds_total_fee_amount'));
        }
        $p =  Payments::find($id);
        $new_balance = $paid - $p->amount;
        if(($new_balance + $request->amount) > $total_fee){
            return back()->with('error', __('text.deposited_amount_exceeds_total_fee_amount'));
        }
        $p->update([
            "payment_id" => $request->item,
            "amount" => $request->amount,
            "unit_id" => $student->class(Helpers::instance()->getYear())->id,
        ]);

        return redirect()->to(route('admin.fee.student.payments.index', $student_id))->with('success', __('text.word_done'));
    }

    public function destroy(Request $request, $student_id, $id)
    {
        $p =  Payments::find($id);
        $p->delete();
        return back()->with('success', __('text.word_done'));
    }

    // private function checkScholars($student_id)
    // {
    //     $scholar = DB::table('student_scholarships')
    //         ->join('students', 'students.id',  '=', 'student_scholarships.student_id')
    //         ->join('batches', 'batches.id', '=', 'student_scholarships.batch_id')
    //         ->join('scholarships', 'scholarships.id', '=', 'student_scholarships.scholarship_id')
    //         ->where('student_scholarships.student_id', $student_id)
    //         ->where('student_scholarships.batch_id', $this->batch_id)
    //         ->select('students.id', 'scholarships.amount')->first();
    //     return $scholar;
    // }

    // private function getBalanceFee($class_id)
    // {
    //     $tuition = DB::table('payment_items')
    //         ->join('school_units', 'school_units.id',  '=', 'payment_items.unit')
    //         ->join('batches', 'batches.id', '=', 'payment_items.year_id')
    //         ->where('payment_items.unit', $class_id)
    //         ->where('payment_items.year_id', $this->batch_id)
    //         ->select('payment_items.amount')->get()->toArray();
    //     return $tuition;
    // }

    // private function getStudentClass($student_id)
    // {
    //     $class_id = DB::table('student_classes')
    //         ->join('students', 'students.id',  '=', 'student_classes.student_id')
    //         ->join('school_units', 'school_units.id',  '=', 'student_classes.class_id')
    //         ->join('batches', 'batches.id', '=', 'student_classes.year_id')
    //         ->where('students.id', $student_id)
    //         ->where('batches.id', $this->batch_id)
    //         ->select('school_units.id')->first();
    //     return $class_id;
    // }

    public function print(Request $request, $student_id, $item_id)
    {
        # code...
    }
}
