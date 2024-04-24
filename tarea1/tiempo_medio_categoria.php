<?php

/******************************************************************************/
/*                                                                            */
/* TIEMPO MEDIO POR CATEGORIA                                                 */
/*                                                                            */
/* BREVE DESCRIPCIÓN: Tiempo medio ejecución según categoría de los encargos  */
/*                                                                            */
/* REQUISITOS:                                                                */
/* 		                                                                      */
/*                                                                            */
/* PARÁMETROS DE ENTRADA:                                                     */
/*                                                                            */
/*                                                                            */
/******************************************************************************/

/************************** LLAMADAS A LA API REST  ***************************/
define('GET_CRM_ITEM', "https://bitrix.proyectanda.com/rest/30/iaemhdw1mr4zigwe/crm.item.list.json");


/***************  FUNCIÓN QUE LANZA LAS LLAMADAS A LA API REST  ***************/

// Función para realizar llamadas a la API REST
function call_API($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);

    $result = curl_exec($ch);

    if (!$result || $result == null) {
        throw new Exception('Error: ' . curl_error($url) . '<br>' . 
                            'Code: ' . curl_errno($url));
    }

    return $result;
    curl_close($ch);
}


// Función que devuelve los datos del tipo de encargo según el "nombre"
function getDatosCategoria1($nombre, $id_entity)
{
    // Filtro
    $params = array
    (
        'entityTypeId' => $id_entity,
        'filter[title]' => $nombre
    );

    // Construcción de la URL
    $url = GET_CRM_ITEM . '?' . http_build_query($params);

    // Llama a la API
    $respuesta = call_API($url);
    // Decodificar la respuesta JSON en un objeto PHP
    $objeto = json_decode($respuesta);

    // Devolución del objeto de categoría decodificado
    return $objeto;
}


// Función que devuelve los datos del tipo de encargo según el "id"
function getDatosCategoria2($id, $id_entity)
{
    // Filtro
    $params = array
    (
        'entityTypeId' => $id_entity,
        'filter[id]' => $id
    );

    // Construcción de la URL
    $url = GET_CRM_ITEM . '?' . http_build_query($params);

    // Llama a la API
    $respuesta = call_API($url);
    // Decodificar la respuesta JSON en un objeto PHP
    $objeto = json_decode($respuesta);

    // Devolución del objeto de categoría decodificado
    return $objeto;
}



/****************  PRINCIPAL  ****************/

// Obtención de los datos de la categoría especificada por nombre
$datos = getDatosCategoria1("PRY - Web Corporativa", 186);
//var_dump($datos);
 
// Verificación de si hay datos disponibles
if (!empty($datos)) 
{   
    // Mostrar el tiempo medio de ejecución de la categoría 
    echo "Categoría: " . $datos->result->items[0]->title . "<br>";
    echo "Tiempo medio ejecución (días): " . $datos->result->items[0]->ufCrm10_1712768893 . "<br><br>";
} 
else 
{
    // Si no hay datos
    echo "No se han encontrado datos";
}


// Obtención de los datos de la categoría especificada por id
$datos = getDatosCategoria2(4, 186);
//var_dump($datos);
 
// Verificación de si hay datos disponibles
if (!empty($datos)) 
{   
    // Mostrar el tiempo medio de ejecución de la categoría 
    echo "Categoría: " . $datos->result->items[0]->title . "<br>";
    echo "Tiempo medio ejecución (días): " . $datos->result->items[0]->ufCrm10_1712768893;
} 
else 
{
    // Si no hay datos
    echo "No se han encontrado datos";
}


?>