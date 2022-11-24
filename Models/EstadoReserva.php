<?php

namespace Models;

enum EstadoReserva: string
{
    case SOLICITADA = "Solicitada";
    case CONFIRMADA = "Confirmada";
    case CANCELADA = "Cancelada";
    case EN_CURSO = "En curso";
    case FINALIZADA = "Finalizada";
    case ESPERA = "En espera de pago";
}
