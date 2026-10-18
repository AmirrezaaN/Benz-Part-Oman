<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#040711;font-family:Tahoma,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#040711;padding:32px 12px;">
    <tr><td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background:#0b1329;border:1px solid #1e293b;border-radius:16px;overflow:hidden;">
            <tr><td style="padding:24px;text-align:center;border-bottom:1px solid #1e293b;">
                <div style="font-size:20px;font-weight:bold;color:#ffffff;">بنز پارت <span style="color:#38bdf8;">عمان</span></div>
                <div style="font-size:10px;color:#64748b;letter-spacing:3px;margin-top:4px;">BENZ PART OMAN</div>
            </td></tr>
            <tr><td style="padding:28px 24px;text-align:center;color:#cbd5e1;font-size:14px;line-height:28px;">
                کد تایید شما برای <strong style="color:#fff;">{{ $purposeLabel }}</strong>:
                <div dir="ltr" style="margin:20px auto;font-size:32px;font-weight:bold;letter-spacing:10px;color:#38bdf8;background:#040711;border:1px solid #1e293b;border-radius:12px;padding:14px 0;max-width:260px;">{{ $code }}</div>
                این کد تا {{ $minutes }} دقیقه معتبر است.<br>
                <span style="color:#64748b;font-size:12px;">اگر شما این درخواست را نداده‌اید، این ایمیل را نادیده بگیرید. این کد را در اختیار هیچ‌کس قرار ندهید.</span>
            </td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
