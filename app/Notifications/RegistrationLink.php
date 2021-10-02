<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\User;


class RegistrationLink extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(array $arr)
    {
        // $this->middleware('auth');
        $this->arr = $arr;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $checkExit = User::where('email', $this->arr['email'])->count();

        if ( $checkExit > 0) {
            $url =  url('/login');
            
        }else{
            $url = url('/register').'/'.$this->arr['code'];
        }


        return (new MailMessage)
                    ->from('no-reply@omniquest.com.ph','UP Dent | Registration Link (No-Replay Email)')
                    ->line('You are invited to take the exam. To '.($checkExit > 0)? 'login':'register'.', kindly click the button below.')
                    ->action(($checkExit > 0)? 'Login':'Register',  $url)
                    ->line('Note: this invitation is intended for '.$this->arr['email'].'. If you were not expecting this invitation, you can ignore this email.')
                    ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
