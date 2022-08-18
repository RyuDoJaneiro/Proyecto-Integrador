<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Por favor ingrese un usuario.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "Este usuario ya fue tomado.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Al parecer algo salió mal.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Por favor ingresa una contraseña.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "La contraseña al menos debe tener 6 caracteres.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Confirma tu contraseña.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "No coincide la contraseña.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, work) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Algo salió mal, por favor inténtalo de nuevo.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
 <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>JLS</title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="icon" type="image/x-icon" href="assets/img/bank.png" />

    <link href="https://cdn.jsdelivr.net/npm/bootstr+ap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,600;1,600&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,300;0,500;0,600;0,700;1,300;1,500;1,600;1,700&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,400;1,400&amp;display=swap"
        rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
</head>

<body id="page-top" class="" style="background-color: #FEDE3B;">

    <nav class="navbar navbar-expand-lg bg-black text-white fixed-top shadow-sm" id="mainNav">
        <div class="container">
            <a class="navbar-brand text-light" href="index.html">JLS</a>
            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
                aria-label="Toggle navigation">
                Menu
                <i class="bi-list"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ms-auto me-3 my-3 my-lg-0">
                    <li class="nav-item"><a class="nav-link me-lg-3 text-white" href="#features">About Us</a></li>
                    <li class="nav-item"><a class="nav-link me-lg-3 text-white" href="login.php">Iniciar Sesion</a></li>
                    <li class="nav-item"><a class="nav-link me-lg-3 text-white" href="register.php">Registro</a></li>
                </ul>
                <button class="btn rounded-pill px-3 mb-2 mb-lg-0 text-black me-3" data-bs-toggle="modal"
                    style="background-color: #FEDE3B;" data-bs-target="#feedbackModal2">
                    <span class="small">More Coins</span>
                </button>
                <button onclick="actualizarCotizaciones()" class="btn rounded-pill px-3 mb-2 mb-lg-0 text-black"
                    data-bs-toggle="modal" style="background-color: #FEDE3B;" data-bs-target="#feedbackModal">
                    <span class="small">Dolar Now</span>
                </button>
            </div>
        </div>
    </nav>
    
    <div class="masthead container" style="background-color: #FEDE3B">
    <div class="wrapper">
        <h2 class= "font-alt">Registro</h2>
        <p>Por favor complete este formulario para crear una cuenta.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group col-12 col-xl-3 <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>" placeholder="Nombre" style="border-radius: 100px;">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group col-12 col-xl-3 mt-3">
                <input type="text" name="work" class="form-control" placeholder="Puesto de trabajo" style="border-radius: 100px;">
            </div>
            <div class="form-group col-12 col-xl-3 mt-3 <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>" placeholder="Contraseña" style="border-radius: 100px;">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group col-12 col-xl-3 mt-3 <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>" placeholder="Confirmar Contraseña" style="border-radius: 100px;">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group mt-3">
                <input type="submit" class="btn btn-dark" value="Ingresar" style="border-radius: 100px">
                <input type="reset" class="btn btn-dark" value="Borrar" style="border-radius: 100px">
            </div>
            <p class="mt-3">¿Ya tienes una cuenta? <a href="login.php">Ingresa aquí</a>.</p>
        </form>
    </div>   
    </div>    


    <footer class="bg-black text-center py-5 col-12 col-md-12 col-xl-12" style="width: 100%;">
        <div class="text-white-50 small">
            <div class="mb-2">&copy;Smart Cookies</div>
            <a href="#!">Privacy</a>
            <span class="mx-1">&middot;</span>
            <a href="#!">Terms</a>
            <span class="mx-1">&middot;</span>
            <a href="#!">FAQ</a>
        </div>
    </footer>

    <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true"
        style="width: 100%;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-4 fw-bold" style="background-color: #FEDE3B;">
                    <h5 class=" modal-title font-alt text-black" id="feedbackModalLabel">Dolar Now</h5>
                    <button class="btn-close btn-close-black" type="button" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body border-0 p-4" id="table-info">
                    <div class="father-cards-dolar text-center fs-6">
                        <div class="row justify-content-center">
                            <div class="col">
                                <div class="card m-1 border border-dark rounded-3" id="dolar-oficial-estilo">
                                    <div class="card-header fw-bolder">DOLAR OFICIAL</div>
                                    <div class="card-body p-0">
                                        <div class="card-group">
                                            <div class="card border m-0 rounded-0" id="tabla-compra">
                                                <div class="card-header fw-lighter fs-6">COMPRA</div>
                                                <div class="card-body dolar-value compravalor" id="data.0.casa.compra">$
                                                </div>
                                            </div>
                                            <div class="card border m-0 rounded-0" id="tabla-venta">
                                                <div class="card-header fw-lighter fs-6">VENTA</div>
                                                <div class="card-body dolar-value ventavalor" id="data.0.casa.venta">$
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer agenciavalor" id="data.0.casa.agencia">AGENCIA </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-group">
                            <div class="card m-1 border border-dark rounded-3" id="dolar-blue">
                                <div class="card-header fw-bolder">BLUE</div>
                                <div class="card-body p-0">
                                    <div class="card-group">
                                        <div class="card border m-0 rounded-0" id="tabla-compra">
                                            <div class="card-header fw-lighter fs-6">COMPRA</div>
                                            <div class="card-body dolar-value compravalor" id="data.1.casa.compra">$
                                            </div>
                                        </div>
                                        <div class="card border m-0 rounded-0" id="tabla-venta">
                                            <div class="card-header fw-lighter fs-6">VENTA</div>
                                            <div class="card-body dolar-value ventavalor" id="data.1.casa.venta">$</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer agenciavalor" id="data.1.casa.agencia">AGENCIA </div>
                            </div>
                            <div class="card m-1 border border-dark rounded-3" id="contado-con-liqui-dolar">
                                <div class="card-header fw-bolder">CONTADO CON LIQUI</div>
                                <div class="card-body p-0">
                                    <div class="card-group">
                                        <div class="card border m-0 rounded-0" id="tabla-compra">
                                            <div class="card-header fw-lighter">COMPRA</div>
                                            <div class="card-body dolar-value compravalor" id="data.3.casa.compra">$
                                            </div>
                                        </div>
                                        <div class="card border m-0 rounded-0" id="tabla-venta">
                                            <div class="card-header fw-lighter">VENTA</div>
                                            <div class="card-body dolar-value ventavalor" id="data.3.casa.venta">$</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer agenciavalor" id="data.3.casa.agencia">AGENCIA </div>
                            </div>
                        </div>
                        <div class="card-group">
                            <div class="card m-1 border border-dark rounded-3" id="dolar-bolsa">
                                <div class="card-header fw-bolder">BOLSA</div>
                                <div class="card-body p-0">
                                    <div class="card-group">
                                        <div class="card border m-0 rounded-0" id="tabla-compra">
                                            <div class="card-header fw-lighter">COMPRA</div>
                                            <div class="card-body dolar-value compravalor" id="data.4.casa.compra">$
                                            </div>
                                        </div>
                                        <div class="card border m-0 rounded-0" id="tabla-venta">
                                            <div class="card-header fw-lighter">VENTA</div>
                                            <div class="card-body dolar-value ventavalor" id="data.4.casa.venta">$</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer agenciavalor" id="data.4.casa.agencia">AGENCIA </div>
                            </div>
                            <div class="card m-1 border border-dark rounded-3" id="dolar-estilo">
                                <div class="card-header fw-bolder">DOLAR</div>
                                <div class="card-body p-0">
                                    <div class="card-group">
                                        <div class="card rounded-0" id="tabla-compra">
                                            <div class="card-header fw-lighter">COMPRA</div>
                                            <div class="card-body dolar-value compravalor" id="data.7.casa.compra">$
                                            </div>
                                        </div>
                                        <div class="card rounded-0" id="tabla-venta">
                                            <div class="card-header fw-lighter">VENTA</div>
                                            <div class="card-body dolar-value ventavalor" id="data.7.casa.venta">$</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer agenciavalor" id="data.7.casa.agencia">AGENCIA</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-warning col-6 mt-3 mb-3"
                            onclick="actualizarCotizaciones()">Actualizar Cotizaciones</button>
                        <button type="button" class="btn btn-warning col-6" onclick="descargarCotizaciones()">Descargar
                            Cotizaciones</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="feedbackModal2" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-4 fw-bold" style="background-color: #FEDE3B;">
                    <h5 class=" modal-title font-alt text-black" id="feedbackModalLabel">More Coins</h5>
                    <button class="btn-close btn-close-black" type="button" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body border-0 p-4">
                    <div class="">
                        <iframe width="300px" height="690px" src="https://www.dolarsi.com/func/moduloArriba-n.html"
                            frameborder="0" scrolling="0" allowfullscreen="" class="col-12"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
    <script src="js/main.js"></script>
</body>

</html>