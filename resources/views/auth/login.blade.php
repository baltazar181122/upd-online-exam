

<!DOCTYPE html>
<html>
    
<head>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="shortcut icon" href="{{ asset('assets/images/updent.png') }}" />
	<title>UP Dent - Login</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">


</head>
<!--Coded with love by Mutiullah Samim-->
<body>
	<div class="container h-100">
		<div class="d-flex justify-content-center h-100">

			<div class="user_card">

				<div class="d-flex justify-content-center">
				
					<div class="brand_logo_container">
						<img src="{{ asset('assets/images/updent.png') }}" class="brand_logo" alt="Logo">
					</div>
				</div>
				<div class="justify-content-center form_container">

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
						@if ($message = Session::get('exist'))
							<div class="form-group">
							<br>
								<div class="alert alert-success alert-block">
									<button type="button" class="close" data-dismiss="alert">×</button>	
										<strong>{{ $message }}</strong>
								</div>
							</div>
						@endif
						<div class="input-group mb-3">
							<div class="input-group-append">
								<span class="input-group-text"><i class="fa  fa-envelope"></i></span>
							</div>
                            <input type="email" class="form-control input_user @error('email') is-invalid @enderror"  placeholder="Email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                    <span class="d-flex invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
						</div>
						<div class="input-group mb-2">
							<div class="input-group-append">
								<span class="input-group-text"><i class="fas fa-key"></i></span>
							</div>
                            <input type="password" name="password" class="form-control input_pass @error('password') is-invalid @enderror" value="" placeholder="password" required>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
						</div>
						<div class="form-group">
							<div class="custom-control custom-checkbox">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
								<label class="custom-control-label text-white" for="customControlInline">Remember me</label>
							</div>
                        </div>
					<button type="submit" name="button" class="btn login_btn">Login</button>
                        
					</form>
				</div>
				<div class="d-flex justify-content-center mt-3 login_container">
				</div>
				<div class="mt-4">
					<div class="d-flex justify-content-center links">
						<a href="/password/reset">Forgot your password?</a>
					</div>
				</div>
			</div>
		</div>
    </div>
    

<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->
</body>
</html>
