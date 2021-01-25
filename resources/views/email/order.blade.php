<!DOCTYPE html>
<html>
<head>
    <title>Pedido en Óptica Madero</title>
</head>
<body>
    <h1>Pedido en Óptica Madero</h1>
    <hr>
    <h4>Estimad@ <span style="text-transform: uppercase;">{{ $data['name'] }}</span></h4>
    <p>Para informarle que su pedido #{{ $data['id'] }}, esta terminado. Sugerimos pasar hoy mismo por el.</p>

    <p>Muchas gracias.</p>
    <small>Este correo es enviado por el sistema <a href="https://www.iqissmexico.com.mx">Orus</a></small>
</body>
</html>