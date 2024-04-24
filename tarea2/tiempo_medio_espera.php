<?php

/******************************************************************************/
/*                                                                            */
/* TIEMPO MEDIO DE ESPERA                                                     */
/*                                                                            */
/* BREVE DESCRIPCIÓN: Tiempo estimado de espera de un encargo para entrar en  */
/*                    producción.                                             */
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


// Funcion que devuelve una lista de encargos abiertos en cola o en ejecución según el "id" cola o "id" ejecución
function getListaEncargos($id_entity, $id_category)
{
    // Filtro que el encargo no esté cancelado ni en ejecución
    $params = array
    (
        'entityTypeId' => $id_entity,
        'filter[categoryId]' => $id_category,
        'filter[!=stageId]' => array
        (
            'DT' . $id_entity . '_' . $id_category . ":FAIL",
            'DT' . $id_entity . '_' . $id_category . ":SUCCESS",
        )
    );

    // Construcción de la URL
    $url = GET_CRM_ITEM . '?' . http_build_query($params);

    // Lama a la API
    $respuesta = call_API($url);
    // Decodificar la respuesta JSON en un objeto PHP
    $objeto = json_decode($respuesta);

    // Array para guardar los encargos
    $listaEncargos = array();

    foreach($objeto->result->items as $item)
    {
       $listaEncargos[] = $item;
    }

    return $listaEncargos;
}


// Función que devuelve un array con el id del tipo de encargo y la cantidad de encargos de este tipo
function contarEncargosTipo($listaEncargos) 
{
    $conteoEncargos = array();

    // Iterar sobre los encargos
    foreach ($listaEncargos as $encargo) 
    {
        // Obtener el id del tipo de encargo
        $id_tipoEncargo = $encargo->ufCrm8_1712769451;

        // Contar los encargos y guardarlo en un array asociativo: tipo de encargo y cantidad
        if (isset($conteoEncargos[$id_tipoEncargo])) 
        {
            $conteoEncargos[$id_tipoEncargo]++;
        } 
        else 
        {
            $conteoEncargos[$id_tipoEncargo] = 1;
        }
    }

    return $conteoEncargos;
}


// Función que devuelve el tiempo medio en cola según el tipo de encargo por su "id"
function tiempoMedioCola($id_tipoEncargo, $id_entity)
{
    // Filtro
    $params = array
    (
        'entityTypeId' => $id_entity,
        'filter[id]' => $id_tipoEncargo
    );

    // Construcción de la URL
    $url = GET_CRM_ITEM . '?' . http_build_query($params);

    // Llama a la API
    $respuesta = call_API($url);
    // Decodificar la respuesta JSON en un objeto PHP
    $objeto = json_decode($respuesta);

    // Devolución del objeto de categoría decodificado
    return $objeto->result->items[0]->ufCrm10_1712768668;
}



/****************  PRINCIPAL  ****************/

// Obtener la lista de encargos abiertos en cola según el id de la cola
$listaEncargosCola = getListaEncargos(137, 18);

// Contar los encargos por tipo
$conteoEncargos = contarEncargosTipo($listaEncargosCola);

// Calcular el tiempo total en cola
$tiempoTotalCola = 0;

foreach ($conteoEncargos as $tipoEncargo => $cantidad) 
{
    $tiempoMedio = tiempoMedioCola($tipoEncargo, 186);

    // Calcular el tiempo total en cola para este tipo de encargo
    $tiempoTotalCola += $tiempoMedio * $cantidad;

    echo "Tipo de encargo $tipoEncargo - tiempo medio en cola $tiempoMedio días: $cantidad uds <br>";

    
}

// Tiempo total días en cola
echo "<br> Tiempo total en cola: $tiempoTotalCola días <br><br>";




// Obtener la lista de encargos abiertos en ejecución según el id ejecución
$listaEncargosEjecucion = getListaEncargos(137, 12);



?>