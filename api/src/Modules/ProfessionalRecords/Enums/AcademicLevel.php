<?php

namespace Modules\ProfessionalRecords\Enums;

enum AcademicLevel: int
{
    case LEVEL_PRIMARY = 0;
    case LEVEL_SECONDARY = 1;
    case LEVEL_TECHNICAL = 2;
    case LEVEL_UNIVERSITY = 3;
    case LEVEL_MASTER = 4;
    case LEVEL_DOCTORATE = 5;
}