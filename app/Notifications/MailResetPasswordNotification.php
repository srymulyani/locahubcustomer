<?php

namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
//custom
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
class MailResetPasswordNotification extends Notification
{
        use Queueable;
        protected $pageUrl;
        public $token;
        /**
        * Create a new notification instance.
        *
        * @return void
        */
        public function __construct(string $url)
        {
            $this->url = $url;
        // we can set whatever we want here, or use .env to set environmental variables
        }
       
        public function via($notifiable)
        {
            return ['mail'];
        }

         /**
         * Undocumented function
         *
         * @param mixed
         * @return \Illuminate\Notifications\Messages\MailMessage
         */

        public function toMail($notifiable)
        {
           return (new MailMessage)
                    ->line('Forgot Password?')
                    ->action('Click to reset', $this->url)
                    ->line('Thank you for using our application!');
        }
        public function toArray($notifiable){
        return [
            //
        ];
         }
         
        
}

