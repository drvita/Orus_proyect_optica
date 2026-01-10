<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Support</title>
</head>
<body>
  <h1>Support</h1>

  <a href="{{ route('supports.create') }}">Criar Dúvida</a>

  <table>
    <thead>
      <th>Assunto</th>
      <th>Status</th>
      <th>Descrição</th>
      <th>Ações</th>
    </thead>
    <tbody>
      @foreach($supports as $support)
        <tr>
          <td>{{ $support->subject }}</td>
          <td>{{ $support->status }}</td>
          <td>{{ $support->body }}</td>
          <td>
            <a href="{{ route('supports.show', $support->id) }}">></a>
            <a href="{{ route('supports.edit', $support->id) }}">Editar</a>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  

</body>
</html>