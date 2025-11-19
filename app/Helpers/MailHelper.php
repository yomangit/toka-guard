<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailHelper
{
    /**
     * Kirim email notifikasi
     *
     * @param string $to Email tujuan
     * @param string $subject Judul email
     * @param string $view Blade view untuk email (resources/views/emails/*)
     * @param array $data Data yang dikirim ke view
     *
     * @return bool
     */
    public static function sendNotification($to, $subject, $view, $data = [])
    {
        try {
            Mail::send($view, $data, function ($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject)
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            if (count(Mail::failures()) > 0) {
                Log::error('Mail gagal dikirim', Mail::failures());
                return false;
            }
            return true;

        } catch (\Exception $e) {
            Log::error('Error saat kirim email: ' . $e->getMessage());
            return false;
        }
    }
}
