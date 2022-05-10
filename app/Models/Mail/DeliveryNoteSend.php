<?php

namespace App\Models\Mail;

use App\Models\Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeliveryNoteSend extends Mailable
{
    use Queueable, SerializesModels;

    public $deliveryNote;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($deliveryNote)
    {
        $this->deliveryNote = $deliveryNote;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        if(\Auth::user()->type == 'super admin')
        {
            return $this->view('email.delivery_note_send')->with('deliveryNote', $this->deliveryNote)->subject('Ragarding to product/service invoice generator.');
        }
        else
        {
            return $this->from(Utility::getValByName('company_email'), Utility::getValByName('company_email_from_name'))->view('email.invoice_send')->with('invoice', $this->deliveryNote)->subject('Ragarding to product/service delivery note generator.');
        }
    }
}
