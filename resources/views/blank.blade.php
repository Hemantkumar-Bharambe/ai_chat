<!DOCTYPE html>
<html>
<head>
    <title>Users</title>
</head>
<body>

<h1>Users List</h1>

@foreach($users as $user)
    <p>{{ $user->name }}</p>
@endforeach

</body>
</html>
