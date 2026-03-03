<?php 

namespace Modules\JobVacancies\Enums;

/**
 * Estados de una convocatoria laboral
 */
enum VacancyStatus: int
{
    case PUBLICADA = 1;      // Publicada - admite ediciones sin log
    case CERRADA = 2;        // Cerrada - no admite postulantes
    case EN_EVALUACION = 3;  // En Evaluación - solo archivos nuevos
    case FINALIZADA = 4;     // Finalizada - fin de vida
    case CANCELADA = 5;      // Cancelada - fin de vida

    public function label(): string
    {
        return match($this) {
            self::PUBLICADA => 'Publicada',
            self::CERRADA => 'Cerrada',
            self::EN_EVALUACION => 'En Evaluación',
            self::FINALIZADA => 'Finalizada',
            self::CANCELADA => 'Cancelada',
        };
    }

}
