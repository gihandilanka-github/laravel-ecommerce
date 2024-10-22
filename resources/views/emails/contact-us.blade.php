@extends('emails.layouts.basic')
@section('content')
<tr>
    <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;" valign="top">
        <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
            <tr>
                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">Hi admin,</p>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-top: 15px;">{{$data['message']}}</p>

                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;padding: 0px;Margin-top: 15px;">Thank you.</p>
                    <p style="padding: 0px;margin: 0px;">{{$data['name']}}</p>
                </td>
            </tr>
        </table>
    </td>
</tr>
@endsection