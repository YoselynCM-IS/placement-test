<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccessExam extends Mailable
{
    use Queueable, SerializesModels;

    private $exam;
    private $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($exam, $user)
    {
        $this->exam = $exam;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('info@placementtestmajesticeducation.com')
            ->cc('me.examencolocacion@gmail.com')
            ->subject(__("Examen de colocación"))
            ->markdown('mails.access-exam') //Template
            ->with('exam', $this->exam)
            ->with('user', $this->user);
    }
}
