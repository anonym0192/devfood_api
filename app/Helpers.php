<?php

function formatOrderStatus($status){

    $newStatus = 0;
        switch($status) {
            case '0':
            case '1': // Aguardando Pgto.
            case '2': // Em análise
                $newStatus = '1';
                break;
            case '3': // Paga
            case '4': // Disponível
                $newStatus = '2';
                break;
            case '6': // Devolvida
            case '7': // Cancelada
                $newStatus = '3';
                break;
            default:
                throw new Exception("Status not valid!") ;
        }
    
        return $newStatus; 
} 

function getStatusDescription($status){
    switch($status){
        case '0':
        case '1': 
            return 'Aguardando Pgto';
        case '2': 
            return 'Pago';
        case '3': 
            return 'Cancelado';
        default:
             return '';
    }
}