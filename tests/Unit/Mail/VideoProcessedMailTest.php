<?php

use App\Infrastructure\Mail\VideoProcessedMail;
use App\Infrastructure\Mail\VideoFailedMail;
use Illuminate\Support\Facades\Mail;

uses(Tests\TestCase::class);

it('envia VideoProcessedMail com assunto correto e dados do video', function () {
    Mail::fake();

    Mail::to('destinatario@teste.com')->send(new VideoProcessedMail('vid-abc', 'https://s3/resultado.zip'));

    Mail::assertSent(VideoProcessedMail::class, function (VideoProcessedMail $mail) {
        return $mail->videoId === 'vid-abc'
            && $mail->resultUrl === 'https://s3/resultado.zip'
            && $mail->envelope()->subject === 'Seu vídeo foi processado com sucesso!';
    });
});

it('envia VideoFailedMail com assunto correto e id do video', function () {
    Mail::fake();

    Mail::to('destinatario@teste.com')->send(new VideoFailedMail('vid-xyz'));

    Mail::assertSent(VideoFailedMail::class, function (VideoFailedMail $mail) {
        return $mail->videoId === 'vid-xyz'
            && $mail->envelope()->subject === 'Falha no processamento do seu vídeo';
    });
});
