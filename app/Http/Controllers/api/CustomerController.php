<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDOException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerController extends Controller
{
    public function allCustomers()
    {
        $customers = Customer::paginate(10);
        return response()->json($customers);
    }
    public function customer(Request $request)
    {
        try {
            $customer = Customer::findOrFail($request->id);
            return response()->json($customer);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Sorry customer not found'], 400);
        }
    }
    public function transfer(Request $request)
    {
        $from_customer = Customer::where('account_number', '=', $request->from_account)->get()->first();
        $to_customer = Customer::where('account_number', '=', $request->to_account)->get()->first();
        if ($from_customer == null) {
            return response()->json(['message' => 'Sender account number is wrong', 'code' => '100'], 400);
        } else if ($from_customer->id != $request->id) {
            return response()->json(['message' => 'Sender account number is changed', 'code' => '101'], 400);
        } else if ($to_customer == null) {
            return response()->json(['message' => 'Recipient account number is wrong', 'code' => '200'], 400);
        } else if ($from_customer->balance < $request->transfer_amount) {
            return response()->json(['message' => 'Sender account balance is less than transfered amount', 'code' => '201'], 400);
        }
        $from_balance = $from_customer->balance - $request->transfer_amount;
        $to_balance = $request->transfer_amount + $to_customer->balance;
        try {
            DB::beginTransaction();
            DB::update('update customers set balance = ? where account_number = ?', [$from_balance, $from_customer->account_number]);
            DB::update('update customers set balance = ? where account_number = ?', [$to_balance, $to_customer->account_number]);
            DB::insert('insert into transfers (from_customer, to_customer, amount, created_at) values (?, ?, ?, ?)', [$from_customer->id, $to_customer->id, $request->transfer_amount, now()]);
            DB::commit();
            return response()->json(['message' => 'Transfer done successfully']);
        } catch (PDOException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Sorry, transfer failed please try again'], 400);
        }
    }
    public function getTranfers(Request $request)
    {
        try {
            $customer = Customer::with('fromCustomer.fromCustomer', 'fromCustomer.toCustomer', 'toCustomer.fromCustomer', 'toCustomer.toCustomer')->findOrFail($request->id);
            return response()->json($customer);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Sorry customer not found'], 400);
        }
    }
}
