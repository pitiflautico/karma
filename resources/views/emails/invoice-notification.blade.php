@component('mail::message')

# 📄 Invoice {{ $invoice->invoice_number }}

@if($customMessage)
{{ $customMessage }}

---
@endif

We've generated your invoice and it's ready for your review.

The invoice PDF is attached to this email for your records.

Thank you for your business!

---

**Questions?** Reply to this email or contact us:
@php 
$settings = App\Models\Setting::first(); 
@endphp
- Email: {{ $settings->company_email ?? 'hello@cloudstudio.es' }}
- Phone: {{ $settings->company_phone ?? '' }}

This is an automated invoice notification from your cloudstudio billing system.

Best regards,<br>
{{ $invoice->organization->name ?? 'cloudstudio' }}
@endcomponent