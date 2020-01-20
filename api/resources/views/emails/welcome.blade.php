<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>turnuvaz.com</title>
</head>
<body style="background: #303030; color: #ffffff; font-family: sans-serif;">
<div align="center">
    <img src="https://api.ozelden.com/images/logo.png" style="height: 48px;">
    <p>Gerçek yarış, gerçek ödül</p>
</div>
<div style="width: 70%; background: #ffffff; color: #303030; margin: 32px 15%; border-radius: 8px; padding: 16px;" align="center">
    <h2>Merhaba, {{$name}} {{$surname}}</h2>
    <p>Hemen
        <a href="https://turnuvaz.com" style="background: #7B1FA2; color: #fff; border: 0; padding: 4px 8px; border-radius: 4px; font-weight: 600; cursor: pointer; text-decoration: none;">turnuvaz.com</a>
        `a gir ve kazanmak için bir {{$type == 'player' ? 'turnuvaya katıl' : 'turnuva oluştur'}}.
    </p>
    <p>Giriş için aşağıdaki bilgilerini kullanabilirsin: </p>
    <p><b>Email:</b> {{$email}}</p>
    <p><b>Şifre:</b> {{$password}}</p>
</div>
<div align="center">
    <p>turnuvaz.com</p>
</div>
</body>
</html>
