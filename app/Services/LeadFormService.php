<?php

namespace App\Services;

use App\Models\LeadForm;
use App\Models\LeadSubmission;
use Illuminate\Support\Str;

class LeadFormService
{
    public function getAllForUser(int $userId, int $perPage = 15)
    {
        return LeadForm::where('user_id', $userId)
            ->withCount('submissions')
            ->latest()
            ->paginate($perPage);
    }

    public function create(int $userId, array $data): LeadForm
    {
        $data['user_id'] = $userId;
        $data['slug']    = Str::slug($data['name']) . '-' . Str::random(6);

        return LeadForm::create($data);
    }

    public function update(LeadForm $form, array $data): LeadForm
    {
        $form->update($data);
        return $form->fresh();
    }

    public function delete(LeadForm $form): bool
    {
        return $form->delete();
    }

    public function submit(LeadForm $form, array $data, string $ip, string $userAgent, ?string $referrer): LeadSubmission
    {
        return LeadSubmission::create([
            'form_id'    => $form->id,
            'data'       => $data,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'referrer'   => $referrer,
        ]);
    }

    public function getEmbedCode(LeadForm $form): string
    {
        $url = config('app.url') . "/lead-forms/{$form->slug}/embed";
        return "<script src=\"{$url}\" async></script>";
    }
}
