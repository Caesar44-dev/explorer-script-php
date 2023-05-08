<?php

// rutas y directorios
$ruta_directorio = getcwd();
if (isset($_POST["ruta_directorio"])) {
    $ruta_directorio = $_POST["ruta_directorio"];
}
$rutaRoot = getcwd();

// filtra archivos de directorios
function verDirectiorio($ruta)
{
    $directorio = opendir($ruta);
    $archivos = array();
    while ($current = readdir($directorio)) {
        if ($current != "." && $current != "..") {
            if (is_dir($ruta . $current)) {
                verDirectiorio($ruta . $current . '/');
            } else {
                $archivos[] = $current;
            }
        }
    }
    echo '<h3 class="list_1">' . $ruta . '</h3>';
    echo '<ul class="list_2">';
    for ($i = 0; $i < count($archivos); $i++) {
        echo '<li>' . $archivos[$i] . "</li>";
    }
    echo '</ul>';
}

if (isset($_POST["descargar_archivo"])) {
    // descargar archivo
    $fileName = $_POST["descargar_archivo"];
    $filePath = $ruta_directorio . "\\" .  $fileName;
    if (!empty($fileName) && file_exists($filePath)) {
        // headers
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");
        readfile($filePath);
        exit;
    } else {
        $error = "el archivo no existe";
    }
}

// subir archivos
if (empty($_FILES["file"]["name"]) === false) {
    $nombre_archivo = $_FILES["file"]["name"];
    $tmp_nombre = $_FILES["file"]["tmp_name"];
    $ruta = $ruta_directorio . "\\" . $nombre_archivo;
    $isError = move_uploaded_file($tmp_nombre, $ruta);
    if (!$isError) {
        $error = "ha ocurrido un erro al subir el archivo";
    } else {
        // header("location: explorer.php");
    }
}


// crear directorios
if (empty($_POST["crear_directorio"]) === false) {
    /* evita el sobreescibir directorios */
    if (file_exists($ruta_directorio . "\\" . $_POST["crear_directorio"]) === true) {
        $error = "El directorio ya existe";
    } else {
        $res = mkdir($ruta_directorio . "\\" . $_POST["crear_directorio"], 0777, true);
        if ($res) {
            header("location: explorer.php");
        }
    }
}

// elimina directorios con contenido
function EliminarDirectorio($directorio)
{
    foreach (glob($directorio . "/*") as $elemento) {
        if (is_dir($elemento)) {
            EliminarDirectorio($elemento);
        } else {
            unlink($elemento);
        }
    }
    rmdir($directorio);
}

if (empty($_POST["borrar_directorio"]) === false) {
    if (file_exists($ruta_directorio . "\\" . $_POST["borrar_directorio"]) === false) {
        $error = "El directorio no existe";
    } else {
        $res = EliminarDirectorio(htmlspecialchars($ruta_directorio . "\\" . $_POST["borrar_directorio"]));
        if ($res) {
            // header("location: explorer.php");
        }
    }
}

// eliminar archivo
if (empty($_POST["borrar_archivo"]) === false) {
    if (file_exists($ruta_directorio . "\\" . $_POST["borrar_archivo"]) === false) {
        $error = "El archivo no existe";
    } else {
        $nombre_archivo = htmlspecialchars($ruta_directorio . "\\" . $_POST["borrar_archivo"]);
        $isError = unlink($nombre_archivo);
        if (!$isError) {
            $error = "ha ocurrido un erro al eliminar el archivo";
        } else {
            // header("location: explorer.php");
        }
    }
}

// ver un archivo
if (empty($_POST["mostrar_archivo"]) === false) {
    if (!file_exists($ruta_directorio . "\\" . $_POST["mostrar_archivo"]) === true) {
        $error = "El archivo o directorio no existe";
    } else {
        $mostrar_archivo = htmlspecialchars($ruta_directorio . "\\" . $_POST["mostrar_archivo"]);
        $ruta_a = fopen($mostrar_archivo, "r");
        while (!feof($ruta_a)) {
            $linea = fgets($ruta_a);
            echo $linea . "<br>";
        }
        fclose($ruta_a);
        // header("location: explorer.php");
    }
}
// if (!((strpos($tipo_archivo, "gif") || strpos($tipo_archivo, "jpeg")) && ($tamano_archivo < 100000)))
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- normalize css -->
    <link rel="stylesheet" href="https://necolas.github.io/normalize.css/8.0.1/normalize.css">
    <title>explorer</title>
</head>

