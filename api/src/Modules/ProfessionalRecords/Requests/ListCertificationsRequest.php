<?php

namespace Modules\ProfessionalRecords\Requests;

use Modules\Auth\Shared\Requests\Template;
use Override;

class ListCertificationsRequest extends Template
{
    #[Override]
    public function authorize(): bool
    {
        return $this->verifyPermission('p.postulante');
    }
}