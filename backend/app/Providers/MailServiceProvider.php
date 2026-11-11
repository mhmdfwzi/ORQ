<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MailServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Fix SSL certificate CN mismatch for mail.medicamodrug.com
        // The SSL cert is issued for 'medicamodrug.com' but the host is 'mail.medicamodrug.com'
        $this->app->afterResolving('mail.manager', function ($mailManager) {
            $transport = $mailManager->mailer()->getSymfonyTransport();

            if ($transport instanceof \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport) {
                $stream = $transport->getStream();
                if ($stream instanceof \Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream) {
                    $stream->setStreamOptions([
                        'ssl' => [
                            'allow_self_signed' => true,
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ],
                    ]);
                }
            }
        });
    }

    public function register(): void
    {
        //
    }
}
