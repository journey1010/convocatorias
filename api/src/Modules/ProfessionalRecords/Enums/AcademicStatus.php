<?php 

namespace Modules\ProfessionalRecords\Enums;

enum AcademicStatus: int
{
    case STATUS_COMPLETED = 1;
    case STATUS_IN_PROGRESS = 2;
    case STATUS_INCOMPLETE = 3;
}