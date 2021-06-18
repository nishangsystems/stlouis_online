<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class IncomeController extends Controller
{
    private $select = [
        'name',
        'amount',
        'description',
        'user_id',
        'id',
        'income_date'
    ];
    /**
     * list all incomes of a school
     * I use the is of the current authenticated admin, to get the incomes for his/her school
     */
    public function index()
    {
        $user_id = Auth::id();
        $incomes = Income::where('user_id', $user_id)->select($this->select)->paginate(5);
        return view('admin.Income.index', compact('incomes'));
    }

    /**
     * show form to create an income for a school
     */
    public function create()
    {
        return view('admin.Income.create');
    }


    /**
     * store an income for a school
     * 
     * @param Illuminate\Http\Request
     * @return string
     */
    public function store(Request $request)
    {
        $this->validateData($request);
        $income = new Income();
        $income->name = $request->name;
        $income->amount = $request->amount;
        $income->description = $request->description;
        $income->user_id = Auth::id();
        $income->income_date = $request->income_date;
        $income->save();
        return redirect()->route('admin.income.index')->with('success', 'Success! Income saved successfully !');
    }

    /**
     * validate data to creat income
     *  @param Illuminate\Http\Request
     */
    private function validateData($request)
    {
        return $request->validate([
            'name' => 'required|max:255|string',
            'amount' => 'required|numeric',
            'description' => 'required|string',
            'income_date' => 'required'
        ]);
    }

    /**
     * show form to edit income
     * 
     */
    public function edit($id)
    {
        $income = Income::findOrFail($id);
        return view('admin.Income.edit', compact('income'));
    }

    /**
     * update an income of a school
     * 
     * @param int $id
     * @param Illuminate\Http\Request
     */
    public function update(Request $request, $id)
    {
        $this->validateData($request);
        $updated_income = Income::findOrFail($id)->update($request->all());
        return  redirect()->route('admin.income.index')->with('success', 'Income updated successfully !');
    }

    /**
     * delete an income of a school
     * 
     * @param int $id
     */
    public function destroy($id)
    {
        $deleted = Income::findOrFail($id)->delete();
        return back()->with('success', 'Student deleted successfully !');
    }

    /**
     * show details of an income
     * 
     * @param int $id
     */
    public function show($id)
    {
        $income = Income::findOrFail($id);
        return view('admin.Income.show', compact('income'));
    }
}