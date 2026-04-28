<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center" style="padding: 32px 24px;">
{{-- Trust signals row --}}
<div class="trust-row" style="margin-bottom: 18px;">
<span style="display: inline-block; margin: 0 8px; font-size: 11px; color: #6b6a66;">🛡️ Verified instructors</span>
<span style="display: inline-block; margin: 0 8px; font-size: 11px; color: #6b6a66;">🔒 Secure payments</span>
<span style="display: inline-block; margin: 0 8px; font-size: 11px; color: #6b6a66;">⭐ Real reviews</span>
</div>

{{-- Brand block --}}
<p style="margin: 0 0 8px; font-size: 14px; font-weight: 700; color: #1f1e1b;">
Secure<span style="background: #ffd500; color: #121110; display: inline-block; padding: 1px 5px; border-radius: 3px; font-weight: 800; margin: 0 1px;">L</span>icences
</p>
<p style="margin: 0 0 16px; font-size: 12px; color: #6b6a66;">
Australia's #1 platform for finding verified driving instructors.
</p>

{{-- Links row --}}
<p style="margin: 0 0 12px; font-size: 12px; color: #6b6a66;">
<a href="{{ url('/') }}" style="color: #6b6a66; text-decoration: none; margin: 0 6px;">Home</a> ·
<a href="{{ url('/find-instructor') }}" style="color: #6b6a66; text-decoration: none; margin: 0 6px;">Find Instructor</a> ·
<a href="{{ url('/contact') }}" style="color: #6b6a66; text-decoration: none; margin: 0 6px;">Support</a> ·
<a href="{{ url('/policies') }}" style="color: #6b6a66; text-decoration: none; margin: 0 6px;">Policies</a>
</p>

{{-- Slot content (legal lines pushed by individual notifications) --}}
{!! Illuminate\Mail\Markdown::parse($slot) !!}

{{-- Copyright --}}
<p style="margin: 16px 0 0; font-size: 11px; color: #9c9b97;">
&copy; {{ date('Y') }} Secure Licences Pty Ltd. All rights reserved.<br>
You received this email because you have an account with Secure Licences. <br>
<a href="{{ url('/contact') }}" style="color: #9c9b97; text-decoration: underline;">Manage email preferences</a>
</p>
</td>
</tr>
</table>
</td>
</tr>
