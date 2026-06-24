<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Falha no processamento do vídeo</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #dc2626; color: #fff; padding: 32px 40px; }
        .header h1 { margin: 0; font-size: 24px; }
        .body { padding: 32px 40px; color: #374151; }
        .body p { line-height: 1.6; }
        .footer { padding: 24px 40px; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❌ Falha no processamento do vídeo</h1>
        </div>
        <div class="body">
            <p>Olá,</p>
            <p>Infelizmente ocorreu um erro durante o processamento do seu vídeo.</p>
            <p><strong>ID do vídeo:</strong> {{ $videoId }}</p>
            <p>Nossa equipe foi notificada. Você pode tentar enviar o vídeo novamente.</p>
        </div>
        <div class="footer">
            Plataforma de Vídeo — FIAP POSTECH Fase 5
        </div>
    </div>
</body>
</html>
