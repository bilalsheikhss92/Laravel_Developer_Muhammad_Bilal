<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Register</title>
</head>
<body>
  <form action={{url('/api/user/register')}} method="POST">
  <input type="hidden" name="email" value= {{$email}} >
    	@if ($errors->has('email'))
      <span class="invalid-feedback" role="alert">
        <strong>{{ $errors->first('email') }}</strong>
      </span>
    @endif
    <label for="">Enter User-name (4-20 No Spaces allowed)</label>

  <input type="text" value="" name="user_name">
    	@if ($errors->has('user_name'))
      <span class="invalid-feedback" role="alert">
        <strong>{{ $errors->first('user_name') }}</strong>
      </span>
    @endif
    <label for="">Enter Password (Min 6)</label>

  <input type="password" value="" name="password">
  	@if ($errors->has('password'))
      <span class="invalid-feedback" role="alert">
        <strong>{{ $errors->first('password') }}</strong>
      </span>
    @endif
  <button type="submit"> Submit </button>
  </form>
</body>
</html>