<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seu vídeo foi processado!</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #4f46e5; color: #fff; padding: 32px 40px; }
        .header h1 { margin: 0; font-size: 24px; }
        .body { padding: 32px 40px; color: #374151; }
        .body p { line-height: 1.6; }
        .button { display: inline-block; margin-top: 24px; padding: 12px 28px; background: #4f46e5; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .footer { padding: 24px 40px; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Vídeo processado com sucesso!</h1>
        </div>
        <div class="body">
            <p>Olá,</p>
            <p>Seu vídeo foi processado e os frames estão prontos para download.</p>
            <p><strong>ID do vídeo:</strong> {{ $videoId }}</p>
            <a href="{{ $resultUrl }}" class="button">Baixar frames</a>
            <p style="margin-top: 24px; font-size: 13px; color: #6b7280;">
                Caso o botão não funcione, copie o link abaixo:<br>
                <span style="word-break: break-all;">{{ $resultUrl }}</span>
            </p>
        </div>
        <div class="footer">
            Plataforma de Vídeo — FIAP POSTECH Fase 5
        </div>
    </div>
</body>
</html>
