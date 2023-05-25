<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="widtd=device-widtd, initial-scale=1">
        <title></title>
        <style>
            #tdizq {
                text-align:left;
            }
            #tdder{
                text-align:right;
            }
            #tdcen{
                text-align:center;
            }
            table,td {
                /* border: 1px solid black; */
            }
            body{
                background-image: url("../public/images/logo2.png");
                background-repeat: no-repeat;
                /* background-size: 180px 150px auto; */
                background-position: right top;
            }
        </style>
    </head>
    <body>
        <table style="width:100%">
            <tr>
                <td id="tdcen" colspan="3">
                    <h5><b>{{ $exam->name }}</b></h5>
                </td>
            </tr>
            <tr>
                <td id="tdizq" style="width:50%"><h5><b>Profesor:</b> {{ $exam->teacher->user->name }}</h5></td>
                <td id="tdizq" style="width:40%"><h5><b>Grupo:</b> {{ $exam->group->group }}</h5></td>
                <td style="width:10%"></td>
            </tr>
        </table>
        <table style="width:100%">
            <tr>
                <td id="tdizq" style="width:15%">
                    <h5><b>Indicaciones:</b></h5>
                </td>
                <td id="tdizq" style="width:90%">
                    {!! $exam->indications !!}
                </td>
            </tr>
        </table>
        <hr>
        <table style="width:100%">
            @foreach($instructions as $instruction)
                <tr>
                    <td><b><i>{{ $instruction->topic->topic }}</i></b></td>
                </tr>
                <tr>
                    <td>
                        {!! $instruction->instruction !!}
                    </td>
                </tr>
                <tr>
                    <ul>
                        @foreach($exam->questions as $question)
                            @if($question->instruction_id == $instruction->id)
                                @if($instruction->categorie_id !== 3)
                                    @if($question->type_id == 1)
                                        @if(!strpos($question->question, '@@@@'))
                                            <li>
                                                {!! $question->question !!}
                                                <p><b>answer:</b> __________________________________________________</p>
                                            </li>
                                        @else
                                        <li>{!! str_replace("@@@@", "__________", $question->question) !!}</li>
                                        @endif
                                    @endif
                                    @if($question->type_id == 2)
                                    <li>
                                        {!! str_replace("@@@@", "__________", $question->question) !!}
                                        <ul>
                                            @foreach($question->answers as $answer)
                                                <li>{{ $answer->answer }}</li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    @endif
                                    @if($question->type_id == 3)
                                        <li>
                                            {!! $question->question !!}
                                            <ul>
                                                @foreach($question->answers as $answer)
                                                    <li>{{ $answer->answer }}</li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endif
                                @else
                                    {!! $question->question !!}
                                @endif
                            @endif
                        @endforeach
                    </ul>
                </tr>
            @endforeach
        </table>
    </body>
</html>
