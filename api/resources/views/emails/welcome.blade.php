<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ozelden.com</title>
</head>
    <body style="font-family: 'Raleway', sans-serif; color: #707070;">
        <div style="width: 100%; margin: 16px 0px;" align="center">
            <img src="https://api.ozelden.com/images/logo.png" style="height: 48px;">
            <p>Öğretmen ve Öğrenci Bulma Portalı</p>
        </div>
        <div style="border-radius: 4px; margin: 16px; padding: 16px; border: 1px solid #707070; z-index: 2; position: relative; background-color: #fff;
               position: relative !important; z-index: 2 !important; margin-bottom: -48px;" align="center">
            <img src="https://api.ozelden.com/images/icons/confetti.png" style="width: 72px; height: 72px;">
            <h2 style="font-weight: 600;">Hoş Geldin {{$name}} {{$surname}}</h2>
            <p>özelden ailesine katıldın!</p>
            <p>Hemen bilgilerini <a href="https://ozelden.com">buradan</a> güncelle ve senin için en uygun {{$type == 'student' ? 'öğretmeni' : 'öğrenciyi'}} bulalım.</p>
            <p>Giriş için aşağıdaki bilgilerini kullanabilirsin: </p>
            <p><b>Email:</b> {{$email}}</p>
            <p><b>Parola:</b> {{$password}}</p>
        </div>
        <div style="background-color: #D1A377; padding: 64px 16px 16px 16px;" align="center">
            <p>ozelden.com</p>
        </div>
    </body>
</html>
