<?php 

namespace Modules\JobVacancies\Enums;

/**
 * Modo de aplicación de una convocatoria
 * Determina si un postulante puede aplicar a uno o varios perfiles
 */
enum ApplicationMode: int
{
    case SINGLE_PROFILE = 0;    // Solo puede postular a un perfil (default)
    case MULTIPLE_PROFILES = 1; // Puede postular a varios perfiles
}
