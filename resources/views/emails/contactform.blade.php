<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Contact Enquiry</title>
</head>
<body style="margin: 0; padding: 0; background-color: #eef2f7; font-family: 'Arial', sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.06);">
                    
                    <!-- Header Banner -->
                    <tr style="background-color: #08683d;">
                        <td style="padding: 25px 30px; color: #ffffff;">
                            <h2 style="margin: 0; font-size: 22px;">📨 {{ env('APP_NAME') }} - New Contact Enquiry</h2>
                            {{-- <p style="margin: 5px 0 0; font-size: 14px;">From {{ env('APP_NAME') }}</p> --}}
                        </td>
                    </tr>

                    <!-- Spacer -->
                    <tr>
                        <td style="height: 20px;"></td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <table width="100%" cellpadding="10" cellspacing="0" style="font-size: 15px; color: #333;">
                                <tr>
                                    <td style="font-weight: bold; width: 140px;">👤 Name:</td>
                                    <td>{{ $contact->name }}</td>
                                </tr>
                                <tr style="background-color: #f9fafc;">
                                    <td style="font-weight: bold;">📧 Email:</td>
                                    <td><a href="mailto:{{ $contact->email }}" style="color: #1a73e8;">{{ $contact->email }}</a></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">📞 Phone:</td>
                                    <td>{{ $contact->phone }}</td>
                                </tr>
                                <tr style="background-color: #f9fafc;">
                                    <td style="font-weight: bold;">📝 Subject:</td>
                                    <td>{{ $contact->subject }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; vertical-align: top;">💬 Message:</td>
                                    <td style="white-space: pre-line;">{{ $contact->message }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f1f3f6; text-align: center; padding: 20px; font-size: 12px; color: #7a7a7a;">
                            &copy; {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
