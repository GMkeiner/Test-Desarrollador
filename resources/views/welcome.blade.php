<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba Técnica</title>
    <link rel="stylesheet" href="{{asset('assets/css/style2.css')}}">
    <link rel="icon" href="{{asset('assets/img/Laravel.png')}}">
</head>
<body>
    <nav>
    </nav>

    <header>
        <h1>Bienvenido a la prueba tecnica en Laravel 10</h1>
        <p>nivel junior</p>
        <button>
            <div class="svg-wrapper-1">
                <div class="svg-wrapper">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path fill="currentColor" d="M1.946 9.315c-.522-.174-.527-.455.01-.634l19.087-6.362c.529-.176.832.12.684.638l-5.454 19.086c-.15.529-.455.547-.679.045L12 14l6-8-8 6-8.054-2.685z"></path>
                  </svg>
                </div>
              </div>
              <a href="{{route('clientes.index')}}">Comenzar</a>
        </button>
        
    </header>

    <footer>
        <p>&copy; Keiner_10</p>
    </footer>
</body>
</html>
