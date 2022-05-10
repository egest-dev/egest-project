<?php

namespace App\Http\Controllers;

use App\Exports\DeliveryNoteExport;
use App\Exports\InvoiceExport;
use App\Exports\ProposalExport;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\CustomField;
use App\Models\DeliveryNote;
use App\Models\DeliveryNotePayment;
use App\Models\DeliveryNoteProduct;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\InvoiceProduct;
use App\Models\Mail\CustomerInvoiceSend;
use App\Models\Mail\DeliveryNotePaymentCreate;
use App\Models\Mail\DeliveryNoteSend;
use App\Models\Mail\InvoicePaymentCreate;
use App\Models\Mail\InvoiceSend;
use App\Models\Mail\PaymentReminder;
use App\Models\Mail\ProposalSend;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
use App\Models\Proposal;
use App\Models\ProposalProduct;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class DeliveryNoteController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage delivery note')) {

            $customer = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $customer->prepend('All', '');

            $status = DeliveryNote::$statues;

            $query = DeliveryNote::where('created_by', '=', \Auth::user()->creatorId());

            if (!empty($request->customer)) {
                $query->where('id', '=', $request->customer);
            }
            if (!empty($request->issue_date)) {
                $date_range = explode(' - ', $request->issue_date);
                $query->whereBetween('issue_date', $date_range);
            }

            if (!empty($request->status)) {
                $query->where('status', '=', $request->status);
            }
            $deliveryNotes = $query->get();

            return view('deliverynote.index', compact('deliveryNotes', 'customer', 'status'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create($customerId)
    {

        if (\Auth::user()->can('create delivery note')) {
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'invoice')->get();
            $delivery_note_number = \Auth::user()->deliveryNoteNumberFormat($this->deliveryNoteNumber());
            $customers = Customer::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $customers->prepend('Select Customer', '');
            $category = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())->where('type', 1)->get()->pluck('name', 'id');
            $category->prepend('Select Category', '');
            $product_services = ProductService::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $product_services->prepend('--', '');

            return view('deliverynote.create', compact('customers', 'delivery_note_number', 'product_services', 'category', 'customFields', 'customerId'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function customer(Request $request)
    {
        $customer = Customer::where('id', '=', $request->id)->first();

        return view('invoice.customer_detail', compact('customer'));
    }

    public function product(Request $request)
    {

        $data['product'] = $product = ProductService::find($request->product_id);
        $data['unit'] = (!empty($product->unit())) ? $product->unit()->name : '';
        $data['taxRate'] = $taxRate = !empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0;
        $data['taxes'] = !empty($product->tax_id) ? $product->tax($product->tax_id) : 0;
        $salePrice = $product->sale_price;
        $quantity = 1;
        $taxPrice = ($taxRate / 100) * ($salePrice * $quantity);
        $data['totalAmount'] = ($salePrice * $quantity);

        return json_encode($data);
    }

    public function store(Request $request)
    {

        if (\Auth::user()->can('create delivery note')) {
            $validator = \Validator::make(
                $request->all(), [
                    'customer_id' => 'required',
                    'issue_date' => 'required',
                    'due_date' => 'required',
                    'category_id' => 'required',
                    'items' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $status = DeliveryNote::$statues;

            $deliverNote = new DeliveryNote();
            $deliverNote->invoice_id = $this->deliveryNoteNumber() ?? 1;
            $deliverNote->customer_id = $request->customer_id;
            $deliverNote->status = 0;
            $deliverNote->issue_date = $request->issue_date;
            $deliverNote->due_date = $request->due_date;
            $deliverNote->category_id = $request->category_id;
            $deliverNote->ref_number = $request->ref_number;
            $deliverNote->discount_apply = isset($request->discount_apply) ? 1 : 0;
            $deliverNote->created_by = \Auth::user()->creatorId();
            $deliverNote->save();
            CustomField::saveData($deliverNote, $request->customField);
            $products = $request->items;

            for ($i = 0; $i < count($products); $i++) {
                $deliveryNoteProduct = new DeliveryNoteProduct();
                $deliveryNoteProduct->invoice_id = $deliverNote->id;
                $deliveryNoteProduct->product_id = $products[$i]['item'];
                $deliveryNoteProduct->quantity = $products[$i]['quantity'];
                $deliveryNoteProduct->tax = $products[$i]['tax'];
                $deliveryNoteProduct->discount = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                $deliveryNoteProduct->price = $products[$i]['price'];
                $deliveryNoteProduct->description = $products[$i]['description'];
                $deliveryNoteProduct->save();

                //inventory management (Quantity)
                Utility::total_quantity('minus', $deliveryNoteProduct->quantity, $deliveryNoteProduct->product_id);


                //Slack Notification
//                $setting = Utility::settings(\Auth::user()->creatorId());
//                if (isset($setting['invoice_notification']) && $setting['invoice_notification'] == 1) {
//                    $msg = __("New Invoice") . ' ' . \Auth::user()->deliveryNoteNumberFormat($invoice->invoice_id) . ' ' . __("created by") . ' ' . \Auth::user()->name . '.';
//                    Utility::send_slack_msg($msg);
//                }

                //Telegram Notification
//                $setting = Utility::settings(\Auth::user()->creatorId());
//                if (isset($setting['telegram_invoice_notification']) && $setting['telegram_invoice_notification'] == 1) {
//                    $msg = __("New Invoice") . ' ' . \Auth::user()->deliveryNoteNumberFormat($invoice->invoice_id) . ' ' . __("created by") . ' ' . \Auth::user()->name . '.';
//                    Utility::send_telegram_msg($msg);
//                }

            }

            //Twilio Notification
//            $setting = Utility::settings(\Auth::user()->creatorId());
//            $customer = Customer::find($request->customer_id);
//            if (isset($setting['twilio_invoice_notification']) && $setting['twilio_invoice_notification'] == 1) {
//                $msg = __("New Invoice") . ' ' . \Auth::user()->deliveryNoteNumberFormat($invoice->invoice_id) . ' ' . __("created by") . ' ' . \Auth::user()->name . '.';
//                Utility::send_twilio_msg($customer->contact, $msg);
//            }


            return redirect()->route('delivery-note.index', $deliverNote->id)->with('success', __('Delivery Note successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($ids)
    {
        if (\Auth::user()->can('edit delivery note')) {
            $id = Crypt::decrypt($ids);
            $deliveryNote = DeliveryNote::find($id);

            $delivery_number = \Auth::user()->deliveryNoteNumberFormat($deliveryNote->invoice_id);
            $customers = Customer::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $category = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())->where('type', 1)->get()->pluck('name', 'id');
            $category->prepend('Select Category', '');
            $product_services = ProductService::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $deliveryNote->customField = CustomField::getData($deliveryNote, 'invoice');
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'invoice')->get();

            return view('deliverynote.edit', compact('customers', 'product_services', 'deliveryNote', 'delivery_number', 'category', 'customFields'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, DeliveryNote $deliveryNote)
    {
        if (\Auth::user()->can('edit delivery note')) {
            if ($deliveryNote->created_by == \Auth::user()->creatorId()) {
                $validator = \Validator::make(
                    $request->all(), [
                        'customer_id' => 'required',
                        'issue_date' => 'required',
                        'due_date' => 'required',
                        'category_id' => 'required',
                        'items' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('invoice.index')->with('error', $messages->first());
                }
                $deliveryNote->customer_id = $request->customer_id;
                $deliveryNote->issue_date = $request->issue_date;
                $deliveryNote->due_date = $request->due_date;
                $deliveryNote->ref_number = $request->ref_number;
                $deliveryNote->discount_apply = isset($request->discount_apply) ? 1 : 0;
                $deliveryNote->category_id = $request->category_id;
                $deliveryNote->save();
                CustomField::saveData($deliveryNote, $request->customField);
                $products = $request->items;

                for ($i = 0; $i < count($products); $i++) {
                    $deliveryNoteProduct = DeliveryNoteProduct::find($products[$i]['id']);

                    //inventory management (Quantity)
                    Utility::total_quantity('minus', $deliveryNoteProduct->quantity, $deliveryNoteProduct->product_id);


                    if ($deliveryNoteProduct == null) {
                        $deliveryNoteProduct = new DeliveryNoteProduct();
                        $deliveryNoteProduct->invoice_id = $deliveryNote->id;
                    }

                    if (isset($products[$i]['item'])) {
                        $deliveryNoteProduct->product_id = $products[$i]['item'];
                    }

                    $deliveryNoteProduct->quantity = $products[$i]['quantity'];
                    $deliveryNoteProduct->tax = $products[$i]['tax'];
                    $deliveryNoteProduct->discount = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                    $deliveryNoteProduct->price = $products[$i]['price'];
                    $deliveryNoteProduct->description = $products[$i]['description'];
                    $deliveryNoteProduct->save();

                    //inventory management (Quantity)
                    Utility::total_quantity('plus', $products[$i]['quantity'], $deliveryNoteProduct->product_id);

                }

                return redirect()->route('delivery-note.index')->with('success', __('Delivery Note successfully updated.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function deliveryNoteNumber()
    {
        $latest = DeliveryNote::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->invoice_id + 1;
    }

    public function show($ids)
    {
        if (\Auth::user()->can('show delivery note')) {
            $id = Crypt::decrypt($ids);
            $deliverNote = DeliveryNote::find($id);
            if ($deliverNote->created_by == \Auth::user()->creatorId()) {
//                $invoicePayment = InvoicePayment::where('invoice_id', $invoice->id)->first();

                $customer = $deliverNote->customer;
                $iteams = $deliverNote->items;

                $user = \Auth::user();
                $deliverNote->customField = CustomField::getData($deliverNote, 'invoice');
                $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'invoice')->get();

                return view('deliverynote.view', compact('deliverNote', 'customer', 'iteams', 'customFields', 'user'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(DeliveryNote $deliveryNote, Request $request)
    {

        if (\Auth::user()->can('delete delivery note')) {
            if ($deliveryNote->created_by == \Auth::user()->creatorId()) {
                foreach ($deliveryNote->payments as $deliveryNotes) {
                    Utility::bankAccountBalance($deliveryNotes->account_id, $deliveryNotes->amount, 'debit');
                    $deliveryNotes->delete();
                }
                $deliveryNote->delete();
                if ($deliveryNote->customer_id != 0) {
                    Utility::userBalance('customer', $deliveryNote->customer_id, $deliveryNote->getTotal(), 'debit');
                }

                DeliveryNoteProduct::where('invoice_id', '=', $deliveryNote->id)->delete();

                return redirect()->route('delivery-note.index')->with('success', __('Delivery Note successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function productDestroy(Request $request)
    {

        if (\Auth::user()->can('delete invoice product')) {
            InvoiceProduct::where('id', '=', $request->id)->delete();

            return redirect()->back()->with('success', __('Bill product successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customerInvoice(Request $request)
    {
        if (\Auth::user()->can('manage customer invoice')) {

            $status = Invoice::$statues;

            $query = Invoice::where('customer_id', '=', \Auth::user()->id)->where('status', '!=', '0')->where('created_by', \Auth::user()->creatorId());

            if (!empty($request->issue_date)) {
                $date_range = explode(' - ', $request->issue_date);
                $query->whereBetween('issue_date', $date_range);
            }

            if (!empty($request->status)) {
                $query->where('status', '=', $request->status);
            }
            $invoices = $query->get();

            return view('invoice.index', compact('invoices', 'status'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function customerInvoiceShow($id)
    {
        $invoice = Invoice::where('id', $id)->first();
        $user = User::where('id', $invoice->created_by)->first();
        if ($invoice->created_by == $user->creatorId()) {
            $customer = $invoice->customer;
            $iteams = $invoice->items;
            if ($user->type == 'super admin') {
                return view('invoice.view', compact('invoice', 'customer', 'iteams', 'user'));
            } elseif ($user->type == 'company') {
                return view('invoice.customer_invoice', compact('invoice', 'customer', 'iteams', 'user'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function sent($id)
    {
        if (\Auth::user()->can('send delivery note')) {
            $deliveryNote = DeliveryNote::where('id', $id)->first();
            $deliveryNote->send_date = date('Y-m-d');
            $deliveryNote->status = 1;
            $deliveryNote->save();

            $customer = Customer::where('id', $deliveryNote->customer_id)->first();
            $deliveryNote->name = !empty($customer) ? $customer->name : '';
            $deliveryNote->invoice = \Auth::user()->deliveryNoteNumberFormat($deliveryNote->invoice_id);

            $deliveryNoteId = Crypt::encrypt($deliveryNote->id);
            $deliveryNote->url = route('delivery-note.pdf', $deliveryNoteId);

            Utility::userBalance('customer', $customer->id, $deliveryNote->getTotal(), 'credit');

            try {
                Mail::to($customer->email)->send(new DeliveryNoteSend($deliveryNote));

            } catch (\Exception $e) {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            return redirect()->back()->with('success', __('Delivery Note successfully sent.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function resent($id)
    {
        if (\Auth::user()->can('send delivery note')) {
            $deliveryNote = DeliveryNote::where('id', $id)->first();

            $customer = Customer::where('id', $deliveryNote->customer_id)->first();
            $deliveryNote->name = !empty($customer) ? $customer->name : '';
            $deliveryNote->invoice = \Auth::user()->deliveryNoteNumberFormat($deliveryNote->invoice_id);

            $deliveryNoteId = Crypt::encrypt($deliveryNote->id);
            $deliveryNote->url = route('delivery-note.pdf', $deliveryNoteId);

            try {
                Mail::to($customer->email)->send(new DeliveryNoteSend($deliveryNote));

            } catch (\Exception $e) {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            return redirect()->back()->with('success', __('Delivery Note successfully sent.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function payment($invoice_id)
    {
        if (\Auth::user()->can('create payment delivery note')) {
            $deliveryNote = DeliveryNote::where('id', $invoice_id)->first();

            $customers = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $categories = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $accounts = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('deliverynote.payment', compact('customers', 'categories', 'accounts', 'deliveryNote'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function createPayment(Request $request, $invoice_id)
    {


        if (\Auth::user()->can('create payment delivery note')) {
            $validator = \Validator::make(
                $request->all(), [
                    'date' => 'required',
                    'amount' => 'required',
                    'account_id' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $deliveryNotePayment = new DeliveryNotePayment();
            $deliveryNotePayment->invoice_id = $invoice_id;
            $deliveryNotePayment->date = $request->date;
            $deliveryNotePayment->amount = $request->amount;
            $deliveryNotePayment->account_id = $request->account_id;
            $deliveryNotePayment->payment_method = 0;
            $deliveryNotePayment->reference = $request->reference;
            $deliveryNotePayment->description = $request->description;
            if (!empty($request->add_receipt)) {
                $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
                $request->add_receipt->storeAs('app/public/uploads/payment', $fileName);
                $deliveryNotePayment->add_receipt = $fileName;
            }

            $deliveryNotePayment->save();

            $deliveryNote = DeliveryNote::where('id', $invoice_id)->first();
            $due = $deliveryNote->getDue();
            $total = $deliveryNote->getTotal();
            if ($deliveryNote->status == 0) {
                $deliveryNote->send_date = date('Y-m-d');
                $deliveryNote->save();
            }

            if ($due <= 0) {
                $deliveryNote->status = 4;
                $deliveryNote->save();
            } else {
                $deliveryNote->status = 3;
                $deliveryNote->save();
            }
            $deliveryNotePayment->user_id = $deliveryNote->customer_id;
            $deliveryNotePayment->user_type = 'Customer';
            $deliveryNotePayment->type = 'Partial';
            $deliveryNotePayment->created_by = \Auth::user()->id;
            $deliveryNotePayment->payment_id = $deliveryNotePayment->id;
            $deliveryNotePayment->category = 'Invoice';
            $deliveryNotePayment->account = $request->account_id;

            Transaction::addTransaction($deliveryNotePayment);
            $customer = Customer::where('id', $deliveryNote->customer_id)->first();


            $payment = new DeliveryNotePayment();
            $payment->name = $customer['name'];
            $payment->date = \Auth::user()->dateFormat($request->date);
            $payment->amount = \Auth::user()->priceFormat($request->amount);
            $payment->invoice = 'invoice ' . \Auth::user()->deliveryNoteNumberFormat($deliveryNote->invoice_id);
            $payment->dueAmount = \Auth::user()->priceFormat($deliveryNote->getDue());

            Utility::userBalance('customer', $deliveryNote->customer_id, $request->amount, 'debit');

            Utility::bankAccountBalance($request->account_id, $request->amount, 'credit');

            try {
                Mail::to($customer['email'])->send(new DeliveryNotePaymentCreate($payment));
            } catch (\Exception $e) {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            return redirect()->back()->with('success', __('Payment successfully added.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
        }

    }

    public function paymentDestroy(Request $request, $invoice_id, $payment_id)
    {

        if (\Auth::user()->can('delete payment invoice')) {
            $payment = DeliveryNotePayment::find($payment_id);

            DeliveryNotePayment::where('id', '=', $payment_id)->delete();

            $deliveryNote = DeliveryNote::where('id', $invoice_id)->first();
            $due = $deliveryNote->getDue();
            $total = $deliveryNote->getTotal();

            if ($due > 0 && $total != $due) {
                $deliveryNote->status = 3;

            } else {
                $deliveryNote->status = 2;
            }

            $deliveryNote->save();
            $type = 'Partial';
            $user = 'Customer';
            Transaction::destroyTransaction($payment_id, $type, $user);

            Utility::userBalance('customer', $deliveryNote->customer_id, $payment->amount, 'credit');

            Utility::bankAccountBalance($payment->account_id, $payment->amount, 'debit');

            return redirect()->back()->with('success', __('Payment successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function paymentReminder($invoice_id)
    {
        $deliveryNote = DeliveryNote::find($invoice_id);
        $customer = Customer::where('id', $deliveryNote->customer_id)->first();
        $deliveryNote->dueAmount = \Auth:: user()->priceFormat($deliveryNote->getDue());
        $deliveryNote->name = $customer['name'];
        $deliveryNote->date = \Auth::user()->dateFormat($deliveryNote->send_date);
        $deliveryNote->invoice = \Auth::user()->deliveryNoteNumberFormat($deliveryNote->invoice_id);

        try {
            Mail::to($customer['email'])->send(new PaymentReminder($deliveryNote));
        } catch (\Exception $e) {
            $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
        }
        //Twilio Notification
        $setting = Utility::settings(\Auth::user()->creatorId());
        $customer = Customer::find($deliveryNote->customer_id);
        if (isset($setting['twilio_reminder_notification']) && $setting['twilio_reminder_notification'] == 1) {
            $msg = __("New Payment Reminder of ") . ' ' . \Auth::user()->deliveryNoteNumberFormat($deliveryNote->invoice_id) . ' ' . __("created by") . ' ' . \Auth::user()->name . '.';
            Utility::send_twilio_msg($customer->contact, $msg);
        }

        return redirect()->back()->with('success', __('Payment reminder successfully send.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
    }

    public function customerInvoiceSend($invoice_id)
    {
        return view('customer.invoice_send', compact('invoice_id'));
    }

    public function customerInvoiceSendMail(Request $request, $invoice_id)
    {
        $validator = \Validator::make(
            $request->all(), [
                'email' => 'required|email',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $email = $request->email;
        $invoice = Invoice::where('id', $invoice_id)->first();

        $customer = Customer::where('id', $invoice->customer_id)->first();
        $invoice->name = !empty($customer) ? $customer->name : '';
        $invoice->invoice = \Auth::user()->deliveryNoteNumberFormat($invoice->invoice_id);

        $invoiceId = Crypt::encrypt($invoice->id);
        $invoice->url = route('invoice.pdf', $invoiceId);

        try {
            Mail::to($email)->send(new CustomerInvoiceSend($invoice));
        } catch (\Exception $e) {
            $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
        }

        return redirect()->back()->with('success', __('Invoice successfully sent.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));

    }

    public function shippingDisplay(Request $request, $id)
    {
        $deliveryNote = DeliveryNote::find($id);

        if ($request->is_display == 'true') {
            $deliveryNote->shipping_display = 1;
        } else {
            $deliveryNote->shipping_display = 0;
        }
        $deliveryNote->save();

        return redirect()->back()->with('success', __('Shipping address status successfully changed.'));
    }

    public function duplicate($invoice_id)
    {
        if (\Auth::user()->can('duplicate delivery note')) {
            $deliveryNote = DeliveryNote::where('id', $invoice_id)->first();
            $duplicateDeliveryNote = new Invoice();
            $duplicateDeliveryNote->invoice_id = $this->deliveryNoteNumber();
            $duplicateDeliveryNote->customer_id = $deliveryNote['customer_id'];
            $duplicateDeliveryNote->issue_date = date('Y-m-d');
            $duplicateDeliveryNote->due_date = $deliveryNote['due_date'];
            $duplicateDeliveryNote->send_date = null;
            $duplicateDeliveryNote->category_id = $deliveryNote['category_id'];
            $duplicateDeliveryNote->ref_number = $deliveryNote['ref_number'];
            $duplicateDeliveryNote->status = 0;
            $duplicateDeliveryNote->shipping_display = $deliveryNote['shipping_display'];
            $duplicateDeliveryNote->created_by = $deliveryNote['created_by'];
            $duplicateDeliveryNote->save();

            if ($duplicateDeliveryNote) {
                $deliveryNoteProduct = DeliveryNoteProduct::where('invoice_id', $invoice_id)->get();
                foreach ($deliveryNoteProduct as $product) {
                    $duplicateProduct = new DeliveryNoteProduct();
                    $duplicateProduct->invoice_id = $duplicateDeliveryNote->id;
                    $duplicateProduct->product_id = $product->product_id;
                    $duplicateProduct->quantity = $product->quantity;
                    $duplicateProduct->tax = $product->tax;
                    $duplicateProduct->discount = $product->discount;
                    $duplicateProduct->price = $product->price;
                    $duplicateProduct->save();
                }
            }

            return redirect()->back()->with('success', __('Delivery Note duplicate successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function previewInvoice($template, $color)
    {
        $objUser = \Auth::user();
        $settings = Utility::settings();
        $deliveryNote = new DeliveryNote();

        $customer = new \stdClass();
        $customer->email = '<Email>';
        $customer->shipping_name = '<Customer Name>';
        $customer->shipping_country = '<Country>';
        $customer->shipping_state = '<State>';
        $customer->shipping_city = '<City>';
        $customer->shipping_phone = '<Customer Phone Number>';
        $customer->shipping_zip = '<Zip>';
        $customer->shipping_address = '<Address>';
        $customer->billing_name = '<Customer Name>';
        $customer->billing_country = '<Country>';
        $customer->billing_state = '<State>';
        $customer->billing_city = '<City>';
        $customer->billing_phone = '<Customer Phone Number>';
        $customer->billing_zip = '<Zip>';
        $customer->billing_address = '<Address>';

        $totalTaxPrice = 0;
        $taxesData = [];

        $items = [];
        for ($i = 1; $i <= 3; $i++) {
            $item = new \stdClass();
            $item->name = 'Item ' . $i;
            $item->quantity = 1;
            $item->tax = 5;
            $item->discount = 50;
            $item->price = 100;

            $taxes = [
                'Tax 1',
                'Tax 2',
            ];

            $itemTaxes = [];
            foreach ($taxes as $k => $tax) {
                $taxPrice = 10;
                $totalTaxPrice += $taxPrice;
                $itemTax['name'] = 'Tax ' . $k;
                $itemTax['rate'] = '10 %';
                $itemTax['price'] = '$10';
                $itemTaxes[] = $itemTax;
                if (array_key_exists('Tax ' . $k, $taxesData)) {
                    $taxesData['Tax ' . $k] = $taxesData['Tax 1'] + $taxPrice;
                } else {
                    $taxesData['Tax ' . $k] = $taxPrice;
                }
            }
            $item->itemTax = $itemTaxes;
            $items[] = $item;
        }

        $deliveryNote->invoice_id = 1;
        $deliveryNote->issue_date = date('Y-m-d H:i:s');
        $deliveryNote->due_date = date('Y-m-d H:i:s');
        $deliveryNote->itemData = $items;

        $deliveryNote->totalTaxPrice = 60;
        $deliveryNote->totalQuantity = 3;
        $deliveryNote->totalRate = 300;
        $deliveryNote->totalDiscount = 10;
        $deliveryNote->taxesData = $taxesData;
        $deliveryNote->customField = [];
        $customFields = [];

        $preview = 1;
        $color = '#' . $color;
        $font_color = Utility::getFontColor($color);

        $logo = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo');
        $img = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-invoice.png'));

        return view('invoice.templates.' . $template, compact('deliveryNote', 'preview', 'color', 'img', 'settings', 'customer', 'font_color', 'customFields'));
    }

    public function invoice($invoice_id)
    {
        $settings = Utility::settings();

        $deliveryNoteId = Crypt::decrypt($invoice_id);
        $deliveryNote = DeliveryNote::where('id', $deliveryNoteId)->first();

        $data = DB::table('settings');
        $data = $data->where('created_by', '=', $deliveryNote->created_by);
        $data1 = $data->get();

        foreach ($data1 as $row) {
            $settings[$row->name] = $row->value;
        }

        $customer = $deliveryNote->customer;
        $items = [];
        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate = 0;
        $totalDiscount = 0;
        $taxesData = [];
        foreach ($deliveryNote->items as $product) {
            $item = new \stdClass();
            $item->name = !empty($product->product()) ? $product->product()->name : '';
            $item->quantity = $product->quantity;
            $item->tax = $product->tax;
            $item->discount = $product->discount;
            $item->price = $product->price;
            $item->description = $product->description;

            $totalQuantity += $item->quantity;
            $totalRate += $item->price;
            $totalDiscount += $item->discount;

            $taxes = Utility::tax($product->tax);

            $itemTaxes = [];
        //     if (!empty($item->tax)) {
        //         foreach ($taxes as $tax) {
        //             $taxPrice = Utility::taxRate($tax->rate, $item->price, $item->quantity) ?? null;
        //             $totalTaxPrice += $taxPrice;

        //             $itemTax['name'] = $tax->name;
        //             $itemTax['rate'] = $tax->rate . '%';
        //             $itemTax['price'] = Utility::priceFormat($settings, $taxPrice);
        //             $itemTaxes[] = $itemTax;


        //             if (array_key_exists($tax->name, $taxesData)) {
        //                 $taxesData[$tax->name] = $taxesData[$tax->name] + $taxPrice;
        //             } else {
        //                 $taxesData[$tax->name] = $taxPrice;
        //             }

        //         }
        //         $item->itemTax = $itemTaxes;
        //     } else {
        //         $item->itemTax = [];
        //     }
        //     $items[] = $item;
        }

        $deliveryNote->itemData = $items;
        // $deliveryNote->totalTaxPrice = $totalTaxPrice;
        $deliveryNote->totalQuantity = $totalQuantity;
        $deliveryNote->totalRate = $totalRate;
        $deliveryNote->totalDiscount = $totalDiscount;
        // $deliveryNote->taxesData = $taxesData;
        $deliveryNote->customField = CustomField::getData($deliveryNote, 'deliverynote');
        $customFields = [];
        if (!empty(\Auth::user())) {
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'deliverynote')->get();
        }


        //Set your logo
        $logo = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo');
        $img = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));

        if ($deliveryNote) {
            $color = '#' . $settings['delivery_note_color'];
            $font_color = Utility::getFontColor($color);

            return view('deliverynote.templates.' . $settings['delivery_note_template'], compact('deliveryNote', 'color', 'settings', 'customer', 'img', 'font_color', 'customFields'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function saveTemplateSettings(Request $request)
    {
        $post = $request->all();
        unset($post['_token']);

        if (isset($post['invoice_template']) && (!isset($post['invoice_color']) || empty($post['invoice_color']))) {
            $post['invoice_color'] = "ffffff";
        }

        foreach ($post as $key => $data) {
            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ', [
                    $data,
                    $key,
                    \Auth::user()->creatorId(),
                ]
            );
        }

        return redirect()->back()->with('success', __('Invoice Setting updated successfully'));
    }

    public function items(Request $request)
    {
        $items = InvoiceProduct::where('invoice_id', $request->invoice_id)->where('product_id', $request->product_id)->first();

        return json_encode($items);
    }

    public function invoiceLink($invoiceId)
    {
        $id = Crypt::decrypt($invoiceId);
        $invoice = Invoice::find($id);

        $user_id = $invoice->created_by;
        $user = User::find($user_id);
        $invoicePayment = InvoicePayment::where('invoice_id', $invoice->id)->first();

        $customer = $invoice->customer;

        $iteams = $invoice->items;

        $invoice->customField = CustomField::getData($invoice, 'invoice');

        $customFields = CustomField::where('module', '=', 'invoice')->get();

        $company_payment_setting = Utility::getCompanyPaymentSetting($user_id);

        return view('invoice.customer_invoice', compact('invoice', 'customer', 'iteams', 'invoicePayment', 'customFields', 'user', 'company_payment_setting'));
    }

    public function export()
    {
        $name = 'deliveryNote_' . date('Y-m-d i:h:s');
        $data = Excel::download(new DeliveryNoteExport(), $name . '.xlsx');
        ob_end_clean();

        return $data;
    }
}