<body>

    <style>
        .container_ {
            background-color: #111;
            color: #fff;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: start;
        }

        .container_list {
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .container_list_2 {
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        form {
            margin: 4px 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        input {
            text-decoration: none;
            border: none;
            outline: none;
        }

        input[type=text] {
            background-color: #fff;
            color: #111;
            width: 280px;
            font-size: 14px;
            padding: 10px 32px;
            margin: 10px 2px;
        }

        input[type=submit] {
            background-color: #bd2d2d;
            color: #fff;
            width: 180px;
            padding: 10px 32px;
            margin: 10px 2px;
            cursor: pointer;
        }

        .text_01 {
            font-size: 18px;
            color: #fff;
        }

        .text_02 {
            font-size: 26px;
            color: #fff;
        }

        .text_03 {
            font-size: 16px;
            color: #bd2d2d;
        }

        .list_1 {
            font-size: 12px;
        }

        .list_2 {
            text-decoration: none;
            list-style: none;
            font-size: 12px;
        }

        label {
            font-size: 12px;
            color: #fff;
        }

        input[type=file]::-webkit-file-upload-button {
            visibility: hidden;
        }

        input[type=file]::before {
            width: 100%;
            content: 'Seleccionar archivo';
            background: #fff;
            color: #111;
            border: 1px solid #fff;
            padding: 5px;
            outline: none;
            cursor: pointer;
            font-size: 14px;
        }

        .list_content {
            padding: 10px 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
    </style>
    <div class="container_">
        <div class="container_list">
            <!-- errores -->
            <strong class="text_03" style="padding-top: 10px;">
                Errores:
                <?php
                if (empty($error) === false) {
                    echo htmlspecialchars($error);
                }
                ?>
            </strong>

            <!-- ver y listar directorios -->
            <h1 class="text_02">Vista de directorios</h1>
            <h3 class="text_01" style="margin-bottom: 0;">Ruta principal del explorador</h3>
            <h3 class="text_03"><?= $rutaRoot ?></h3>
            <form method="post" enctype="multipart/form-data" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                <label>Ingrese ruta del directorio a navegar</label>
                <input type="text" name="ruta_directorio" />
                <input type="submit" name="ver" />
            </form>
            <h3 class="text_01" style="margin-bottom: 0;">Ruta del directorio seleccionado</h3>
            <h3 class="text_03"><?= $ruta_directorio ?></h3>

            <div class="list_content">
                <?php
                if (isset($ruta_directorio)) {
                    verDirectiorio($ruta_directorio);
                }
                ?>
            </div>
        </div>
        <div class="container_list_2">

            <!-- descargar archivos -->
            <h1 class="text_01">Descargar archivos</h1>
            <form method="post" enctype="multipart/form-data" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                <label>Ingrese ruta del archivo en el directorio seleccionado</label>
                <input type="text" name="descargar_archivo" />
                <input type="hidden" name="ruta_directorio" value="<?= htmlspecialchars($ruta_directorio) ?>" />
                <input type="submit" />
            </form>

            <!-- subir archivos -->
            <h1 class="text_01">Subir archivo al directorio seleccionado</h1>
            <form method="post" enctype="multipart/form-data" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                <input class="custom-file-input" type="file" name="file" />
                <input type="hidden" name="ruta_directorio" value="<?= htmlspecialchars($ruta_directorio) ?>" />
                <input type="submit" />
            </form>

            <!-- crear directorios -->
            <h1 class="text_01">Crear directorio</h1>
            <form method="post" enctype="multipart/form-data" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                <label>Nombre de la carpeta a crear en el directorio seleccionado</label>
                <input type="text" name="crear_directorio" />
                <input type="hidden" name="ruta_directorio" value="<?= htmlspecialchars($ruta_directorio) ?>" />
                <input type="submit" />
            </form>

            <!-- eliminar directorios -->
            <h1 class="text_01">Eliminar directorio</h1>
            <form method="post" enctype="multipart/form-data" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                <label>Nombre de carpeta a eliminar en el directorio seleccionado</label>
                <input type="text" name="borrar_directorio" />
                <input type="hidden" name="ruta_directorio" value="<?= htmlspecialchars($ruta_directorio) ?>" />
                <input type="submit" />
            </form>

            <!-- eliminar archivo -->
            <h1 class="text_01">Eliminar archivos</h1>
            <form method="post" enctype="multipart/form-data" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                <label>Nombre del archivo a eliminar en el directorio seleccionado</label>
                <input type="text" name="borrar_archivo" />
                <input type="hidden" name="ruta_directorio" value="<?= htmlspecialchars($ruta_directorio) ?>" />
                <input type="submit" />
            </form>

            <!-- ver archivos -->
            <h1 class="text_01">Ver archivo</h1>
            <form method="post" enctype="multipart/form-data" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                <label>Nombre del archivo a mostrar en el directorio seleccionado</label>
                <input type="text" name="mostrar_archivo" />
                <input type="hidden" name="ruta_directorio" value="<?= htmlspecialchars($ruta_directorio) ?>" />
                <input type="submit" />
            </form>
        </div>
        <script>
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        </script>
</body>

</html>