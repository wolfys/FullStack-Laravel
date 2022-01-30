<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class WelcomeMail extends Mailable
{
    public function __construct($userId,$token,$name)
    {
        $this->userId = $userId;
        $this->token = $token;
        $this->name = $name;
    }

    public function build()
    {
        return $this->markdown('emails.welcome')
            ->subject('Подтверждение регистрации '. $this->name .' на проекте Ge-World.RU')
            ->with(
                [
                    'userId' => $this->userId,
                    'token' => $this->token,
                    'name' => $this->name,
                ]);
    }
}
