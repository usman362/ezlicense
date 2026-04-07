{{-- Event Complete / Review Request Email --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How was your booking?</title>
    <style>
        /* Reset */
        body, table, td, p, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; margin: 0; padding: 0; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333333;
            line-height: 1.5;
        }
    </style>
</head>
<body style="background-color: #f5f5f5; margin: 0; padding: 0;">

<!-- Wrapper Table -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5;">
    <tr>
        <td align="center" style="padding: 24px 16px;">

            <!-- Main Container -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 480px; background-color: #ffffff; border-radius: 12px; overflow: hidden;">

                <!-- Logo Header -->
                <tr>
                    <td align="center" style="padding: 32px 24px 16px; background-color: #f8f9fa;">
                        <table role="presentation" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="font-size: 28px; font-weight: 700; color: #333333; letter-spacing: -0.5px;">
                                    Ez<span style="background-color: #f0ad4e; color: #ffffff; padding: 1px 4px; border-radius: 4px; font-weight: 800;">L</span>icence
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Event Complete Title -->
                <tr>
                    <td align="center" style="padding: 16px 24px 8px;">
                        <span style="color: #28a745; font-size: 18px; font-weight: 600;">Event Complete</span>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding: 0 24px 24px;">
                        <span style="color: #333333; font-size: 20px; font-weight: 700;">How was your booking?</span>
                    </td>
                </tr>

                <!-- Lesson Details Card -->
                <tr>
                    <td style="padding: 0 24px 16px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #e9ecef; border-radius: 10px; overflow: hidden;">
                            <!-- Card Header -->
                            <tr>
                                <td style="padding: 14px 16px; border-bottom: 1px solid #e9ecef;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="font-size: 15px; font-weight: 600; color: #333333;">Lesson details</td>
                                            <td align="right">
                                                <span style="background-color: #28a745; color: #ffffff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px;">COMPLETE</span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <!-- Card Body -->
                            <tr>
                                <td style="padding: 16px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <!-- Date -->
                                        <tr>
                                            <td style="padding: 4px 0; font-size: 14px; color: #555555;">
                                                <span style="display: inline-block; width: 24px; text-align: center; margin-right: 8px;">&#128197;</span>
                                                {{ $lessonDate }}
                                            </td>
                                        </tr>
                                        <!-- Time -->
                                        <tr>
                                            <td style="padding: 4px 0; font-size: 14px; color: #555555;">
                                                <span style="display: inline-block; width: 24px; text-align: center; margin-right: 8px;">&#128339;</span>
                                                {{ $lessonTime }}
                                            </td>
                                        </tr>
                                        <!-- Lesson Type -->
                                        <tr>
                                            <td style="padding: 4px 0; font-size: 14px; color: #555555;">
                                                <span style="display: inline-block; width: 24px; text-align: center; margin-right: 8px;">&#128663;</span>
                                                {{ $lessonType }}
                                            </td>
                                        </tr>
                                        <!-- Location -->
                                        @if($lessonLocation)
                                        <tr>
                                            <td style="padding: 4px 0; font-size: 14px; color: #555555;">
                                                <span style="display: inline-block; width: 24px; text-align: center; margin-right: 8px;">&#128205;</span>
                                                <a href="https://maps.google.com/?q={{ urlencode($lessonLocation) }}" style="color: #0d6efd; text-decoration: underline;">{{ $lessonLocation }}</a>
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Rate Your Experience Button -->
                <tr>
                    <td align="center" style="padding: 8px 24px 24px;">
                        <a href="{{ $reviewUrl }}" style="display: inline-block; background-color: #f0ad4e; color: #ffffff; font-size: 16px; font-weight: 700; text-decoration: none; padding: 14px 48px; border-radius: 8px;">
                            Rate your experience
                        </a>
                    </td>
                </tr>

                <!-- Benefits Section -->
                <tr>
                    <td style="padding: 0 24px 16px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #e9ecef; border-radius: 10px; overflow: hidden;">
                            <tr>
                                <td style="padding: 16px;">
                                    <p style="font-size: 15px; font-weight: 700; color: #333333; margin: 0 0 8px;">Benefits of remaining on the platform</p>
                                    <p style="font-size: 13px; color: #666666; margin: 0 0 12px;">Booking through EzLicence ensures you receive a consistent, high-quality lesson experience.</p>
                                    <table role="presentation" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding: 3px 0; font-size: 13px; color: #555555;">
                                                &bull;&nbsp; 100% verified and accredited Driving Instructors
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 3px 0; font-size: 13px; color: #555555;">
                                                &bull;&nbsp; Book online 24/7
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 3px 0; font-size: 13px; color: #555555;">
                                                &bull;&nbsp; Switch Instructor anytime for free
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- CTA Buttons Row -->
                <tr>
                    <td style="padding: 0 24px 24px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="48%" align="center" style="padding-right: 6px;">
                                    <a href="{{ $newBookingUrl }}" style="display: block; border: 1px solid #dee2e6; border-radius: 8px; padding: 14px 8px; text-decoration: none; text-align: center;">
                                        <span style="display: block; font-size: 18px; margin-bottom: 4px;">&#128197;</span>
                                        <span style="font-size: 13px; font-weight: 600; color: #333333;">Make a new<br>booking</span>
                                    </a>
                                </td>
                                <td width="48%" align="center" style="padding-left: 6px;">
                                    <a href="{{ $findInstructorUrl }}" style="display: block; border: 1px solid #dee2e6; border-radius: 8px; padding: 14px 8px; text-decoration: none; text-align: center;">
                                        <span style="display: block; font-size: 18px; margin-bottom: 4px;">&#128269;</span>
                                        <span style="font-size: 13px; font-weight: 600; color: #333333;">Find another<br>instructor</span>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Contact Support -->
                <tr>
                    <td align="center" style="padding: 0 24px 24px;">
                        <span style="font-size: 13px; color: #999999;">Issue with this booking? <a href="{{ $supportUrl }}" style="color: #666666; text-decoration: underline;">Contact Support</a></span>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="padding: 24px; background-color: #f8f9fa; border-top: 1px solid #e9ecef;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="font-size: 20px; font-weight: 700; color: #333333;">
                                    Ez<span style="background-color: #f0ad4e; color: #ffffff; padding: 1px 3px; border-radius: 3px; font-weight: 800;">L</span>icence
                                </td>
                                <td align="right">
                                    <!-- Social Icons -->
                                    <a href="{{ $facebookUrl }}" style="display: inline-block; width: 28px; height: 28px; background-color: #333333; border-radius: 50%; text-align: center; line-height: 28px; text-decoration: none; margin-left: 8px;">
                                        <span style="color: #ffffff; font-size: 14px; font-weight: bold;">f</span>
                                    </a>
                                    <a href="{{ $instagramUrl }}" style="display: inline-block; width: 28px; height: 28px; background-color: #333333; border-radius: 50%; text-align: center; line-height: 28px; text-decoration: none; margin-left: 8px;">
                                        <span style="color: #ffffff; font-size: 14px;">&#9675;</span>
                                    </a>
                                    <a href="{{ $websiteUrl }}" style="display: inline-block; width: 28px; height: 28px; background-color: #333333; border-radius: 50%; text-align: center; line-height: 28px; text-decoration: none; margin-left: 8px;">
                                        <span style="color: #ffffff; font-size: 14px;">&#128279;</span>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top: 12px; font-size: 11px; color: #999999;">
                                    &copy; Copyright &copy; {{ date('Y') }} EzLicence Pty Ltd. All rights reserved.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
            <!-- End Main Container -->

        </td>
    </tr>
</table>
<!-- End Wrapper -->

</body>
</html>
