
@extends('email.app')
@section('content')
<tr>
    <td style="padding:0 35px;">
        <h1 style="color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;">Dear {{$user->full_name}}, </h1>
        <span style="display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;"></span>
        <p style="color:#455056; font-size:15px;line-height:24px; margin:0;">
            A new task is assigned to you. Assigned by {{$task->assignedBy->name}}
        </p>
    </td>
</tr>
@endsection