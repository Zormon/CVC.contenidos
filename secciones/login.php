<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link type="text/css" rel="stylesheet" href="/css/main.css?4"  media="screen,projection"/>
    <style><?=print_css_vars()?></style>
</head>
<body id="login">
    <header>
        <h1>Canal Corporativo</h1>
        <img src="img/mainLogo.webp">
    </header>

    <main>
        <h2>Cuenta de usuario</h2>
        <form id="loginForm">
            <div class="grid form">
                <div class="g6">
                    <div class="input icon-prefix icon-usuario">
                        <input name="user" id="user" type="text" required placeholder=" ">
                        <label for="user">Usuario</label>
                    </div>
                </div>    
                <div class="g6">
                    <div class="input icon-prefix icon-equipo">
                        <input name="pass" id="pass" type="password" required placeholder=" ">
                        <label for="pass">Contraseña</label>
                    </div>
                </div>    
                <div class="g12">
                    <input name="remember" type="checkbox" />
                    <span>Recordarme en este equipo</span>
                </div>
                <div class="g12"><p id="errorMsg">&nbsp;</p></div>
            </div>
            <button id="entrar">Acceder</button>
        </form>
    </main>

    <footer>
            <div class="row">
                <div class="col s12 center-align">
                    <img src="img/cvcLogo.webp">
                </div>
            </div>
    </footer>       

    <script type="module">
        import {$} from '/js/exports.js?3'

        function login(e) {
            e.preventDefault()
            $('errorMsg').classList.add('invisible')
            if ( $('loginForm').checkValidity() ) {
                const loginForm = new FormData($('loginForm'))
                loginForm.append('mode', 'login')

                fetch('/api/login', {method: 'POST', body: loginForm}).then(resp => resp.json()).then( (data)=> {
                    if (data.status == 'ok') { 
                        let loginData = new FormData()
                        loginData.append('usuario', loginForm.get('user'))
                        loginData.append('clave', loginForm.get('pass'))
                        loginData.append('login','Entrar')
                        loginData.append('action','loginSubmit')

                        const url = 'https://soporte.comunicacionvisualcanarias.com/gestion.html'
                        fetch(url, {method: 'POST', mode: 'no-cors', body: loginData, credentials: 'include'})
                        .then( ()=> { 
                            location = '/' 
                        } )
                    } else if (data.status == 'ko') { 
                        switch (data.error) {
                            case 'invalid':
                                $('errorMsg').textContent = 'Credenciales inválidas'
                                $('errorMsg').classList.remove('invisible')
                            break;
                        }
                     }
                })
            } else { $('loginForm').reportValidity() }
        }

        $('entrar').onclick = (e)=> { login(e) }
        $('user').onkeypress = $('pass').onkeypress = (e)=> { if(e.keyCode === 13) { login(e) } }
    </script>
</body>
</html>
